<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

/**
 * Contact mail configuration for contact form delivery.
 *
 * Use environment variables in production to avoid storing secrets in code.
 * Example:
 *   WYP_SMTP_ENABLED=1
 *   WYP_SMTP_HOST=smtp.yourprovider.com
 *   WYP_SMTP_PORT=587
 *   WYP_SMTP_ENCRYPTION=tls
 *   WYP_SMTP_AUTH=1
 *   WYP_SMTP_USERNAME=noreply@wipeyourpaws.net
 *   WYP_SMTP_PASSWORD=your_app_password
 */
return [
    'site' => [
        'name' => 'Wipe Your Paws',
        'url' => 'https://wipeyourpaws.net/contact.php',
        'admin_email' => 'admin@wipeyourpaws.net',
        'from_email' => 'noreply@wipeyourpaws.net',
    ],
    'smtp' => [
        'enabled' => wyp_env('WYP_SMTP_ENABLED') === '1',
        'host' => wyp_env('WYP_SMTP_HOST'),
        'port' => (int) (wyp_env('WYP_SMTP_PORT', '587')),
        'encryption' => wyp_env('WYP_SMTP_ENCRYPTION', 'tls'), // tls|ssl|none
        'auth' => wyp_env('WYP_SMTP_AUTH', '1') !== '0',
        'username' => wyp_env('WYP_SMTP_USERNAME'),
        'password' => wyp_env('WYP_SMTP_PASSWORD'),
        'timeout' => (int) (wyp_env('WYP_SMTP_TIMEOUT', '15')),
        'debug' => (int) (wyp_env('WYP_SMTP_DEBUG', '0')), // 0 for production
        'allow_self_signed' => wyp_env('WYP_SMTP_ALLOW_SELF_SIGNED') === '1',
    ],
    'dkim' => [
        'enabled' => wyp_env('WYP_DKIM_ENABLED') === '1',
        'domain' => wyp_env('WYP_DKIM_DOMAIN', 'wipeyourpaws.net'),
        'selector' => wyp_env('WYP_DKIM_SELECTOR'),
        'private_key_path' => wyp_env('WYP_DKIM_PRIVATE_KEY_PATH'),
        'identity' => wyp_env('WYP_DKIM_IDENTITY'),
        'passphrase' => wyp_env('WYP_DKIM_PASSPHRASE'),
    ],
    'logging' => [
        'path' => __DIR__ . '/../logs/contact-mail.log',
    ],
];