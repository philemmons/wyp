<?php

declare(strict_types=1);

/**
 * Temporary diagnostics endpoint.
 * Remove this file after troubleshooting.
 *
 * Usage:
 * /diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY
 * /diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY&format=json
 * /diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY&domain=example.com&selector=default
 * /diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY&smtp_host=smtp.example.com
 * /diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY&mail_test_to=you@example.com
 * /diagnostics/mail_delivery_diagnostic.php?key=YOUR_KEY&outbound_ip=203.0.113.10
 */

$bootstrapFilePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'init.php';
if (is_file($bootstrapFilePath)) {
    require_once $bootstrapFilePath;
}

$resolveEnvironmentValue = static function (string $key, string $default = ''): string {
    if (function_exists('wyp_env')) {
        return wyp_env($key, $default);
    }

    $value = getenv($key);
    if ($value === false) {
        return $default;
    }

    return trim((string) $value);
};

$configuredDiagnosticAccessKey = $resolveEnvironmentValue('WYP_DIAG_KEY');
$requestedDiagnosticAccessKey = isset($_GET['key']) && is_string($_GET['key']) ? $_GET['key'] : '';
$shouldReturnJson = (isset($_GET['format']) && $_GET['format'] === 'json');

if ($configuredDiagnosticAccessKey === '') {
    http_response_code(403);
    $message = [
        'ok' => false,
        'error' => 'WYP_DIAG_KEY is not set on the server.',
        'hint' => 'Set an environment variable WYP_DIAG_KEY and pass it as ?key=...',
    ];
    if ($shouldReturnJson) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Access denied: WYP_DIAG_KEY is not set.\n";
    exit;
}

if ($requestedDiagnosticAccessKey === '' || !hash_equals($configuredDiagnosticAccessKey, $requestedDiagnosticAccessKey)) {
    http_response_code(403);
    if ($shouldReturnJson) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'error' => 'Invalid key'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Access denied: invalid key.\n";
    exit;
}

$recipientEmailAddress = $resolveEnvironmentValue('WYP_EMAIL');
if ($recipientEmailAddress === '') {
    $recipientEmailAddress = $resolveEnvironmentValue('CONTACT_RECIPIENT_EMAIL');
}

$configuredFromAddress = $resolveEnvironmentValue('WYP_FORM_FROM_EMAIL');
$fromEmailAddress = filter_var($configuredFromAddress, FILTER_VALIDATE_EMAIL)
    ? $configuredFromAddress
    : (filter_var($recipientEmailAddress, FILTER_VALIDATE_EMAIL) ? $recipientEmailAddress : 'noreply@example.com');

$defaultSmtpHost = $resolveEnvironmentValue('WYP_SMTP_HOST');
$defaultSmtpPort = (int) $resolveEnvironmentValue('WYP_SMTP_PORT', '587');
if ($defaultSmtpPort <= 0) {
    $defaultSmtpPort = 587;
}
$defaultDkimSelector = $resolveEnvironmentValue('WYP_DKIM_SELECTOR', 'default');

$fromEmailDomain = strtolower((string) substr(strrchr($fromEmailAddress, '@') ?: '', 1));
$domain = isset($_GET['domain']) && is_string($_GET['domain']) && $_GET['domain'] !== ''
    ? strtolower(trim($_GET['domain']))
    : ($fromEmailDomain !== '' ? $fromEmailDomain : 'example.com');

$selector = isset($_GET['selector']) && is_string($_GET['selector']) && $_GET['selector'] !== ''
    ? trim($_GET['selector'])
    : ($defaultDkimSelector !== '' ? $defaultDkimSelector : 'default');

$smtpHost = isset($_GET['smtp_host']) && is_string($_GET['smtp_host']) && $_GET['smtp_host'] !== ''
    ? trim($_GET['smtp_host'])
    : $defaultSmtpHost;

$smtpPortsToTest = [25, 465, 587, 2525];

