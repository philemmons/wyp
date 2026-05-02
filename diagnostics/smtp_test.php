<?php

declare(strict_types=1);

/**
 * Temporary SMTP test route.
 * Remove after troubleshooting.
 *
 * Usage:
 * /diagnostics/smtp_test.php?key=YOUR_KEY
 * /diagnostics/smtp_test.php?key=YOUR_KEY&to=you@example.com&send=1
 * /diagnostics/smtp_test.php?key=YOUR_KEY&format=json
 */

$expected_key = (string) getenv('WYP_DIAG_KEY');
$provided_key = isset($_GET['key']) && is_string($_GET['key']) ? $_GET['key'] : '';
$json_mode = (isset($_GET['format']) && $_GET['format'] === 'json');

if ($expected_key === '' || $provided_key === '' || !hash_equals($expected_key, $provided_key)) {
    http_response_code(403);
    $message = [
        'ok' => false,
        'error' => $expected_key === '' ? 'WYP_DIAG_KEY is not set' : 'Invalid key',
    ];
    if ($json_mode) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Access denied.\n";
    exit;
}

$load_phpmailer = static function (): array {
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

$config_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'contact_mail.php';
$config = is_file($config_path) ? require $config_path : [];

$site_name = (string) ($config['site']['name'] ?? 'Wipe Your Paws');
$from_email = (string) ($config['site']['from_email'] ?? 'noreply@example.com');
$admin_email = (string) ($config['site']['admin_email'] ?? $from_email);

$smtp = [
    'host' => isset($_GET['host']) && is_string($_GET['host']) && $_GET['host'] !== ''
        ? trim($_GET['host'])
        : (string) ($config['smtp']['host'] ?? ''),
    'port' => isset($_GET['port']) ? (int) $_GET['port'] : (int) ($config['smtp']['port'] ?? 587),
    'encryption' => isset($_GET['encryption']) && is_string($_GET['encryption']) && $_GET['encryption'] !== ''
        ? strtolower(trim($_GET['encryption']))
        : strtolower((string) ($config['smtp']['encryption'] ?? 'tls')),
    'auth' => isset($_GET['auth']) ? ($_GET['auth'] === '1') : (bool) ($config['smtp']['auth'] ?? true),
    'username' => isset($_GET['username']) && is_string($_GET['username']) ? trim($_GET['username']) : (string) ($config['smtp']['username'] ?? ''),
    'password' => isset($_GET['password']) && is_string($_GET['password']) ? $_GET['password'] : (string) ($config['smtp']['password'] ?? ''),
    'timeout' => isset($_GET['timeout']) ? max(3, (int) $_GET['timeout']) : (int) ($config['smtp']['timeout'] ?? 15),
];

$send_mode = isset($_GET['send']) && $_GET['send'] === '1';
$to = isset($_GET['to']) && is_string($_GET['to']) && $_GET['to'] !== '' ? trim($_GET['to']) : $admin_email;

$port_probe = static function (string $host, int $port): array {
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

$result = [
    'generated_at_utc' => gmdate('c'),
    'smtp' => [
        'host' => $smtp['host'],
        'port' => $smtp['port'],
        'encryption' => $smtp['encryption'],
        'auth' => $smtp['auth'],
        'username_set' => $smtp['username'] !== '',
        'password_set' => $smtp['password'] !== '',
        'timeout' => $smtp['timeout'],
    ],
    'to' => $to,
    'port_probe' => $port_probe($smtp['host'], $smtp['port']),
    'phpmailer' => [
        'loaded' => false,
        'load_reason' => '',
    ],
    'smtp_connect' => null,
    'send_attempt' => null,
    'debug_log' => [],
];

$loader = $load_phpmailer();
$result['phpmailer']['loaded'] = (bool) ($loader['ok'] ?? false);
$result['phpmailer']['load_reason'] = (string) ($loader['reason'] ?? '');

if (!$result['phpmailer']['loaded']) {
    if ($json_mode) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

$pm_class = 'PHPMailer\\PHPMailer\\PHPMailer';

try {
    $mail = new $pm_class(true);
    $mail->isSMTP();
    $mail->Host = $smtp['host'];
    $mail->Port = (int) $smtp['port'];
    $mail->SMTPAuth = (bool) $smtp['auth'];
    $mail->Username = (string) $smtp['username'];
    $mail->Password = (string) $smtp['password'];
    $mail->Timeout = (int) $smtp['timeout'];
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPDebug = 3;
    $mail->Debugoutput = static function (string $line, int $level) use (&$result): void {
        $result['debug_log'][] = '[' . $level . '] ' . $line;
    };

    if ($smtp['encryption'] === 'ssl') {
        $mail->SMTPSecure = $pm_class::ENCRYPTION_SMTPS;
    } elseif ($smtp['encryption'] === 'tls') {
        $mail->SMTPSecure = $pm_class::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = '';
    }

    $connected = $mail->smtpConnect();
    $result['smtp_connect'] = [
        'ok' => (bool) $connected,
        'error_info' => (string) $mail->ErrorInfo,
    ];

    if ($connected && $send_mode) {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $result['send_attempt'] = [
                'ok' => false,
                'error' => 'Invalid recipient address',
            ];
        } else {
            $mail->setFrom($from_email, $site_name . ' SMTP Test');
            $mail->addAddress($to);
            $mail->addReplyTo($admin_email, $site_name . ' Admin');
            $mail->Subject = 'SMTP Deliverability Test ' . gmdate('c');
            $mail->isHTML(true);
            $mail->Body = '<p>This is an SMTP test message from diagnostics/smtp_test.php</p>'
                . '<p>UTC: ' . htmlspecialchars(gmdate('c'), ENT_QUOTES, 'UTF-8') . '</p>';
            $mail->AltBody = "This is an SMTP test message from diagnostics/smtp_test.php\r\nUTC: " . gmdate('c');
            $mail->addCustomHeader('X-Diagnostic', 'smtp_test.php');

            $sent = $mail->send();
            $result['send_attempt'] = [
                'ok' => (bool) $sent,
                'error_info' => (string) $mail->ErrorInfo,
            ];
        }
    }

    $mail->smtpClose();
} catch (\Throwable $e) {
    $result['smtp_connect'] = [
        'ok' => false,
        'error_class' => get_class($e),
        'error_message' => $e->getMessage(),
    ];
}

if ($json_mode) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>SMTP Test</title>';
echo '<style>body{font-family:Arial,sans-serif;max-width:980px;margin:24px auto;padding:0 12px}pre{background:#f5f5f5;padding:12px;border-radius:8px;overflow:auto}</style>';
echo '</head><body><h1>SMTP Test Route</h1><p>Temporary endpoint. Remove after troubleshooting.</p>';
echo '<pre>' . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') . '</pre>';
echo '</body></html>';
