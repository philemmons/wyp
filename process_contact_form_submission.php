<?php

declare(strict_types=1);

session_start();

$submissionRequestId = bin2hex(random_bytes(8));
$requestStartTime = microtime(true);

$defaultMailConfiguration = [
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

$mailConfigurationFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'contact_mail.php';
$fileBasedMailConfiguration = [];
if (is_file($mailConfigurationFilePath)) {
    $loadedMailConfiguration = require $mailConfigurationFilePath;
    if (is_array($loadedMailConfiguration)) {
        $fileBasedMailConfiguration = $loadedMailConfiguration;
    }
}
$mailConfiguration = array_replace_recursive($defaultMailConfiguration, $fileBasedMailConfiguration);

$siteDisplayName = (string) ($mailConfiguration['site']['name'] ?? 'Wipe Your Paws');
$publicContactPageUrl = (string) ($mailConfiguration['site']['url'] ?? 'https://wipeyourpaws.net/contact.php');
$adminRecipientEmail = (string) ($mailConfiguration['site']['admin_email'] ?? 'admin@wipeyourpaws.net');
$outboundFromEmail = (string) ($mailConfiguration['site']['from_email'] ?? 'noreply@wipeyourpaws.net');
$deliveryLogFilePath = (string) ($mailConfiguration['logging']['path'] ?? (__DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'contact-mail.log'));

$maskEmailAddress = static function (string $value): string {
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

$logContactFormEvent = static function (string $event, array $context = []) use ($submissionRequestId, $deliveryLogFilePath): void {
    $logDirectoryPath = dirname($deliveryLogFilePath);
    if (!is_dir($logDirectoryPath)) {
        @mkdir($logDirectoryPath, 0755, true);
    }

    $redactedContextKeys = [
        'contact_form_csrf_token',
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
    foreach ($redactedContextKeys as $key) {
        if (array_key_exists($key, $context)) {
            unset($context[$key]);
        }
    }

    $entry = [
        'ts' => gmdate('c'),
        'request_id' => $submissionRequestId,
        'event' => $event,
        'context' => $context,
    ];

    $line = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($line === false) {
        $line = '{"ts":"' . gmdate('c') . '","request_id":"' . $submissionRequestId . '","event":"json_encode_failed"}';
    }

    error_log($line);
    @file_put_contents($deliveryLogFilePath, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
};

$readPostField = static function (string $key): string {
    $value = $_POST[$key] ?? '';
    if (!is_string($value)) {
        return '';
    }

    return trim(str_replace("\0", '', $value));
};

$normalizeWhitespace = static function (string $value): string {
    return preg_replace('/\s+/u', ' ', $value) ?? $value;
};

$containsHeaderInjectionPayload = static function (string $value): bool {
    return preg_match('/\r|\n/', $value) === 1;
};

$resolveClientIpAddress = static function (): string {
    $ipCandidate = $_SERVER['REMOTE_ADDR'] ?? '';

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && is_string($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $forwarded = trim($parts[0] ?? '');
        if (filter_var($forwarded, FILTER_VALIDATE_IP)) {
            $ipCandidate = $forwarded;
        }
    }

    $validatedIpAddress = filter_var($ipCandidate, FILTER_VALIDATE_IP);
    return is_string($validatedIpAddress) ? $validatedIpAddress : 'unknown';
};

$sanitizeHeaderValue = static function (string $value): string {
    return str_replace(["\r", "\n"], '', trim($value));
};

$loadPhpMailer = static function (): array {
    $phpMailerClassName = 'PHPMailer\\PHPMailer\\PHPMailer';

    if (class_exists($phpMailerClassName)) {
        return ['ok' => true, 'reason' => 'already_loaded'];
    }

    $autoload = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    if (is_file($autoload)) {
        require_once $autoload;
        if (class_exists($phpMailerClassName)) {
            return ['ok' => true, 'reason' => 'composer_autoload'];
        }
    }

    $base = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
    $required = ['Exception.php', 'PHPMailer.php', 'SMTP.php'];
    $arePhpMailerFilesPresent = true;
    foreach ($required as $file) {
        if (!is_file($base . $file)) {
            $arePhpMailerFilesPresent = false;
            break;
        }
    }

    if ($arePhpMailerFilesPresent) {
        require_once $base . 'Exception.php';
        require_once $base . 'PHPMailer.php';
        require_once $base . 'SMTP.php';
        if (class_exists($phpMailerClassName)) {
            return ['ok' => true, 'reason' => 'manual_include'];
        }
    }

    return ['ok' => false, 'reason' => 'phpmailer_not_found'];
};

$sendViaNativeMailTransport = static function (
    string $channel,
    string $to,
    string $contactSubjectLine,
    string $htmlBody,
    string $plainTextBody,
    array $headers,
    string $envelopeSender,
    callable $logger,
    ?callable $recipientMasker = null
): array {
    $maskedRecipient = $recipientMasker ? $recipientMasker($to) : $to;
    $envelopeSenderParameter = '-f' . $envelopeSender;
    $mailBodyPayload = $plainTextBody;
    $effectiveHeaders = $headers;

    if (trim($htmlBody) !== '') {
        $boundary = 'wyp_' . bin2hex(random_bytes(12));
        $filtered = [];
        foreach ($headers as $header) {
            $normalized = strtolower(trim($header));
            if (str_starts_with($normalized, 'content-type:') || str_starts_with($normalized, 'mime-version:')) {
                continue;
            }
            $filtered[] = $header;
        }

        $effectiveHeaders = $filtered;
        $effectiveHeaders[] = 'MIME-Version: 1.0';
        $effectiveHeaders[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

        $mailBodyPayload = "--{$boundary}\r\n";
        $mailBodyPayload .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $mailBodyPayload .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $mailBodyPayload .= $plainTextBody . "\r\n";
        $mailBodyPayload .= "--{$boundary}\r\n";
        $mailBodyPayload .= "Content-Type: text/html; charset=UTF-8\r\n";
        $mailBodyPayload .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $mailBodyPayload .= $htmlBody . "\r\n";
        $mailBodyPayload .= "--{$boundary}--\r\n";
    }

    $headerBlob = implode("\r\n", $effectiveHeaders);

    $logger('native_mail_attempt', [
        'channel' => $channel,
        'to' => $maskedRecipient,
        'subject_len' => mb_strlen($contactSubjectLine),
        'body_len' => mb_strlen($mailBodyPayload),
        'header_count' => count($effectiveHeaders),
        'multipart' => trim($htmlBody) !== '',
        'has_envelope_sender' => true,
    ]);

    $errorBeforeSend = error_get_last();
    $ok = @mail($to, $contactSubjectLine, $mailBodyPayload, $headerBlob, $envelopeSenderParameter);
    $errorAfterSend = error_get_last();

    $warning = '';
    if ($errorAfterSend !== $errorBeforeSend && isset($errorAfterSend['message']) && is_string($errorAfterSend['message'])) {
        $warning = $errorAfterSend['message'];
    }

    $logger('native_mail_result', [
        'channel' => $channel,
        'to' => $maskedRecipient,
        'ok' => $ok,
        'attempt' => 'with_envelope_sender',
        'warning' => $warning,
    ]);

    if (!$ok) {
        $errorBeforeFallbackSend = error_get_last();
        $didFallbackSendSucceed = @mail($to, $contactSubjectLine, $mailBodyPayload, $headerBlob);
        $errorAfterFallbackSend = error_get_last();
        $fallbackWarningMessage = '';
        if ($errorAfterFallbackSend !== $errorBeforeFallbackSend && isset($errorAfterFallbackSend['message']) && is_string($errorAfterFallbackSend['message'])) {
            $fallbackWarningMessage = $errorAfterFallbackSend['message'];
        }

        $logger('native_mail_result', [
            'channel' => $channel,
            'to' => $maskedRecipient,
            'ok' => $didFallbackSendSucceed,
            'attempt' => 'without_envelope_sender',
            'warning' => $fallbackWarningMessage,
        ]);

        if ($didFallbackSendSucceed) {
            return ['ok' => true, 'transport' => 'native_mail_fallback_no_envelope', 'reason' => 'fallback_succeeded'];
        }
    }

    return [
        'ok' => $ok,
        'transport' => 'native_mail',
        'reason' => $ok ? '' : ($warning !== '' ? $warning : 'mail_returned_false'),
    ];
};

$sendViaSmtpTransport = static function (
    string $channel,
    string $to,
    string $recipientName,
    string $contactSubjectLine,
    string $htmlBody,
    string $plainTextBody,
    string $replyToEmail,
    string $replyToName,
    array $config,
    string $fromAddress,
    string $fromDisplayName,
    callable $logger,
    ?callable $recipientMasker = null
): array {
    $phpMailerClass = 'PHPMailer\\PHPMailer\\PHPMailer';

    try {
        $mail = new $phpMailerClass(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSMTP();
        $mail->Host = (string) ($config['smtp']['host'] ?? '');
        $mail->Port = (int) ($config['smtp']['port'] ?? 587);
        $mail->SMTPAuth = (bool) ($config['smtp']['auth'] ?? true);
        $mail->Username = (string) ($config['smtp']['username'] ?? '');
        $mail->Password = (string) ($config['smtp']['password'] ?? '');
        $mail->Timeout = (int) ($config['smtp']['timeout'] ?? 15);
        $mail->Sender = $fromAddress;

        $smtpDebugLevel = (int) ($config['smtp']['debug'] ?? 0);
        $mail->SMTPDebug = $smtpDebugLevel;
        $mail->Debugoutput = static function (string $str, int $level) use ($logger, $channel): void {
            $logger('smtp_debug', [
                'channel' => $channel,
                'level' => $level,
                'message' => mb_substr($str, 0, 800),
            ]);
        };

        $encryption = strtolower((string) ($config['smtp']['encryption'] ?? 'tls'));
        if ($encryption === 'tls') {
            $mail->SMTPSecure = $phpMailerClass::ENCRYPTION_STARTTLS;
        } elseif ($encryption === 'ssl') {
            $mail->SMTPSecure = $phpMailerClass::ENCRYPTION_SMTPS;
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

        $mail->setFrom($fromAddress, $fromDisplayName);
        $mail->addAddress($to, $recipientName);
        $mail->addReplyTo($replyToEmail, $replyToName);
        $mail->Subject = $contactSubjectLine;
        $mail->isHTML(true);
        $mail->Body = $htmlBody;
        $mail->AltBody = $plainTextBody;
        $mail->addCustomHeader('Auto-Submitted', $channel === 'user_autoreply' ? 'auto-replied' : 'no');
        $mail->addCustomHeader('X-Mailer', 'PHPMailer');

        $isDkimEnabled = (bool) ($config['dkim']['enabled'] ?? false);
        if ($isDkimEnabled) {
            $dkimPrivateKeyPath = (string) ($config['dkim']['private_key_path'] ?? '');
            if ($dkimPrivateKeyPath !== '' && is_file($dkimPrivateKeyPath)) {
                $mail->DKIM_domain = (string) ($config['dkim']['domain'] ?? '');
                $mail->DKIM_selector = (string) ($config['dkim']['selector'] ?? '');
                $mail->DKIM_private = $dkimPrivateKeyPath;
                $mail->DKIM_identity = (string) ($config['dkim']['identity'] ?? $fromAddress);
                $mail->DKIM_passphrase = (string) ($config['dkim']['passphrase'] ?? '');
            } else {
                $logger('dkim_skipped', [
                    'channel' => $channel,
                    'reason' => 'private_key_not_found',
                ]);
            }
        }

        $maskedRecipient = $recipientMasker ? $recipientMasker($to) : $to;
        $logger('smtp_attempt', [
            'channel' => $channel,
            'to' => $maskedRecipient,
            'host' => (string) ($config['smtp']['host'] ?? ''),
            'port' => (int) ($config['smtp']['port'] ?? 587),
            'encryption' => $encryption,
            'debug_level' => $smtpDebugLevel,
        ]);

        $ok = $mail->send();
        if ($ok) {
            $logger('smtp_result', [
                'channel' => $channel,
                'to' => $maskedRecipient,
                'ok' => true,
            ]);

            return ['ok' => true, 'transport' => 'phpmailer_smtp', 'reason' => ''];
        }

        $errorInfo = (string) $mail->ErrorInfo;
        $logger('smtp_result', [
            'channel' => $channel,
            'to' => $maskedRecipient,
            'ok' => false,
            'error_info' => $errorInfo,
        ]);

        return ['ok' => false, 'transport' => 'phpmailer_smtp', 'reason' => $errorInfo !== '' ? $errorInfo : 'smtp_send_failed'];
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
    $logContactFormEvent('invalid_method', ['method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown']);
    header('Location: contact.php');
    exit;
}

$clientIpAddress = $resolveClientIpAddress();
$userAgent = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
$logContactFormEvent('submission_received', [
    'ip' => $clientIpAddress,
    'ua_len' => mb_strlen($userAgent),
    'transport_preference' => (bool) ($mailConfiguration['smtp']['enabled'] ?? false) ? 'smtp_first' : 'native_mail_only',
]);

$submittedCsrfToken = $readPostField('contact_form_csrf_token');
$sessionCsrfToken = $_SESSION['contact_form_csrf_token'] ?? '';
if (
    $submittedCsrfToken === '' ||
    !is_string($sessionCsrfToken) ||
    $sessionCsrfToken === '' ||
    !hash_equals($sessionCsrfToken, $submittedCsrfToken)
) {
    $_SESSION['contact_form_submission_failed'] = true;
    $_SESSION['contact_form_csrf_token'] = bin2hex(random_bytes(32));

    $logContactFormEvent('csrf_failed', [
        'has_submitted_token' => $submittedCsrfToken !== '',
        'has_session_token' => is_string($sessionCsrfToken) && $sessionCsrfToken !== '',
    ]);

    header('Location: contact.php');
    exit;
}

$_SESSION['contact_form_csrf_token'] = bin2hex(random_bytes(32));

$rateLimitWindowSeconds = 600;
$maximumSubmissionsPerWindow = 5;
$currentUnixTimestamp = time();
$recentSubmissionTimestamps = $_SESSION['contact_form_submission_timestamps'] ?? [];
if (!is_array($recentSubmissionTimestamps)) {
    $recentSubmissionTimestamps = [];
}

$recentSubmissionTimestamps = array_values(array_filter(
    $recentSubmissionTimestamps,
    static fn ($ts): bool => is_int($ts) && ($currentUnixTimestamp - $ts) <= $rateLimitWindowSeconds
));

if (count($recentSubmissionTimestamps) >= $maximumSubmissionsPerWindow) {
    $_SESSION['contact_form_submission_failed'] = true;
    $_SESSION['contact_form_error_messages'] = ['Too many submissions from this session. Please wait a few minutes and try again.'];
    $logContactFormEvent('rate_limited', [
        'ip' => $clientIpAddress,
        'recent_count' => count($recentSubmissionTimestamps),
        'window_seconds' => $rateLimitWindowSeconds,
    ]);
    header('Location: contact.php');
    exit;
}

$recentSubmissionTimestamps[] = $currentUnixTimestamp;
$_SESSION['contact_form_submission_timestamps'] = $recentSubmissionTimestamps;

$honeypotValue = $readPostField('contact_website');
if ($honeypotValue !== '') {
    $_SESSION['contact_form_submission_succeeded'] = true;
    $_SESSION['contact_form_confirmation_sent'] = false;
    $logContactFormEvent('honeypot_triggered', ['ip' => $clientIpAddress]);
    header('Location: contact.php');
    exit;
}

$rawSenderName = $readPostField('name');
$rawSenderEmail = $readPostField('email');
$rawMessageSubject = $readPostField('subject');
$rawInquiryType = $readPostField('inquiry_type');
$rawMessageBody = $readPostField('message');

$senderName = $normalizeWhitespace(strip_tags($rawSenderName));
$senderEmail = strtolower($rawSenderEmail);
$contactSubjectLine = $normalizeWhitespace(strip_tags($rawMessageSubject));
$inquiryType = $normalizeWhitespace(strip_tags($rawInquiryType));
$messageBody = strip_tags($rawMessageBody);
$messageBody = preg_replace('/[^\P{C}\r\n\t]/u', '', $messageBody) ?? $messageBody;
$messageBody = trim($messageBody);

/** @var list<string> $validationErrors */
$validationErrors = [];
/** @var array<string, string> $fieldErrorsByField */
$fieldErrorsByField = [];
$addValidationError = static function (string $field, string $validationMessage) use (&$validationErrors, &$fieldErrorsByField): void {
    $validationErrors[] = $validationMessage;
    $fieldErrorsByField[$field] = $validationMessage;
};

if ($containsHeaderInjectionPayload($rawSenderEmail) || $containsHeaderInjectionPayload($rawSenderName) || $containsHeaderInjectionPayload($rawMessageSubject)) {
    $addValidationError('email', 'Invalid input detected in email headers.');
}

if ($senderName === '') {
    $addValidationError('name', 'Name is required. Please enter your full name.');
} elseif (mb_strlen($senderName) > 120) {
    $addValidationError('name', 'Name is too long. Use 120 characters or fewer.');
}

if ($senderEmail === '') {
    $addValidationError('email', 'Email address is required. Enter an email like name@example.com.');
} elseif (mb_strlen($senderEmail) > 254) {
    $addValidationError('email', 'Email address is too long. Use 254 characters or fewer.');
} elseif (preg_match('/\.\./', $senderEmail) === 1) {
    $addValidationError('email', 'Email format is not valid. Consecutive dots are not allowed.');
} elseif (!filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
    $addValidationError('email', 'Email format is not valid. Use a format like name@example.com.');
}

if (mb_strlen($contactSubjectLine) > 200) {
    $addValidationError('subject', 'Subject is too long. Use 200 characters or fewer.');
}

if (mb_strlen($inquiryType) > 80) {
    $addValidationError('inquiry_type', 'Inquiry type is too long. Use 80 characters or fewer.');
}

if ($messageBody === '') {
    $addValidationError('message', 'Message is required. Tell us how we can help.');
} elseif (mb_strlen($messageBody) < 10) {
    $addValidationError('message', 'Message is too short. Please add at least 10 characters.');
} elseif (mb_strlen($messageBody) > 5000) {
    $addValidationError('message', 'Message is too long. Limit your message to 5,000 characters.');
}

if (!empty($validationErrors)) {
    $_SESSION['contact_form_error_messages'] = $validationErrors;
    $_SESSION['contact_form_field_errors'] = $fieldErrorsByField;
    $_SESSION['contact_form_previous_values'] = [
        'name' => $senderName,
        'email' => $senderEmail,
        'subject' => $contactSubjectLine,
        'inquiry_type' => $inquiryType,
        'message' => $messageBody,
    ];

    $logContactFormEvent('validation_failed', [
        'ip' => $clientIpAddress,
        'error_count' => count($validationErrors),
        'fields' => array_keys($fieldErrorsByField),
        'masked_email' => $maskEmailAddress($senderEmail),
    ]);

    header('Location: contact.php');
    exit;
}

$logContactFormEvent('validation_passed', [
    'ip' => $clientIpAddress,
    'masked_email' => $maskEmailAddress($senderEmail),
]);

$resolvedInquiryType = $inquiryType !== '' ? $inquiryType : 'General inquiry';
$safeMessageSubject = $sanitizeHeaderValue($contactSubjectLine !== '' ? $contactSubjectLine : 'New message from contact form');
$adminNotificationSubject = '[wipeyourpaws.net] ' . $safeMessageSubject;
$confirmationSubject = 'We received your message - ' . $siteDisplayName;

$firstName = $senderName;
if (preg_match('/^\S+/u', $senderName, $firstNameMatch) === 1) {
    $firstName = $firstNameMatch[0];
}
$firstName = mb_substr($firstName, 0, 60);
$recipientGreetingName = $firstName !== '' ? $firstName : 'there';

$messagePreviewSource = preg_replace('/\s+/u', ' ', $messageBody) ?? $messageBody;
$messagePreview = trim(mb_substr($messagePreviewSource, 0, 220));
if (mb_strlen($messagePreviewSource) > 220) {
    $messagePreview .= '...';
}

$adminNotificationText = "You have a new message from the wipeyourpaws.net contact form.\r\n\r\n";
$adminNotificationText .= "------------------------------\r\n";
$adminNotificationText .= "Name    : {$senderName}\r\n";
$adminNotificationText .= "Email   : {$senderEmail}\r\n";
$adminNotificationText .= "Type    : {$resolvedInquiryType}\r\n";
$adminNotificationText .= "Subject : {$contactSubjectLine}\r\n";
$adminNotificationText .= "------------------------------\r\n\r\n";
$adminNotificationText .= "Message:\r\n{$messageBody}\r\n\r\n";
$adminNotificationText .= "------------------------------\r\n";
$adminNotificationText .= 'Sent    : ' . date('Y-m-d H:i:s T') . "\r\n";
$adminNotificationText .= "IP      : {$clientIpAddress}\r\n";
$adminNotificationText .= "Request : {$submissionRequestId}\r\n";

$adminNotificationHtml = '<!doctype html>'
    . '<html lang="en"><body style="font-family:Arial,Helvetica,sans-serif;color:#1f1f1f;">'
    . '<h2>New contact form submission</h2>'
    . '<p><strong>Name:</strong> ' . htmlspecialchars($senderName, ENT_QUOTES, 'UTF-8') . '<br>'
    . '<strong>Email:</strong> ' . htmlspecialchars($senderEmail, ENT_QUOTES, 'UTF-8') . '<br>'
    . '<strong>Type:</strong> ' . htmlspecialchars($resolvedInquiryType, ENT_QUOTES, 'UTF-8') . '<br>'
    . '<strong>Subject:</strong> ' . htmlspecialchars($contactSubjectLine, ENT_QUOTES, 'UTF-8') . '<br>'
    . '<strong>IP:</strong> ' . htmlspecialchars($clientIpAddress, ENT_QUOTES, 'UTF-8') . '<br>'
    . '<strong>Request:</strong> ' . htmlspecialchars($submissionRequestId, ENT_QUOTES, 'UTF-8') . '</p>'
    . '<h3>Message</h3>'
    . '<pre style="white-space:pre-wrap;">' . htmlspecialchars($messageBody, ENT_QUOTES, 'UTF-8') . '</pre>'
    . '</body></html>';

$confirmationHtml = '<!doctype html>'
    . '<html lang="en"><body style="margin:0;padding:0;background-color:#f8f3ee;">'
    . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f8f3ee;">'
    . '<tr><td align="center" style="padding:24px 12px;">'
    . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="640" style="max-width:640px;width:100%;background-color:#ffffff;border:2px solid #8b6b4a;border-radius:10px;">'
    . '<tr><td style="padding:18px 24px;background-color:#f6e8da;border-bottom:1px solid #c7aa8a;color:#5b3f27;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:700;line-height:1.4;">'
    . htmlspecialchars($siteDisplayName, ENT_QUOTES, 'UTF-8')
    . '</td></tr>'
    . '<tr><td style="padding:24px;font-family:Arial,Helvetica,sans-serif;color:#2f2a26;">'
    . '<h1 style="margin:0 0 12px 0;font-size:26px;line-height:1.25;color:#2f2a26;">Thanks for reaching out, ' . htmlspecialchars($recipientGreetingName, ENT_QUOTES, 'UTF-8') . '.</h1>'
    . '<p style="margin:0 0 14px 0;font-size:16px;line-height:1.6;">We received your message and our team will get back to you soon.</p>'
    . '<p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#5b3f27;"><strong>What happens next:</strong> a team member will review your note and follow up by email.</p>'
    . '<h2 style="margin:18px 0 10px 0;font-size:18px;line-height:1.3;color:#2f2a26;">Your message summary</h2>'
    . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid #d8c3ad;background-color:#fffaf5;">'
    . '<tr><td style="padding:10px 12px;border-bottom:1px solid #ead9c8;font-size:14px;line-height:1.5;color:#2f2a26;font-family:Arial,Helvetica,sans-serif;"><strong>Inquiry type:</strong> ' . htmlspecialchars($resolvedInquiryType, ENT_QUOTES, 'UTF-8') . '</td></tr>'
    . '<tr><td style="padding:10px 12px;font-size:14px;line-height:1.5;color:#2f2a26;font-family:Arial,Helvetica,sans-serif;"><strong>Message preview:</strong> ' . htmlspecialchars($messagePreview, ENT_QUOTES, 'UTF-8') . '</td></tr>'
    . '</table>'
    . '<p style="margin:18px 0 0 0;font-size:14px;line-height:1.6;color:#5b3f27;">Need to add details? Reply to this email and our team will attach your update.</p>'
    . '<p style="margin:20px 0 0 0;"><a href="' . htmlspecialchars($publicContactPageUrl, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background-color:#b24a14;color:#ffffff;text-decoration:none;font-weight:700;padding:10px 14px;border-radius:6px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.2;">Contact ' . htmlspecialchars($siteDisplayName, ENT_QUOTES, 'UTF-8') . '</a></p>'
    . '</td></tr>'
    . '<tr><td style="padding:14px 24px;background-color:#f6e8da;border-top:1px solid #c7aa8a;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.5;color:#5b3f27;">Thank you for helping us keep things clean, calm, and dog-friendly.</td></tr>'
    . '</table></td></tr></table></body></html>';

$confirmationText = $siteDisplayName . "\r\n"
    . "------------------------------\r\n"
    . "Hi {$recipientGreetingName},\r\n\r\n"
    . "Thanks for contacting us. We received your message, and someone from our team will respond soon.\r\n\r\n"
    . "Your message summary:\r\n"
    . "Inquiry type: {$resolvedInquiryType}\r\n"
    . "Message preview: {$messagePreview}\r\n\r\n"
    . "If you want to add details, reply to this email.\r\n"
    . "Contact page: {$publicContactPageUrl}\r\n\r\n"
    . "Thank you,\r\n"
    . $siteDisplayName . " Team\r\n"
    . "------------------------------\r\n";

$adminNotificationHeaders = [
    "From: {$siteDisplayName} <{$outboundFromEmail}>",
    "Reply-To: {$senderEmail}",
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'X-Mailer: PHP/' . PHP_VERSION,
    'Auto-Submitted: no',
];

$confirmationHeaders = [
    "From: {$siteDisplayName} <{$outboundFromEmail}>",
    "Reply-To: {$adminRecipientEmail}",
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'X-Mailer: PHP/' . PHP_VERSION,
    'Auto-Submitted: auto-replied',
    'Precedence: bulk',
];

$phpMailerAvailability = $loadPhpMailer();
$isSmtpEnabled = (bool) ($mailConfiguration['smtp']['enabled'] ?? false);
$isSmtpReady = $isSmtpEnabled && (bool) ($phpMailerAvailability['ok'] ?? false);
if ($isSmtpEnabled && !$isSmtpReady) {
    $logContactFormEvent('smtp_unavailable', [
        'reason' => (string) ($phpMailerAvailability['reason'] ?? 'unknown'),
    ]);
}

$sendWithPreferredMailTransport = static function (
    string $channel,
    string $to,
    string $recipientName,
    string $contactSubjectLine,
    string $htmlBody,
    string $plainTextBody,
    string $replyToEmail,
    string $replyToName,
    array $nativeMailHeaders,
    bool $smtpReady,
    array $config,
    string $fromAddress,
    string $fromDisplayName,
    callable $sendViaSmtp,
    callable $sendViaNative,
    callable $logger,
    ?callable $recipientMasker = null
): array {
    if ($smtpReady) {
        $smtpSendResult = $sendViaSmtp(
            $channel,
            $to,
            $recipientName,
            $contactSubjectLine,
            $htmlBody,
            $plainTextBody,
            $replyToEmail,
            $replyToName,
            $config,
            $fromAddress,
            $fromDisplayName,
            $logger,
            $recipientMasker
        );

        if (($smtpSendResult['ok'] ?? false) === true) {
            return $smtpSendResult;
        }

        $logger('smtp_fallback_to_native', [
            'channel' => $channel,
            'reason' => (string) ($smtpSendResult['reason'] ?? 'smtp_failed'),
        ]);
    }

    return $sendViaNative(
        $channel,
        $to,
        $contactSubjectLine,
        $htmlBody,
        $plainTextBody,
        $nativeMailHeaders,
        $fromAddress,
        $logger,
        $recipientMasker
    );
};

$adminDeliveryResult = $sendWithPreferredMailTransport(
    'admin_notification',
    $adminRecipientEmail,
    'Admin',
    $adminNotificationSubject,
    $adminNotificationHtml,
    $adminNotificationText,
    $senderEmail,
    $senderName,
    $adminNotificationHeaders,
    $isSmtpReady,
    $mailConfiguration,
    $outboundFromEmail,
    $siteDisplayName,
    $sendViaSmtpTransport,
    $sendViaNativeMailTransport,
    $logContactFormEvent
);

$confirmationDeliveryResult = $sendWithPreferredMailTransport(
    'user_autoreply',
    $senderEmail,
    $senderName,
    $confirmationSubject,
    $confirmationHtml,
    $confirmationText,
    $adminRecipientEmail,
    $siteDisplayName . ' Team',
    $confirmationHeaders,
    $isSmtpReady,
    $mailConfiguration,
    $outboundFromEmail,
    $siteDisplayName,
    $sendViaSmtpTransport,
    $sendViaNativeMailTransport,
    $logContactFormEvent,
    $maskEmailAddress
);

$wasAdminDeliverySuccessful = (bool) ($adminDeliveryResult['ok'] ?? false);
$wasConfirmationDeliverySuccessful = (bool) ($confirmationDeliveryResult['ok'] ?? false);

if (!$wasConfirmationDeliverySuccessful) {
    $logContactFormEvent('auto_reply_failed', [
        'masked_email' => $maskEmailAddress($senderEmail),
        'reason' => (string) ($confirmationDeliveryResult['reason'] ?? 'unknown'),
        'transport' => (string) ($confirmationDeliveryResult['transport'] ?? 'unknown'),
    ]);
}

if ($wasAdminDeliverySuccessful) {
    $_SESSION['contact_form_submission_succeeded'] = true;
    $_SESSION['contact_form_confirmation_sent'] = $wasConfirmationDeliverySuccessful;
} else {
    $_SESSION['contact_form_submission_failed'] = true;
    $_SESSION['contact_form_previous_values'] = [
        'name' => $senderName,
        'email' => $senderEmail,
        'subject' => $contactSubjectLine,
        'inquiry_type' => $inquiryType,
        'message' => $messageBody,
    ];
}

$processingDurationMilliseconds = (int) round((microtime(true) - $requestStartTime) * 1000);
$logContactFormEvent('submission_complete', [
    'admin_sent' => $wasAdminDeliverySuccessful,
    'admin_transport' => (string) ($adminDeliveryResult['transport'] ?? 'unknown'),
    'admin_reason' => (string) ($adminDeliveryResult['reason'] ?? ''),
    'auto_reply_sent' => $wasConfirmationDeliverySuccessful,
    'auto_reply_transport' => (string) ($confirmationDeliveryResult['transport'] ?? 'unknown'),
    'auto_reply_reason' => (string) ($confirmationDeliveryResult['reason'] ?? ''),
    'duration_ms' => $processingDurationMilliseconds,
]);

header('Location: contact.php');
exit;