$disabledPhpFunctions = array_filter(array_map(
    static fn (string $v): string => trim($v),
    explode(',', (string) ini_get('disable_functions'))
));

$isMailFunctionAvailable = function_exists('mail');
$isMailFunctionDisabled = in_array('mail', $disabledPhpFunctions, true);

$testSmtpPortConnectivity = static function (string $host, int $port): array {
    if ($host === '') {
        return [
            'host' => $host,
            'port' => $port,
            'ok' => false,
            'error' => 'SMTP host not set',
            'latency_ms' => null,
        ];
    }

    $errno = 0;
    $errstr = '';
    $timeout = 8;
    $start = microtime(true);
    $stream = @fsockopen($host, $port, $errno, $errstr, $timeout);
    $latency = (int) round((microtime(true) - $start) * 1000);

    if (!is_resource($stream)) {
        return [
            'host' => $host,
            'port' => $port,
            'ok' => false,
            'error' => $errstr !== '' ? $errstr : ('errno_' . $errno),
            'errno' => $errno,
            'latency_ms' => $latency,
        ];
    }

    stream_set_timeout($stream, 3);
    $banner = fgets($stream, 512);
    fclose($stream);

    return [
        'host' => $host,
        'port' => $port,
        'ok' => true,
        'latency_ms' => $latency,
        'banner' => is_string($banner) ? trim($banner) : '',
    ];
};

$lookupDnsTxtValues = static function (string $name): array {
    $records = dns_get_record($name, DNS_TXT);
    if (!is_array($records)) {
        return [];
    }

    $values = [];
    foreach ($records as $record) {
        if (isset($record['txt']) && is_string($record['txt'])) {
            $values[] = $record['txt'];
        }
    }
    return $values;
};

$spfTxtRecords = array_values(array_filter(
    $lookupDnsTxtValues($domain),
    static fn (string $txt): bool => stripos($txt, 'v=spf1') === 0
));

$dmarcTxtRecords = array_values(array_filter(
    $lookupDnsTxtValues('_dmarc.' . $domain),
    static fn (string $txt): bool => stripos($txt, 'v=DMARC1') === 0
));

$dkimTxtRecords = $lookupDnsTxtValues($selector . '._domainkey.' . $domain);

$reverseDnsStatus = ['checked' => false];
$outboundIpAddress = isset($_GET['outbound_ip']) && is_string($_GET['outbound_ip']) ? trim($_GET['outbound_ip']) : '';
if ($outboundIpAddress !== '' && filter_var($outboundIpAddress, FILTER_VALIDATE_IP)) {
    $ptr = gethostbyaddr($outboundIpAddress);
    $reverseDnsStatus = [
        'checked' => true,
        'ip' => $outboundIpAddress,
        'ptr' => $ptr,
        'ok' => $ptr !== $outboundIpAddress && $ptr !== false,
    ];
}

$mailFunctionTestResult = ['attempted' => false];
$mailFunctionTestResultRecipient = isset($_GET['mail_test_to']) && is_string($_GET['mail_test_to']) ? trim($_GET['mail_test_to']) : '';
if ($mailFunctionTestResultRecipient !== '') {
    $mailFunctionTestResult['attempted'] = true;
    if (!filter_var($mailFunctionTestResultRecipient, FILTER_VALIDATE_EMAIL)) {
        $mailFunctionTestResult['ok'] = false;
        $mailFunctionTestResult['error'] = 'Invalid mail_test_to email format';
    } elseif (!$isMailFunctionAvailable || $isMailFunctionDisabled) {
        $mailFunctionTestResult['ok'] = false;
        $mailFunctionTestResult['error'] = 'mail() unavailable or disabled';
    } else {
        $subject = 'WYP diagnostic mail() test ' . gmdate('c');
        $body = "This is a diagnostic mail() test.\r\n"
            . "Server: " . gethostname() . "\r\n"
            . "Time: " . gmdate('c') . "\r\n";
        $headers = [
            'From: WYP Diagnostics <' . $fromEmailAddress . '>',
            'Reply-To: ' . $fromEmailAddress,
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'X-Diagnostic: mail_delivery_diagnostic.php',
        ];
        $headerBlob = implode("\r\n", $headers);

        $errorBeforeSend = error_get_last();
        $ok = @mail($mailFunctionTestResultRecipient, $subject, $body, $headerBlob, '-f' . $fromEmailAddress);
        $errorAfterSend = error_get_last();
        $warning = '';
        if ($errorAfterSend !== $errorBeforeSend && isset($errorAfterSend['message']) && is_string($errorAfterSend['message'])) {
            $warning = $errorAfterSend['message'];
        }
        $mailFunctionTestResult['ok'] = $ok;
        $mailFunctionTestResult['warning'] = $warning;
    }
}

