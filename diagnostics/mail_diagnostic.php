<?php

declare(strict_types=1);

/**
 * Temporary diagnostics endpoint.
 * Remove this file after troubleshooting.
 *
 * Usage:
 * /diagnostics/mail_diagnostic.php?key=YOUR_KEY
 * /diagnostics/mail_diagnostic.php?key=YOUR_KEY&format=json
 * /diagnostics/mail_diagnostic.php?key=YOUR_KEY&domain=example.com&selector=default
 * /diagnostics/mail_diagnostic.php?key=YOUR_KEY&smtp_host=smtp.example.com
 * /diagnostics/mail_diagnostic.php?key=YOUR_KEY&mail_test_to=you@example.com
 * /diagnostics/mail_diagnostic.php?key=YOUR_KEY&outbound_ip=203.0.113.10
 */

$expected_key = (string) getenv('WYP_DIAG_KEY');
$provided_key = isset($_GET['key']) && is_string($_GET['key']) ? $_GET['key'] : '';
$json_mode = (isset($_GET['format']) && $_GET['format'] === 'json');

if ($expected_key === '') {
    http_response_code(403);
    $message = [
        'ok' => false,
        'error' => 'WYP_DIAG_KEY is not set on the server.',
        'hint' => 'Set an environment variable WYP_DIAG_KEY and pass it as ?key=...',
    ];
    if ($json_mode) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Access denied: WYP_DIAG_KEY is not set.\n";
    exit;
}

if ($provided_key === '' || !hash_equals($expected_key, $provided_key)) {
    http_response_code(403);
    if ($json_mode) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'error' => 'Invalid key'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Access denied: invalid key.\n";
    exit;
}

$config_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'contact_mail.php';
$config = is_file($config_path) ? require $config_path : [];

$from_email = (string) ($config['site']['from_email'] ?? 'noreply@example.com');
$smtp_host_default = (string) ($config['smtp']['host'] ?? '');
$smtp_port_default = (int) ($config['smtp']['port'] ?? 587);
$dkim_selector_default = (string) ($config['dkim']['selector'] ?? 'default');

$from_domain = strtolower((string) substr(strrchr($from_email, '@') ?: '', 1));
$domain = isset($_GET['domain']) && is_string($_GET['domain']) && $_GET['domain'] !== ''
    ? strtolower(trim($_GET['domain']))
    : ($from_domain !== '' ? $from_domain : 'example.com');

$selector = isset($_GET['selector']) && is_string($_GET['selector']) && $_GET['selector'] !== ''
    ? trim($_GET['selector'])
    : ($dkim_selector_default !== '' ? $dkim_selector_default : 'default');

$smtp_host = isset($_GET['smtp_host']) && is_string($_GET['smtp_host']) && $_GET['smtp_host'] !== ''
    ? trim($_GET['smtp_host'])
    : $smtp_host_default;

$smtp_ports = [25, 465, 587, 2525];

$disabled_functions = array_filter(array_map(
    static fn (string $v): string => trim($v),
    explode(',', (string) ini_get('disable_functions'))
));

$has_mail_fn = function_exists('mail');
$mail_disabled = in_array('mail', $disabled_functions, true);

