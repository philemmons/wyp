<?php

declare(strict_types=1);

/**
 * Example production PHPMailer factory.
 * Copy/adapt into your application service layer.
 */

use PHPMailer\PHPMailer\PHPMailer;

/**
 * @param array<string,mixed> $mail_config
 */
function buildProductionMailer(array $mail_config): PHPMailer
{
    $site_name = (string) ($mail_config['site']['name'] ?? 'Website');
    $from_email = (string) ($mail_config['site']['from_email'] ?? 'noreply@example.com');

    $smtp_host = (string) ($mail_config['smtp']['host'] ?? '');
    $smtp_port = (int) ($mail_config['smtp']['port'] ?? 587);
    $smtp_auth = (bool) ($mail_config['smtp']['auth'] ?? true);
    $smtp_user = (string) ($mail_config['smtp']['username'] ?? '');
    $smtp_pass = (string) ($mail_config['smtp']['password'] ?? '');
    $smtp_timeout = (int) ($mail_config['smtp']['timeout'] ?? 15);
    $smtp_encryption = strtolower((string) ($mail_config['smtp']['encryption'] ?? 'tls'));

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->Port = $smtp_port;
    $mail->SMTPAuth = $smtp_auth;
    $mail->Username = $smtp_user;
    $mail->Password = $smtp_pass;
    $mail->Timeout = $smtp_timeout;
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPDebug = 0;
    $mail->Sender = $from_email;
    $mail->setFrom($from_email, $site_name);

    if ($smtp_encryption === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($smtp_encryption === 'tls') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = '';
    }

    $dkim_enabled = (bool) ($mail_config['dkim']['enabled'] ?? false);
    if ($dkim_enabled) {
        $dkim_private = (string) ($mail_config['dkim']['private_key_path'] ?? '');
        if ($dkim_private !== '' && is_file($dkim_private)) {
            $mail->DKIM_domain = (string) ($mail_config['dkim']['domain'] ?? '');
            $mail->DKIM_selector = (string) ($mail_config['dkim']['selector'] ?? '');
            $mail->DKIM_private = $dkim_private;
            $mail->DKIM_identity = (string) ($mail_config['dkim']['identity'] ?? $from_email);
            $mail->DKIM_passphrase = (string) ($mail_config['dkim']['passphrase'] ?? '');
        }
    }

    $mail->addCustomHeader('X-Mailer', 'PHPMailer');
    return $mail;
}