$smtpPortTestResults = [];
foreach ($smtpPortsToTest as $port) {
    $smtpPortTestResults[] = $testSmtpPortConnectivity($smtpHost, $port);
}

$mailDiagnosticReport = [
    'generated_at_utc' => gmdate('c'),
    'server' => [
        'hostname' => gethostname(),
        'php_version' => PHP_VERSION,
        'sapi' => PHP_SAPI,
        'software' => $_SERVER['SERVER_SOFTWARE'] ?? '',
    ],
    'php_mail' => [
        'mail_function_exists' => $isMailFunctionAvailable,
        'mail_disabled' => $isMailFunctionDisabled,
        'sendmail_path' => ini_get('sendmail_path'),
        'smtp_ini_host' => ini_get('SMTP'),
        'smtp_ini_port' => ini_get('smtp_port'),
    ],
    'dns' => [
        'domain' => $domain,
        'from_email' => $fromEmailAddress,
        'from_domain' => $fromEmailDomain,
        'spf_records' => $spfTxtRecords,
        'dkim_query' => $selector . '._domainkey.' . $domain,
        'dkim_records' => $dkimTxtRecords,
        'dmarc_records' => $dmarcTxtRecords,
    ],
    'alignment_checks' => [
        'from_domain_matches_test_domain' => ($fromEmailDomain !== '' && $fromEmailDomain === $domain),
        'has_spf' => !empty($spfTxtRecords),
        'has_dkim_selector_record' => !empty($dkimTxtRecords),
        'has_dmarc' => !empty($dmarcTxtRecords),
    ],
    'smtp_connectivity' => [
        'smtp_host' => $smtpHost,
        'configured_smtp_port' => $defaultSmtpPort,
        'port_tests' => $smtpPortTestResults,
    ],
    'mail_routing' => [
        'contact_recipient_email' => $recipientEmailAddress,
        'configured_from_email' => $configuredFromAddress,
        'resolved_from_email' => $fromEmailAddress,
    ],
    'reverse_dns' => $reverseDnsStatus,
    'mail_test' => $mailFunctionTestResult,
    'manual_checks' => [
        'mailbox_quota' => 'Check cPanel -> Email Accounts -> Storage usage and quota for sending mailbox.',
        'spam_filtering' => 'Check recipient spam/junk folders and cPanel Track Delivery logs.',
        'throttling' => 'Check host outbound mail limits, hourly caps, and Exim deferrals with host support.',
        'tls' => 'Use diagnostics/smtp_delivery_test.php for SMTP handshake/auth/tls debugging.',
    ],
];

if ($shouldReturnJson) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($mailDiagnosticReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Mail Diagnostics</title>';
echo '<style>body{font-family:Arial,sans-serif;max-width:980px;margin:24px auto;padding:0 12px}pre{background:#f5f5f5;padding:12px;border-radius:8px;overflow:auto}h1,h2{margin-bottom:8px}</style>';
echo '</head><body>';
echo '<h1>Mail Diagnostics</h1>';
echo '<p>Temporary endpoint. Remove after troubleshooting.</p>';
echo '<h2>Summary</h2>';
echo '<pre>' . htmlspecialchars(json_encode($mailDiagnosticReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') . '</pre>';
echo '</body></html>';



