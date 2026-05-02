<?php

declare(strict_types=1);

/**
 * Example production PHPMailer factory.
 * Copy/adapt into your application service layer.
 */

use PHPMailer\PHPMailer\PHPMailer;

/**
 * @param array<string,mixed> $mailConfiguration
 */
function createProductionMailer(array $mailConfiguration): PHPMailer
{
    $siteDisplayName = (string) ($mailConfiguration['site']['name'] ?? 'Website');
    $fromEmailAddress = (string) ($mailConfiguration['site']['from_email'] ?? 'noreply@example.com');

    $smtpHost = (string) ($mailConfiguration['smtp']['host'] ?? '');
    $smtpPort = (int) ($mailConfiguration['smtp']['port'] ?? 587);
    $smtpAuthEnabled = (bool) ($mailConfiguration['smtp']['auth'] ?? true);
    $smtpUsername = (string) ($mailConfiguration['smtp']['username'] ?? '');
    $smtpPassword = (string) ($mailConfiguration['smtp']['password'] ?? '');
    $smtpTimeoutSeconds = (int) ($mailConfiguration['smtp']['timeout'] ?? 15);
    $smtpEncryption = strtolower((string) ($mailConfiguration['smtp']['encryption'] ?? 'tls'));

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->Port = $smtpPort;
    $mail->SMTPAuth = $smtpAuthEnabled;
    $mail->Username = $smtpUsername;
    $mail->Password = $smtpPassword;
    $mail->Timeout = $smtpTimeoutSeconds;
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPDebug = 0;
    $mail->Sender = $fromEmailAddress;
    $mail->setFrom($fromEmailAddress, $siteDisplayName);

    if ($smtpEncryption === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($smtpEncryption === 'tls') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = '';
    }

    $isDkimEnabled = (bool) ($mailConfiguration['dkim']['enabled'] ?? false);
    if ($isDkimEnabled) {
        $dkimPrivateKeyPath = (string) ($mailConfiguration['dkim']['private_key_path'] ?? '');
        if ($dkimPrivateKeyPath !== '' && is_file($dkimPrivateKeyPath)) {
            $mail->DKIM_domain = (string) ($mailConfiguration['dkim']['domain'] ?? '');
            $mail->DKIM_selector = (string) ($mailConfiguration['dkim']['selector'] ?? '');
            $mail->DKIM_private = $dkimPrivateKeyPath;
            $mail->DKIM_identity = (string) ($mailConfiguration['dkim']['identity'] ?? $fromEmailAddress);
            $mail->DKIM_passphrase = (string) ($mailConfiguration['dkim']['passphrase'] ?? '');
        }
    }

    $mail->addCustomHeader('X-Mailer', 'PHPMailer');
    return $mail;
}

