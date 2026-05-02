<?php

declare(strict_types=1);

session_start();

$request_id = bin2hex(random_bytes(8));
$started_at = microtime(true);

$default_config = [
    'site' => [
        'name' => 'Wipe Your Paws',
        'url' => 'https://wipeyourpaws.net/contact.php',
        'admin_email' => 'admin@wipeyourpaws.net',
        'from_email' => 'noreply@wipeyourpaws.net',
    ],
    'smtp' => [
        'enabled' => false,
        'host' => '',
        'port' => 587,
        'encryption' => 'tls',
        'auth' => true,
        'username' => '',
        'password' => '',
        'timeout' => 15,
        'debug' => 0,
        'allow_self_signed' => false,
    ],
    'dkim' => [
        'enabled' => false,
        'domain' => 'wipeyourpaws.net',
        'selector' => '',
        'private_key_path' => '',
        'identity' => '',
        'passphrase' => '',
    ],
    'logging' => [
        'path' => __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'contact-mail.log',
    ],
];

$config_file = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'contact_mail.php';
$file_config = [];
if (is_file($config_file)) {
    $loaded_config = require $config_file;
    if (is_array($loaded_config)) {
        $file_config = $loaded_config;
    }
}
$mail_config = array_replace_recursive($default_config, $file_config);

$site_name = (string) ($mail_config['site']['name'] ?? 'Wipe Your Paws');
$public_contact_url = (string) ($mail_config['site']['url'] ?? 'https://wipeyourpaws.net/contact.php');
$admin_email = (string) ($mail_config['site']['admin_email'] ?? 'admin@wipeyourpaws.net');
$from_email = (string) ($mail_config['site']['from_email'] ?? 'noreply@wipeyourpaws.net');
$log_path = (string) ($mail_config['logging']['path'] ?? (__DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'contact-mail.log'));

$mask_email = static function (string $value): string {
    $parts = explode('@', $value, 2);
    if (count($parts) !== 2) {
        return 'invalid';
    }

    $local = $parts[0];
    $domain = $parts[1];
    if ($local === '' || $domain === '') {
        return 'invalid';
    }

    return mb_substr($local, 0, 1) . '***@' . $domain;
};

$log_event = static function (string $event, array $context = []) use ($request_id, $log_path): void {
    $log_dir = dirname($log_path);
    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }

    $redacted_keys = [
        'csrf_token',
        'submitted_token',
        'session_token',
        'raw_message',
        'message',
        'headers',
        'password',
        'secret',
        'smtp_password',
        'dkim_passphrase',
    ];
    foreach ($redacted_keys as $key) {
        if (array_key_exists($key, $context)) {
            unset($context[$key]);
        }
    }

    $entry = [
        'ts' => gmdate('c'),
        'request_id' => $request_id,
        'event' => $event,
        'context' => $context,
    ];

    $line = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($line === false) {
        $line = '{"ts":"' . gmdate('c') . '","request_id":"' . $request_id . '","event":"json_encode_failed"}';
    }

    error_log($line);
    @file_put_contents($log_path, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
};

$post_string = static function (string $key): string {
    $value = $_POST[$key] ?? '';
    if (!is_string($value)) {
        return '';
    }

    return trim(str_replace("\0", '', $value));
};

$normalize_whitespace = static function (string $value): string {
    return preg_replace('/\s+/u', ' ', $value) ?? $value;
};

$contains_header_injection = static function (string $value): bool {
    return preg_match('/\r|\n/', $value) === 1;
};

$get_ip_address = static function (): string {
    $ip_candidate = $_SERVER['REMOTE_ADDR'] ?? '';

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && is_string($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $forwarded = trim($parts[0] ?? '');
        if (filter_var($forwarded, FILTER_VALIDATE_IP)) {
            $ip_candidate = $forwarded;
        }
    }

    $safe_ip = filter_var($ip_candidate, FILTER_VALIDATE_IP);
    return is_string($safe_ip) ? $safe_ip : 'unknown';
};

$safe_header_value = static function (string $value): string {
    return str_replace(["\r", "\n"], '', trim($value));
};

