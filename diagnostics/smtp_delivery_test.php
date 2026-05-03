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
 */

header('Content-Type: text/plain');
var_dump(getenv('WYP_DIAG_KEY'));
var_dump($_SERVER['WYP_DIAG_KEY'] ?? null);
var_dump($_ENV['WYP_DIAG_KEY'] ?? null);

$expectedAccessKey = (string) getenv('WYP_DIAG_KEY');
$providedAccessKey = isset($_GET['key']) && is_string($_GET['key']) ? $_GET['key'] : '';
$isJsonResponseRequested = (isset($_GET['format']) && $_GET['format'] === 'json');

if ($expectedAccessKey === '' || $providedAccessKey === '' || !hash_equals($expectedAccessKey, $providedAccessKey)) {
    http_response_code(403);
    $message = [
        'ok' => false,
        'error' => $expectedAccessKey === '' ? 'WYP_DIAG_KEY is not set' : 'Invalid key',
    ];
    if ($isJsonResponseRequested) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Access denied.\n";
    exit;
}

$loadPhpMailer = static function (): array {
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

$mailConfigurationFilePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'contact_mail.php';
$mailConfiguration = is_file($mailConfigurationFilePath) ? require $mailConfigurationFilePath : [];

$siteDisplayName = (string) ($mailConfiguration['site']['name'] ?? 'Wipe Your Paws');
$fromEmailAddress = (string) ($mailConfiguration['site']['from_email'] ?? 'noreply@example.com');
$adminRecipientEmail = (string) ($mailConfiguration['site']['admin_email'] ?? $fromEmailAddress);

$smtpSettings = [
    'host' => isset($_GET['host']) && is_string($_GET['host']) && $_GET['host'] !== ''
        ? trim($_GET['host'])
        : (string) ($mailConfiguration['smtp']['host'] ?? ''),
    'port' => isset($_GET['port']) ? (int) $_GET['port'] : (int) ($mailConfiguration['smtp']['port'] ?? 587),
    'encryption' => isset($_GET['encryption']) && is_string($_GET['encryption']) && $_GET['encryption'] !== ''
        ? strtolower(trim($_GET['encryption']))
        : strtolower((string) ($mailConfiguration['smtp']['encryption'] ?? 'tls')),
    'auth' => isset($_GET['auth']) ? ($_GET['auth'] === '1') : (bool) ($mailConfiguration['smtp']['auth'] ?? true),
    'username' => isset($_GET['username']) && is_string($_GET['username']) ? trim($_GET['username']) : (string) ($mailConfiguration['smtp']['username'] ?? ''),
    'password' => isset($_GET['password']) && is_string($_GET['password']) ? $_GET['password'] : (string) ($mailConfiguration['smtp']['password'] ?? ''),
    'timeout' => isset($_GET['timeout']) ? max(3, (int) $_GET['timeout']) : (int) ($mailConfiguration['smtp']['timeout'] ?? 15),
];

$shouldSendTestMessage = isset($_GET['send']) && $_GET['send'] === '1';
$recipientEmailAddress = isset($_GET['to']) && is_string($_GET['to']) && $_GET['to'] !== '' ? trim($_GET['to']) : $adminRecipientEmail;

$probeSmtpPort = static function (string $host, int $port): array {
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

$diagnosticReport = [
    'generated_at_utc' => gmdate('c'),
    'smtp' => [
        'host' => $smtpSettings['host'],
        'port' => $smtpSettings['port'],
        'encryption' => $smtpSettings['encryption'],
        'auth' => $smtpSettings['auth'],
        'username_set' => $smtpSettings['username'] !== '',
        'password_set' => $smtpSettings['password'] !== '',
        'timeout' => $smtpSettings['timeout'],
    ],
    'to' => $recipientEmailAddress,
    'port_probe' => $probeSmtpPort($smtpSettings['host'], $smtpSettings['port']),
    'phpmailer' => [
        'loaded' => false,
        'load_reason' => '',
    ],
    'smtp_connect' => null,
    'send_attempt' => null,
    'debug_log' => [],
];

$phpMailerLoaderStatus = $loadPhpMailer();
$diagnosticReport['phpmailer']['loaded'] = (bool) ($phpMailerLoaderStatus['ok'] ?? false);
$diagnosticReport['phpmailer']['load_reason'] = (string) ($phpMailerLoaderStatus['reason'] ?? '');

if (!$diagnosticReport['phpmailer']['loaded']) {
    if ($isJsonResponseRequested) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($diagnosticReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo json_encode($diagnosticReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

$phpMailerClass = 'PHPMailer\\PHPMailer\\PHPMailer';

try {
    $mail = new $phpMailerClass(true);
    $mail->isSMTP();
    $mail->Host = $smtpSettings['host'];
    $mail->Port = (int) $smtpSettings['port'];
    $mail->SMTPAuth = (bool) $smtpSettings['auth'];
    $mail->Username = (string) $smtpSettings['username'];
    $mail->Password = (string) $smtpSettings['password'];
    $mail->Timeout = (int) $smtpSettings['timeout'];
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPDebug = 3;
    $mail->Debugoutput = static function (string $line, int $level) use (&$diagnosticReport): void {
        $diagnosticReport['debug_log'][] = '[' . $level . '] ' . $line;
    };

    if ($smtpSettings['encryption'] === 'ssl') {
        $mail->SMTPSecure = $phpMailerClass::ENCRYPTION_SMTPS;
    } elseif ($smtpSettings['encryption'] === 'tls') {
        $mail->SMTPSecure = $phpMailerClass::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = '';
    }

    $connected = $mail->smtpConnect();
    $diagnosticReport['smtp_connect'] = [
        'ok' => (bool) $connected,
        'error_info' => (string) $mail->ErrorInfo,
    ];

    if ($connected && $shouldSendTestMessage) {
        if (!filter_var($recipientEmailAddress, FILTER_VALIDATE_EMAIL)) {
            $diagnosticReport['send_attempt'] = [
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
            $diagnosticReport['send_attempt'] = [
                'ok' => (bool) $sent,
                'error_info' => (string) $mail->ErrorInfo,
            ];
        }
    }

    $mail->smtpClose();
} catch (\Throwable $e) {
    $diagnosticReport['smtp_connect'] = [
        'ok' => false,
        'error_class' => get_class($e),
        'error_message' => $e->getMessage(),
    ];
}

if ($isJsonResponseRequested) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($diagnosticReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>SMTP Test</title>';
echo '<style>body{font-family:Arial,sans-serif;max-width:980px;margin:24px auto;padding:0 12px}pre{background:#f5f5f5;padding:12px;border-radius:8px;overflow:auto}</style>';
echo '</head><body><h1>SMTP Test Route</h1><p>Temporary endpoint. Remove after troubleshooting.</p>';
echo '<pre>' . htmlspecialchars(json_encode($diagnosticReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') . '</pre>';
echo '</body></html>';