$port_test = static function (string $host, int $port): array {
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

$dns_txt = static function (string $name): array {
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

$spf_records = array_values(array_filter(
    $dns_txt($domain),
    static fn (string $txt): bool => stripos($txt, 'v=spf1') === 0
));

$dmarc_records = array_values(array_filter(
    $dns_txt('_dmarc.' . $domain),
    static fn (string $txt): bool => stripos($txt, 'v=DMARC1') === 0
));

$dkim_records = $dns_txt($selector . '._domainkey.' . $domain);

$rdns_check = ['checked' => false];
$outbound_ip = isset($_GET['outbound_ip']) && is_string($_GET['outbound_ip']) ? trim($_GET['outbound_ip']) : '';
if ($outbound_ip !== '' && filter_var($outbound_ip, FILTER_VALIDATE_IP)) {
    $ptr = gethostbyaddr($outbound_ip);
    $rdns_check = [
        'checked' => true,
        'ip' => $outbound_ip,
        'ptr' => $ptr,
        'ok' => $ptr !== $outbound_ip && $ptr !== false,
    ];
}

$mail_test = ['attempted' => false];
$mail_test_to = isset($_GET['mail_test_to']) && is_string($_GET['mail_test_to']) ? trim($_GET['mail_test_to']) : '';
if ($mail_test_to !== '') {
    $mail_test['attempted'] = true;
    if (!filter_var($mail_test_to, FILTER_VALIDATE_EMAIL)) {
        $mail_test['ok'] = false;
        $mail_test['error'] = 'Invalid mail_test_to email format';
    } elseif (!$has_mail_fn || $mail_disabled) {
        $mail_test['ok'] = false;
        $mail_test['error'] = 'mail() unavailable or disabled';
    } else {
        $subject = 'WYP diagnostic mail() test ' . gmdate('c');
        $body = "This is a diagnostic mail() test.\r\n"
            . "Server: " . gethostname() . "\r\n"
            . "Time: " . gmdate('c') . "\r\n";
        $headers = [
            'From: WYP Diagnostics <' . $from_email . '>',
            'Reply-To: ' . $from_email,
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'X-Diagnostic: mail_diagnostic.php',
        ];
        $header_blob = implode("\r\n", $headers);

        $error_before = error_get_last();
        $ok = @mail($mail_test_to, $subject, $body, $header_blob, '-f' . $from_email);
        $error_after = error_get_last();
        $warning = '';
        if ($error_after !== $error_before && isset($error_after['message']) && is_string($error_after['message'])) {
            $warning = $error_after['message'];
        }
        $mail_test['ok'] = $ok;
        $mail_test['warning'] = $warning;
    }
}

$smtp_port_results = [];
foreach ($smtp_ports as $port) {
    $smtp_port_results[] = $port_test($smtp_host, $port);
}

$results = [
    'generated_at_utc' => gmdate('c'),
    'server' => [
        'hostname' => gethostname(),
        'php_version' => PHP_VERSION,
        'sapi' => PHP_SAPI,
        'software' => $_SERVER['SERVER_SOFTWARE'] ?? '',
    ],
    'php_mail' => [
        'mail_function_exists' => $has_mail_fn,
        'mail_disabled' => $mail_disabled,
        'sendmail_path' => ini_get('sendmail_path'),
        'smtp_ini_host' => ini_get('SMTP'),
        'smtp_ini_port' => ini_get('smtp_port'),
    ],
    'dns' => [
        'domain' => $domain,
        'from_email' => $from_email,
        'from_domain' => $from_domain,
        'spf_records' => $spf_records,
        'dkim_query' => $selector . '._domainkey.' . $domain,
        'dkim_records' => $dkim_records,
        'dmarc_records' => $dmarc_records,
    ],
    'alignment_checks' => [
        'from_domain_matches_test_domain' => ($from_domain !== '' && $from_domain === $domain),
        'has_spf' => !empty($spf_records),
        'has_dkim_selector_record' => !empty($dkim_records),
        'has_dmarc' => !empty($dmarc_records),
    ],
    'smtp_connectivity' => [
        'smtp_host' => $smtp_host,
        'configured_smtp_port' => $smtp_port_default,
        'port_tests' => $smtp_port_results,
    ],
    'reverse_dns' => $rdns_check,
    'mail_test' => $mail_test,
    'manual_checks' => [
        'mailbox_quota' => 'Check cPanel -> Email Accounts -> Storage usage and quota for sending mailbox.',
        'spam_filtering' => 'Check recipient spam/junk folders and cPanel Track Delivery logs.',
        'throttling' => 'Check host outbound mail limits, hourly caps, and Exim deferrals with host support.',
        'tls' => 'Use diagnostics/smtp_test.php for SMTP handshake/auth/tls debugging.',
    ],
];

if ($json_mode) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Mail Diagnostics</title>';
echo '<style>body{font-family:Arial,sans-serif;max-width:980px;margin:24px auto;padding:0 12px}pre{background:#f5f5f5;padding:12px;border-radius:8px;overflow:auto}h1,h2{margin-bottom:8px}</style>';
echo '</head><body>';
echo '<h1>Mail Diagnostics</h1>';
echo '<p>Temporary endpoint. Remove after troubleshooting.</p>';
echo '<h2>Summary</h2>';
echo '<pre>' . htmlspecialchars(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') . '</pre>';
echo '</body></html>';