$load_phpmailer = static function (): array {
    $ph_class = 'PHPMailer\\PHPMailer\\PHPMailer';

    if (class_exists($ph_class)) {
        return ['ok' => true, 'reason' => 'already_loaded'];
    }

    $autoload = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    if (is_file($autoload)) {
        require_once $autoload;
        if (class_exists($ph_class)) {
            return ['ok' => true, 'reason' => 'composer_autoload'];
        }
    }

    $base = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
    $required = ['Exception.php', 'PHPMailer.php', 'SMTP.php'];
    $all_present = true;
    foreach ($required as $file) {
        if (!is_file($base . $file)) {
            $all_present = false;
            break;
        }
    }

    if ($all_present) {
        require_once $base . 'Exception.php';
        require_once $base . 'PHPMailer.php';
        require_once $base . 'SMTP.php';
        if (class_exists($ph_class)) {
            return ['ok' => true, 'reason' => 'manual_include'];
        }
    }

    return ['ok' => false, 'reason' => 'phpmailer_not_found'];
};

$send_via_native_mail = static function (
    string $channel,
    string $to,
    string $subject,
    string $html_body,
    string $text_body,
    array $headers,
    string $envelope_sender,
    callable $logger,
    ?callable $recipient_masker = null
): array {
    $masked_to = $recipient_masker ? $recipient_masker($to) : $to;
    $additional_params = '-f' . $envelope_sender;
    $payload_body = $text_body;
    $effective_headers = $headers;

    if (trim($html_body) !== '') {
        $boundary = 'wyp_' . bin2hex(random_bytes(12));
        $filtered = [];
        foreach ($headers as $header) {
            $normalized = strtolower(trim($header));
            if (str_starts_with($normalized, 'content-type:') || str_starts_with($normalized, 'mime-version:')) {
                continue;
            }
            $filtered[] = $header;
        }

        $effective_headers = $filtered;
        $effective_headers[] = 'MIME-Version: 1.0';
        $effective_headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

        $payload_body = "--{$boundary}\r\n";
        $payload_body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $payload_body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $payload_body .= $text_body . "\r\n";
        $payload_body .= "--{$boundary}\r\n";
        $payload_body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $payload_body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $payload_body .= $html_body . "\r\n";
        $payload_body .= "--{$boundary}--\r\n";
    }

    $header_blob = implode("\r\n", $effective_headers);

    $logger('native_mail_attempt', [
        'channel' => $channel,
        'to' => $masked_to,
        'subject_len' => mb_strlen($subject),
        'body_len' => mb_strlen($payload_body),
        'header_count' => count($effective_headers),
        'multipart' => trim($html_body) !== '',
        'has_envelope_sender' => true,
    ]);

    $error_before = error_get_last();
    $ok = @mail($to, $subject, $payload_body, $header_blob, $additional_params);
    $error_after = error_get_last();

    $warning = '';
    if ($error_after !== $error_before && isset($error_after['message']) && is_string($error_after['message'])) {
        $warning = $error_after['message'];
    }

    $logger('native_mail_result', [
        'channel' => $channel,
        'to' => $masked_to,
        'ok' => $ok,
        'attempt' => 'with_envelope_sender',
        'warning' => $warning,
    ]);

    if (!$ok) {
        $error_before_fallback = error_get_last();
        $ok_fallback = @mail($to, $subject, $payload_body, $header_blob);
        $error_after_fallback = error_get_last();
        $fallback_warning = '';
        if ($error_after_fallback !== $error_before_fallback && isset($error_after_fallback['message']) && is_string($error_after_fallback['message'])) {
            $fallback_warning = $error_after_fallback['message'];
        }

        $logger('native_mail_result', [
            'channel' => $channel,
            'to' => $masked_to,
            'ok' => $ok_fallback,
            'attempt' => 'without_envelope_sender',
            'warning' => $fallback_warning,
        ]);

        if ($ok_fallback) {
            return ['ok' => true, 'transport' => 'native_mail_fallback_no_envelope', 'reason' => 'fallback_succeeded'];
        }
    }

    return [
        'ok' => $ok,
        'transport' => 'native_mail',
        'reason' => $ok ? '' : ($warning !== '' ? $warning : 'mail_returned_false'),
    ];
};

