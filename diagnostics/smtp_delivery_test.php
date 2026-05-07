<?php

declare(strict_types=1);

/**
 * Temporary SMTP test route.
 * Remove after troubleshooting.
 *
 * Usage:
 * /diagnostics/smtp_delivery_test.php?key=YOUR_KEY
 * /diagnostics/smtp_delivery_test.php?key=YOUR_KEY&to=you@example.com&send=1
 * /diagnostics/smtp_delivery_test.php?key=YOUR_KEY&format=json
 *
 * Requires:
 * - WYP_ENABLE_DIAGNOSTICS=1
 * - WYP_DIAG_KEY=... (shared secret)
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

$resolveEnvironmentFlag = static function (string $key, bool $default) use ($resolveEnvironmentValue): bool {
    $value = strtolower($resolveEnvironmentValue($key, $default ? '1' : '0'));
    return in_array($value, ['1', 'true', 'yes', 'on'], true);
};

$configuredDiagnosticAccessKey = $resolveEnvironmentValue('WYP_DIAG_KEY');
$requestedDiagnosticAccessKey = isset($_GET['key']) && is_string($_GET['key']) ? $_GET['key'] : '';
$shouldReturnJson = (isset($_GET['format']) && $_GET['format'] === 'json');
$diagnosticsEnabled = $resolveEnvironmentFlag('WYP_ENABLE_DIAGNOSTICS', false);
$diagnosticsVerboseEnabled = $resolveEnvironmentFlag('WYP_DIAG_VERBOSE', false);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('X-Robots-Tag: noindex, nofollow, noarchive');
header('Referrer-Policy: no-referrer');

if (!$diagnosticsEnabled) {
    http_response_code(404);
    if ($shouldReturnJson) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'error' => 'Not found'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Not found.\n";
    exit;
}

if ($configuredDiagnosticAccessKey === '' || $requestedDiagnosticAccessKey === '' || !hash_equals($configuredDiagnosticAccessKey, $requestedDiagnosticAccessKey)) {
    http_response_code(403);
    $message = [
        'ok' => false,
        'error' => $configuredDiagnosticAccessKey === '' ? 'WYP_DIAG_KEY is not set' : 'Invalid key',
    ];
    if ($shouldReturnJson) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Access denied.\n";
    exit;
}

$loadPhpMailerDependencies = static function (): array {
    $class = 'PHPMailer\\PHPMailer\\PHPMailer';
    if (class_exists($class)) {
        return ['ok' => true, 'reason' => 'already_loaded'];
    }

    $autoload = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    if (is_file($autoload)) {
        require_once $autoload;
        if (class_exists($class)) {
            return ['ok' => true, 'reason' => 'composer_autoload'];
        }
    }

    $base = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
    $files = ['Exception.php', 'PHPMailer.php', 'SMTP.php'];
    foreach ($files as $file) {
        if (!is_file($base . $file)) {
            return ['ok' => false, 'reason' => 'phpmailer_not_found'];
        }
    }
    require_once $base . 'Exception.php';
    require_once $base . 'PHPMailer.php';
    require_once $base . 'SMTP.php';
    return class_exists($class)
        ? ['ok' => true, 'reason' => 'manual_include']
        : ['ok' => false, 'reason' => 'load_failed'];
};

$siteDisplayName = 'Wipe Your Paws';
$adminRecipientEmail = $resolveEnvironmentValue('WYP_EMAIL');
if ($adminRecipientEmail === '') {
    $adminRecipientEmail = $resolveEnvironmentValue('CONTACT_RECIPIENT_EMAIL');
}
$configuredFromAddress = $resolveEnvironmentValue('WYP_FORM_FROM_EMAIL');
$fromEmailAddress = filter_var($configuredFromAddress, FILTER_VALIDATE_EMAIL)
    ? $configuredFromAddress
    : (filter_var($adminRecipientEmail, FILTER_VALIDATE_EMAIL) ? $adminRecipientEmail : 'noreply@example.com');
$defaultSmtpPort = (int) $resolveEnvironmentValue('WYP_SMTP_PORT', '587');
if ($defaultSmtpPort <= 0) {
    $defaultSmtpPort = 587;
}
$defaultSmtpTimeout = (int) $resolveEnvironmentValue('WYP_SMTP_TIMEOUT', '15');
if ($defaultSmtpTimeout < 3) {
    $defaultSmtpTimeout = 15;
}

$sanitizeHostname = static function (string $host): string {
    $candidate = trim($host);
    if ($candidate === '') {
        return '';
    }

    return preg_match('/^[A-Za-z0-9.-]+$/', $candidate) ? $candidate : '';
};

$requestedHostOverride = isset($_GET['host']) && is_string($_GET['host']) ? trim($_GET['host']) : '';
$resolvedSmtpHost = $requestedHostOverride !== ''
    ? $sanitizeHostname($requestedHostOverride)
    : $sanitizeHostname($resolveEnvironmentValue('WYP_SMTP_HOST'));

$resolvedSmtpPort = isset($_GET['port']) ? (int) $_GET['port'] : $defaultSmtpPort;
if ($resolvedSmtpPort < 1 || $resolvedSmtpPort > 65535) {
    $resolvedSmtpPort = $defaultSmtpPort;
}

$smtpConfiguration = [
    'host' => $resolvedSmtpHost,
    'port' => $resolvedSmtpPort,
    'encryption' => isset($_GET['encryption']) && is_string($_GET['encryption']) && $_GET['encryption'] !== ''
        ? strtolower(trim($_GET['encryption']))
        : strtolower($resolveEnvironmentValue('WYP_SMTP_ENCRYPTION', 'tls')),
    'auth' => isset($_GET['auth']) ? ($_GET['auth'] === '1') : $resolveEnvironmentFlag('WYP_SMTP_AUTH', true),
    'username' => $resolveEnvironmentValue('WYP_SMTP_USERNAME'),
    'password' => $resolveEnvironmentValue('WYP_SMTP_PASSWORD'),
    'timeout' => isset($_GET['timeout']) ? max(3, (int) $_GET['timeout']) : $defaultSmtpTimeout,
];

$shouldSendDiagnosticMessage = isset($_GET['send']) && $_GET['send'] === '1';
$recipientEmailAddress = isset($_GET['to']) && is_string($_GET['to']) && $_GET['to'] !== '' ? trim($_GET['to']) : $adminRecipientEmail;

$testSmtpPortConnectivity = static function (string $host, int $port): array {
    if ($host === '' || $port <= 0) {
        return ['ok' => false, 'error' => 'host_or_port_missing'];
    }
    $errno = 0;
    $errstr = '';
    $start = microtime(true);
    $stream = @fsockopen($host, $port, $errno, $errstr, 8);
    $latency = (int) round((microtime(true) - $start) * 1000);
    if (!is_resource($stream)) {
        return ['ok' => false, 'errno' => $errno, 'error' => $errstr !== '' ? $errstr : 'connect_failed', 'latency_ms' => $latency];
    }
    stream_set_timeout($stream, 3);
    $banner = fgets($stream, 512);
    fclose($stream);
    return ['ok' => true, 'latency_ms' => $latency, 'banner' => is_string($banner) ? trim($banner) : ''];
};

$smtpDiagnosticReport = [
    'generated_at_utc' => gmdate('c'),
    'smtp' => [
        'host' => $smtpConfiguration['host'],
        'port' => $smtpConfiguration['port'],
        'encryption' => $smtpConfiguration['encryption'],
        'auth' => $smtpConfiguration['auth'],
        'username_set' => $smtpConfiguration['username'] !== '',
        'password_set' => $smtpConfiguration['password'] !== '',
        'timeout' => $smtpConfiguration['timeout'],
    ],
    'mail_routing' => [
        'contact_recipient_email' => $adminRecipientEmail,
        'configured_from_email' => $configuredFromAddress,
        'resolved_from_email' => $fromEmailAddress,
    ],
    'to' => $recipientEmailAddress,
    'port_probe' => $testSmtpPortConnectivity($smtpConfiguration['host'], $smtpConfiguration['port']),
    'phpmailer' => [
        'loaded' => false,
        'load_reason' => '',
    ],
    'smtp_connect' => null,
    'send_attempt' => null,
    'debug_log' => $diagnosticsVerboseEnabled ? [] : ['Verbose debug disabled. Set WYP_DIAG_VERBOSE=1 for protocol logs.'],
];

$phpMailerDependencyStatus = $loadPhpMailerDependencies();
$smtpDiagnosticReport['phpmailer']['loaded'] = (bool) ($phpMailerDependencyStatus['ok'] ?? false);
$smtpDiagnosticReport['phpmailer']['load_reason'] = (string) ($phpMailerDependencyStatus['reason'] ?? '');

if (!$smtpDiagnosticReport['phpmailer']['loaded']) {
    if ($shouldReturnJson) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($smtpDiagnosticReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo json_encode($smtpDiagnosticReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

$phpMailerClass = 'PHPMailer\\PHPMailer\\PHPMailer';

try {
    $mail = new $phpMailerClass(true);
    $mail->isSMTP();
    $mail->Host = $smtpConfiguration['host'];
    $mail->Port = (int) $smtpConfiguration['port'];
    $mail->SMTPAuth = (bool) $smtpConfiguration['auth'];
    $mail->Username = (string) $smtpConfiguration['username'];
    $mail->Password = (string) $smtpConfiguration['password'];
    $mail->Timeout = (int) $smtpConfiguration['timeout'];
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPDebug = $diagnosticsVerboseEnabled ? 3 : 0;
    if ($diagnosticsVerboseEnabled) {
        $mail->Debugoutput = static function (string $line, int $level) use (&$smtpDiagnosticReport): void {
            $smtpDiagnosticReport['debug_log'][] = '[' . $level . '] ' . $line;
        };
    }

    if ($smtpConfiguration['encryption'] === 'ssl') {
        $mail->SMTPSecure = $phpMailerClass::ENCRYPTION_SMTPS;
    } elseif ($smtpConfiguration['encryption'] === 'tls') {
        $mail->SMTPSecure = $phpMailerClass::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = '';
    }

    $connected = $mail->smtpConnect();
    $smtpDiagnosticReport['smtp_connect'] = [
        'ok' => (bool) $connected,
        'error_info' => (string) $mail->ErrorInfo,
    ];

    if ($connected && $shouldSendDiagnosticMessage) {
        if (!filter_var($recipientEmailAddress, FILTER_VALIDATE_EMAIL)) {
            $smtpDiagnosticReport['send_attempt'] = [
                'ok' => false,
                'error' => 'Invalid recipient address',
            ];
        } else {
            $mail->setFrom($fromEmailAddress, $siteDisplayName . ' SMTP Test');
            $mail->addAddress($recipientEmailAddress);
            $mail->addReplyTo($adminRecipientEmail, $siteDisplayName . ' Admin');
            $mail->Subject = 'SMTP Deliverability Test ' . gmdate('c');
            $mail->isHTML(true);
            $mail->Body = '<p>This is an SMTP test message from diagnostics/smtp_delivery_test.php</p>'
                . '<p>UTC: ' . htmlspecialchars(gmdate('c'), ENT_QUOTES, 'UTF-8') . '</p>';
            $mail->AltBody = "This is an SMTP test message from diagnostics/smtp_delivery_test.php\r\nUTC: " . gmdate('c');
            $mail->addCustomHeader('X-Diagnostic', 'smtp_delivery_test.php');

            $sent = $mail->send();
            $smtpDiagnosticReport['send_attempt'] = [
                'ok' => (bool) $sent,
                'error_info' => (string) $mail->ErrorInfo,
            ];
        }
    }

    $mail->smtpClose();
} catch (\Throwable $e) {
    $smtpDiagnosticReport['smtp_connect'] = [
        'ok' => false,
        'error_class' => get_class($e),
        'error_message' => $e->getMessage(),
    ];
}

if ($shouldReturnJson) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($smtpDiagnosticReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>SMTP Test</title>';
echo '<style>body{font-family:Arial,sans-serif;max-width:980px;margin:24px auto;padding:0 12px}pre{background:#f5f5f5;padding:12px;border-radius:8px;overflow:auto}</style>';
echo '</head><body><h1>SMTP Test Route</h1><p>Temporary endpoint. Remove after troubleshooting.</p>';
echo '<pre>' . htmlspecialchars(json_encode($smtpDiagnosticReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') . '</pre>';
echo '</body></html>';


