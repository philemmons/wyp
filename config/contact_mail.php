<?php

declare(strict_types=1);

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
        'enabled' => getenv('WYP_SMTP_ENABLED') === '1',
        'host' => (string) (getenv('WYP_SMTP_HOST') ?: ''),
        'port' => (int) (getenv('WYP_SMTP_PORT') ?: 587),
        'encryption' => (string) (getenv('WYP_SMTP_ENCRYPTION') ?: 'tls'), // tls|ssl|none
        'auth' => getenv('WYP_SMTP_AUTH') !== '0',
        'username' => (string) (getenv('WYP_SMTP_USERNAME') ?: ''),
        'password' => (string) (getenv('WYP_SMTP_PASSWORD') ?: ''),
        'timeout' => (int) (getenv('WYP_SMTP_TIMEOUT') ?: 15),
        'debug' => (int) (getenv('WYP_SMTP_DEBUG') ?: 0), // 0 for production
        'allow_self_signed' => getenv('WYP_SMTP_ALLOW_SELF_SIGNED') === '1',
    ],
    'dkim' => [
        'enabled' => getenv('WYP_DKIM_ENABLED') === '1',
        'domain' => (string) (getenv('WYP_DKIM_DOMAIN') ?: 'wipeyourpaws.net'),
        'selector' => (string) (getenv('WYP_DKIM_SELECTOR') ?: ''),
        'private_key_path' => (string) (getenv('WYP_DKIM_PRIVATE_KEY_PATH') ?: ''),
        'identity' => (string) (getenv('WYP_DKIM_IDENTITY') ?: ''),
        'passphrase' => (string) (getenv('WYP_DKIM_PASSPHRASE') ?: ''),
    ],
    'logging' => [
        'path' => __DIR__ . '/../logs/contact-mail.log',
    ],
];