$send_via_phpmailer = static function (
    string $channel,
    string $to,
    string $to_name,
    string $subject,
    string $html_body,
    string $text_body,
    string $reply_to_email,
    string $reply_to_name,
    array $config,
    string $from_email_address,
    string $from_name_value,
    callable $logger,
    ?callable $recipient_masker = null
): array {
    $pm_class = 'PHPMailer\\PHPMailer\\PHPMailer';

    try {
        $mail = new $pm_class(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSMTP();
        $mail->Host = (string) ($config['smtp']['host'] ?? '');
        $mail->Port = (int) ($config['smtp']['port'] ?? 587);
        $mail->SMTPAuth = (bool) ($config['smtp']['auth'] ?? true);
        $mail->Username = (string) ($config['smtp']['username'] ?? '');
        $mail->Password = (string) ($config['smtp']['password'] ?? '');
        $mail->Timeout = (int) ($config['smtp']['timeout'] ?? 15);
        $mail->Sender = $from_email_address;

        $smtp_debug = (int) ($config['smtp']['debug'] ?? 0);
        $mail->SMTPDebug = $smtp_debug;
        $mail->Debugoutput = static function (string $str, int $level) use ($logger, $channel): void {
            $logger('smtp_debug', [
                'channel' => $channel,
                'level' => $level,
                'message' => mb_substr($str, 0, 800),
            ]);
        };

        $encryption = strtolower((string) ($config['smtp']['encryption'] ?? 'tls'));
        if ($encryption === 'tls') {
            $mail->SMTPSecure = $pm_class::ENCRYPTION_STARTTLS;
        } elseif ($encryption === 'ssl') {
            $mail->SMTPSecure = $pm_class::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = '';
        }

        if ((bool) ($config['smtp']['allow_self_signed'] ?? false)) {
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        }

        $mail->setFrom($from_email_address, $from_name_value);
        $mail->addAddress($to, $to_name);
        $mail->addReplyTo($reply_to_email, $reply_to_name);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $html_body;
        $mail->AltBody = $text_body;
        $mail->addCustomHeader('Auto-Submitted', $channel === 'user_autoreply' ? 'auto-replied' : 'no');
        $mail->addCustomHeader('X-Mailer', 'PHPMailer');

        $dkim_enabled = (bool) ($config['dkim']['enabled'] ?? false);
        if ($dkim_enabled) {
            $dkim_private_key_path = (string) ($config['dkim']['private_key_path'] ?? '');
            if ($dkim_private_key_path !== '' && is_file($dkim_private_key_path)) {
                $mail->DKIM_domain = (string) ($config['dkim']['domain'] ?? '');
                $mail->DKIM_selector = (string) ($config['dkim']['selector'] ?? '');
                $mail->DKIM_private = $dkim_private_key_path;
                $mail->DKIM_identity = (string) ($config['dkim']['identity'] ?? $from_email_address);
                $mail->DKIM_passphrase = (string) ($config['dkim']['passphrase'] ?? '');
            } else {
                $logger('dkim_skipped', [
                    'channel' => $channel,
                    'reason' => 'private_key_not_found',
                ]);
            }
        }

        $masked_to = $recipient_masker ? $recipient_masker($to) : $to;
        $logger('smtp_attempt', [
            'channel' => $channel,
            'to' => $masked_to,
            'host' => (string) ($config['smtp']['host'] ?? ''),
            'port' => (int) ($config['smtp']['port'] ?? 587),
            'encryption' => $encryption,
            'debug_level' => $smtp_debug,
        ]);

        $ok = $mail->send();
        if ($ok) {
            $logger('smtp_result', [
                'channel' => $channel,
                'to' => $masked_to,
                'ok' => true,
            ]);

            return ['ok' => true, 'transport' => 'phpmailer_smtp', 'reason' => ''];
        }

        $error_info = (string) $mail->ErrorInfo;
        $logger('smtp_result', [
            'channel' => $channel,
            'to' => $masked_to,
            'ok' => false,
            'error_info' => $error_info,
        ]);

        return ['ok' => false, 'transport' => 'phpmailer_smtp', 'reason' => $error_info !== '' ? $error_info : 'smtp_send_failed'];
    } catch (\Throwable $e) {
        $logger('smtp_exception', [
            'channel' => $channel,
            'error_class' => get_class($e),
            'error_message' => mb_substr($e->getMessage(), 0, 900),
        ]);

        return ['ok' => false, 'transport' => 'phpmailer_smtp', 'reason' => $e->getMessage()];
    }
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $log_event('invalid_method', ['method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown']);
    header('Location: contact.php');
    exit;
}

$ip = $get_ip_address();
$ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
$log_event('submission_received', [
    'ip' => $ip,
    'ua_len' => mb_strlen($ua),
    'transport_preference' => (bool) ($mail_config['smtp']['enabled'] ?? false) ? 'smtp_first' : 'native_mail_only',
]);

$submitted_token = $post_string('csrf_token');
$session_token = $_SESSION['csrf_token'] ?? '';
if (
    $submitted_token === '' ||
    !is_string($session_token) ||
    $session_token === '' ||
    !hash_equals($session_token, $submitted_token)
) {
    $_SESSION['form_error'] = true;
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    $log_event('csrf_failed', [
        'has_submitted_token' => $submitted_token !== '',
        'has_session_token' => is_string($session_token) && $session_token !== '',
    ]);

    header('Location: contact.php');
    exit;
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$rate_window_seconds = 600;
$rate_limit_max = 5;
$now = time();
$recent_submissions = $_SESSION['contact_submit_times'] ?? [];
if (!is_array($recent_submissions)) {
    $recent_submissions = [];
}

$recent_submissions = array_values(array_filter(
    $recent_submissions,
    static fn ($ts): bool => is_int($ts) && ($now - $ts) <= $rate_window_seconds
));

if (count($recent_submissions) >= $rate_limit_max) {
    $_SESSION['form_error'] = true;
    $_SESSION['form_errors'] = ['Too many submissions from this session. Please wait a few minutes and try again.'];
    $log_event('rate_limited', [
        'ip' => $ip,
        'recent_count' => count($recent_submissions),
        'window_seconds' => $rate_window_seconds,
    ]);
    header('Location: contact.php');
    exit;
}

$recent_submissions[] = $now;
$_SESSION['contact_submit_times'] = $recent_submissions;

$honeypot = $post_string('website');
if ($honeypot !== '') {
    $_SESSION['form_sent'] = true;
    $_SESSION['form_confirmation_sent'] = false;
    $log_event('honeypot_triggered', ['ip' => $ip]);
    header('Location: contact.php');
    exit;
}

$name_raw = $post_string('name');
$email_raw = $post_string('email');
$subject_raw = $post_string('subject');
$inquiry_type_raw = $post_string('inquiry_type');
$message_raw = $post_string('message');

$name = $normalize_whitespace(strip_tags($name_raw));
$email = strtolower($email_raw);
$subject = $normalize_whitespace(strip_tags($subject_raw));
$inquiry_type = $normalize_whitespace(strip_tags($inquiry_type_raw));
$message = strip_tags($message_raw);
$message = preg_replace('/[^\P{C}\r\n\t]/u', '', $message) ?? $message;
$message = trim($message);

/** @var list<string> $errors */
$errors = [];
/** @var array<string, string> $field_errors */
$field_errors = [];
$add_error = static function (string $field, string $message) use (&$errors, &$field_errors): void {
    $errors[] = $message;
    $field_errors[$field] = $message;
};

if ($contains_header_injection($email_raw) || $contains_header_injection($name_raw) || $contains_header_injection($subject_raw)) {
    $add_error('email', 'Invalid input detected in email headers.');
}

if ($name === '') {
    $add_error('name', 'Name is required. Please enter your full name.');
} elseif (mb_strlen($name) > 120) {
    $add_error('name', 'Name is too long. Use 120 characters or fewer.');
}

if ($email === '') {
    $add_error('email', 'Email address is required. Enter an email like name@example.com.');
} elseif (mb_strlen($email) > 254) {
    $add_error('email', 'Email address is too long. Use 254 characters or fewer.');
} elseif (preg_match('/\.\./', $email) === 1) {
    $add_error('email', 'Email format is not valid. Consecutive dots are not allowed.');
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $add_error('email', 'Email format is not valid. Use a format like name@example.com.');
}

if (mb_strlen($subject) > 200) {
    $add_error('subject', 'Subject is too long. Use 200 characters or fewer.');
}

if (mb_strlen($inquiry_type) > 80) {
    $add_error('inquiry_type', 'Inquiry type is too long. Use 80 characters or fewer.');
}

if ($message === '') {
    $add_error('message', 'Message is required. Tell us how we can help.');
} elseif (mb_strlen($message) < 10) {
    $add_error('message', 'Message is too short. Please add at least 10 characters.');
} elseif (mb_strlen($message) > 5000) {
    $add_error('message', 'Message is too long. Limit your message to 5,000 characters.');
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_field_errors'] = $field_errors;
    $_SESSION['form_values'] = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'inquiry_type' => $inquiry_type,
        'message' => $message,
    ];

    $log_event('validation_failed', [
        'ip' => $ip,
        'error_count' => count($errors),
        'fields' => array_keys($field_errors),
        'masked_email' => $mask_email($email),
    ]);

    header('Location: contact.php');
    exit;
}

$log_event('validation_passed', [
    'ip' => $ip,
    'masked_email' => $mask_email($email),
]);

$resolved_inquiry_type = $inquiry_type !== '' ? $inquiry_type : 'General inquiry';
$safe_subject = $safe_header_value($subject !== '' ? $subject : 'New message from contact form');
$admin_subject = '[wipeyourpaws.net] ' . $safe_subject;
$reply_subject = 'We received your message - ' . $site_name;

$first_name = $name;
if (preg_match('/^\S+/u', $name, $name_match) === 1) {
    $first_name = $name_match[0];
}
$first_name = mb_substr($first_name, 0, 60);
$greeting_name = $first_name !== '' ? $first_name : 'there';

$message_compact = preg_replace('/\s+/u', ' ', $message) ?? $message;
$message_preview = trim(mb_substr($message_compact, 0, 220));
if (mb_strlen($message_compact) > 220) {
    $message_preview .= '...';
}

$admin_text = "You have a new message from the wipeyourpaws.net contact form.\r\n\r\n";
$admin_text .= "------------------------------\r\n";
$admin_text .= "Name    : {$name}\r\n";
$admin_text .= "Email   : {$email}\r\n";
$admin_text .= "Type    : {$resolved_inquiry_type}\r\n";
$admin_text .= "Subject : {$subject}\r\n";
$admin_text .= "------------------------------\r\n\r\n";
$admin_text .= "Message:\r\n{$message}\r\n\r\n";
$admin_text .= "------------------------------\r\n";
$admin_text .= 'Sent    : ' . date('Y-m-d H:i:s T') . "\r\n";
$admin_text .= "IP      : {$ip}\r\n";
$admin_text .= "Request : {$request_id}\r\n";

$admin_html = '<!doctype html>'
    . '<html lang="en"><body style="font-family:Arial,Helvetica,sans-serif;color:#1f1f1f;">'
    . '<h2>New contact form submission</h2>'
    . '<p><strong>Name:</strong> ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '<br>'
    . '<strong>Email:</strong> ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '<br>'
    . '<strong>Type:</strong> ' . htmlspecialchars($resolved_inquiry_type, ENT_QUOTES, 'UTF-8') . '<br>'
    . '<strong>Subject:</strong> ' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '<br>'
    . '<strong>IP:</strong> ' . htmlspecialchars($ip, ENT_QUOTES, 'UTF-8') . '<br>'
    . '<strong>Request:</strong> ' . htmlspecialchars($request_id, ENT_QUOTES, 'UTF-8') . '</p>'
    . '<h3>Message</h3>'
    . '<pre style="white-space:pre-wrap;">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</pre>'
    . '</body></html>';

$reply_html = '<!doctype html>'
    . '<html lang="en"><body style="margin:0;padding:0;background-color:#f8f3ee;">'
    . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f8f3ee;">'
    . '<tr><td align="center" style="padding:24px 12px;">'
    . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="640" style="max-width:640px;width:100%;background-color:#ffffff;border:2px solid #8b6b4a;border-radius:10px;">'
    . '<tr><td style="padding:18px 24px;background-color:#f6e8da;border-bottom:1px solid #c7aa8a;color:#5b3f27;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:700;line-height:1.4;">'
    . htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8')
    . '</td></tr>'
    . '<tr><td style="padding:24px;font-family:Arial,Helvetica,sans-serif;color:#2f2a26;">'
    . '<h1 style="margin:0 0 12px 0;font-size:26px;line-height:1.25;color:#2f2a26;">Thanks for reaching out, ' . htmlspecialchars($greeting_name, ENT_QUOTES, 'UTF-8') . '.</h1>'
    . '<p style="margin:0 0 14px 0;font-size:16px;line-height:1.6;">We received your message and our team will get back to you soon.</p>'
    . '<p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#5b3f27;"><strong>What happens next:</strong> a team member will review your note and follow up by email.</p>'
    . '<h2 style="margin:18px 0 10px 0;font-size:18px;line-height:1.3;color:#2f2a26;">Your message summary</h2>'
    . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid #d8c3ad;background-color:#fffaf5;">'
    . '<tr><td style="padding:10px 12px;border-bottom:1px solid #ead9c8;font-size:14px;line-height:1.5;color:#2f2a26;font-family:Arial,Helvetica,sans-serif;"><strong>Inquiry type:</strong> ' . htmlspecialchars($resolved_inquiry_type, ENT_QUOTES, 'UTF-8') . '</td></tr>'
    . '<tr><td style="padding:10px 12px;font-size:14px;line-height:1.5;color:#2f2a26;font-family:Arial,Helvetica,sans-serif;"><strong>Message preview:</strong> ' . htmlspecialchars($message_preview, ENT_QUOTES, 'UTF-8') . '</td></tr>'
    . '</table>'
    . '<p style="margin:18px 0 0 0;font-size:14px;line-height:1.6;color:#5b3f27;">Need to add details? Reply to this email and our team will attach your update.</p>'
    . '<p style="margin:20px 0 0 0;"><a href="' . htmlspecialchars($public_contact_url, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background-color:#b24a14;color:#ffffff;text-decoration:none;font-weight:700;padding:10px 14px;border-radius:6px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.2;">Contact ' . htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') . '</a></p>'
    . '</td></tr>'
    . '<tr><td style="padding:14px 24px;background-color:#f6e8da;border-top:1px solid #c7aa8a;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.5;color:#5b3f27;">Thank you for helping us keep things clean, calm, and dog-friendly.</td></tr>'
    . '</table></td></tr></table></body></html>';

$reply_text = $site_name . "\r\n"
    . "------------------------------\r\n"
    . "Hi {$greeting_name},\r\n\r\n"
    . "Thanks for contacting us. We received your message, and someone from our team will respond soon.\r\n\r\n"
    . "Your message summary:\r\n"
    . "Inquiry type: {$resolved_inquiry_type}\r\n"
    . "Message preview: {$message_preview}\r\n\r\n"
    . "If you want to add details, reply to this email.\r\n"
    . "Contact page: {$public_contact_url}\r\n\r\n"
    . "Thank you,\r\n"
    . $site_name . " Team\r\n"
    . "------------------------------\r\n";

$admin_native_headers = [
    "From: {$site_name} <{$from_email}>",
    "Reply-To: {$email}",
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'X-Mailer: PHP/' . PHP_VERSION,
    'Auto-Submitted: no',
];

$reply_native_headers = [
    "From: {$site_name} <{$from_email}>",
    "Reply-To: {$admin_email}",
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'X-Mailer: PHP/' . PHP_VERSION,
    'Auto-Submitted: auto-replied',
    'Precedence: bulk',
];

$phpmailer_status = $load_phpmailer();
$smtp_enabled = (bool) ($mail_config['smtp']['enabled'] ?? false);
$smtp_usable = $smtp_enabled && (bool) ($phpmailer_status['ok'] ?? false);
if ($smtp_enabled && !$smtp_usable) {
    $log_event('smtp_unavailable', [
        'reason' => (string) ($phpmailer_status['reason'] ?? 'unknown'),
    ]);
}

$send_with_best_available_transport = static function (
    string $channel,
    string $to,
    string $to_name,
    string $subject,
    string $html_body,
    string $text_body,
    string $reply_to_email,
    string $reply_to_name,
    array $native_headers,
    bool $smtp_ready,
    array $config,
    string $from_address,
    string $from_display_name,
    callable $send_smtp,
    callable $send_native,
    callable $logger,
    ?callable $recipient_masker = null
): array {
    if ($smtp_ready) {
        $smtp_result = $send_smtp(
            $channel,
            $to,
            $to_name,
            $subject,
            $html_body,
            $text_body,
            $reply_to_email,
            $reply_to_name,
            $config,
            $from_address,
            $from_display_name,
            $logger,
            $recipient_masker
        );

        if (($smtp_result['ok'] ?? false) === true) {
            return $smtp_result;
        }

        $logger('smtp_fallback_to_native', [
            'channel' => $channel,
            'reason' => (string) ($smtp_result['reason'] ?? 'smtp_failed'),
        ]);
    }

    return $send_native(
        $channel,
        $to,
        $subject,
        $html_body,
        $text_body,
        $native_headers,
        $from_address,
        $logger,
        $recipient_masker
    );
};

$admin_result = $send_with_best_available_transport(
    'admin_notification',
    $admin_email,
    'Admin',
    $admin_subject,
    $admin_html,
    $admin_text,
    $email,
    $name,
    $admin_native_headers,
    $smtp_usable,
    $mail_config,
    $from_email,
    $site_name,
    $send_via_phpmailer,
    $send_via_native_mail,
    $log_event
);

$reply_result = $send_with_best_available_transport(
    'user_autoreply',
    $email,
    $name,
    $reply_subject,
    $reply_html,
    $reply_text,
    $admin_email,
    $site_name . ' Team',
    $reply_native_headers,
    $smtp_usable,
    $mail_config,
    $from_email,
    $site_name,
    $send_via_phpmailer,
    $send_via_native_mail,
    $log_event,
    $mask_email
);

$admin_sent = (bool) ($admin_result['ok'] ?? false);
$auto_reply_sent = (bool) ($reply_result['ok'] ?? false);

if (!$auto_reply_sent) {
    $log_event('auto_reply_failed', [
        'masked_email' => $mask_email($email),
        'reason' => (string) ($reply_result['reason'] ?? 'unknown'),
        'transport' => (string) ($reply_result['transport'] ?? 'unknown'),
    ]);
}

if ($admin_sent) {
    $_SESSION['form_sent'] = true;
    $_SESSION['form_confirmation_sent'] = $auto_reply_sent;
} else {
    $_SESSION['form_error'] = true;
    $_SESSION['form_values'] = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'inquiry_type' => $inquiry_type,
        'message' => $message,
    ];
}

$duration_ms = (int) round((microtime(true) - $started_at) * 1000);
$log_event('submission_complete', [
    'admin_sent' => $admin_sent,
    'admin_transport' => (string) ($admin_result['transport'] ?? 'unknown'),
    'admin_reason' => (string) ($admin_result['reason'] ?? ''),
    'auto_reply_sent' => $auto_reply_sent,
    'auto_reply_transport' => (string) ($reply_result['transport'] ?? 'unknown'),
    'auto_reply_reason' => (string) ($reply_result['reason'] ?? ''),
    'duration_ms' => $duration_ms,
]);

header('Location: contact.php');
exit;
