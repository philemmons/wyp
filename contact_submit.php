<?php

/**
 * contact_submit.php - Form handler
 * wipeyourpaws.net - PHP 8.5 - WCAG 2.1 AA rev.2
 *
 * Bug fixes applied:
 *   B1 - Honeypot check moved BEFORE validation
 *   B2 - Reply-To header sanitized against email header injection
 *   B3 - htmlspecialchars() removed from plain-text email subject
 *   B4 - Flash messages and form values stored in session, NOT URL GET params
 *   R1 - CSRF token validated with hash_equals() (timing-safe comparison)
 */

session_start();

// Only process POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

// R1: CSRF validation
$submitted_token = $_POST['csrf_token'] ?? '';
$session_token = $_SESSION['csrf_token'] ?? '';

if (
    empty($submitted_token) ||
    empty($session_token) ||
    !hash_equals($session_token, $submitted_token)
) {
    $_SESSION['form_error'] = true;
    // Regenerate token to prevent reuse after failure
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    header('Location: contact.php');
    exit;
}

// Regenerate CSRF token after each valid submission (prevents reuse)
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// B1: Honeypot check FIRST - before any validation
// Bots that fill hidden fields are silently redirected (appear to succeed)
if (!empty($_POST['website'] ?? '')) {
    $_SESSION['form_sent'] = true;
    header('Location: contact.php');
    exit;
}

// Collect and sanitize inputs.
$post_string = static function (string $key): string {
    $value = $_POST[$key] ?? '';

    // Defend against array/object injection from crafted requests.
    if (!is_string($value)) {
        return '';
    }

    return trim(str_replace("\0", '', $value));
};

$normalize_whitespace = static function (string $value): string {
    return preg_replace('/\s+/u', ' ', $value) ?? $value;
};

$name_raw = $post_string('name');
$email_raw = $post_string('email');
$subject_raw = $post_string('subject');
$message_raw = $post_string('message');

$name = $normalize_whitespace(strip_tags($name_raw));
$email = strtolower($email_raw);
$subject = $normalize_whitespace(strip_tags($subject_raw));

// Keep line breaks/tabs in message for readability; strip tags/control chars.
$message = strip_tags($message_raw);
$message = preg_replace('/[^\P{C}\r\n\t]/u', '', $message) ?? $message;
$message = trim($message);

// Validation
/** @var list<string> $errors */
$errors = [];
/** @var array<string, string> $field_errors */
$field_errors = [];

$add_error = static function (int|string $field, string $message) use (&$errors, &$field_errors): void {
    $errors[] = $message;
    $field_errors[(string) $field] = $message;
};

if (empty($name)) {
    $add_error('name', 'Name is required. Please enter your full name.');
} elseif (mb_strlen($name) > 120) {
    $add_error('name', 'Name is too long. Use 120 characters or fewer.');
}

if (empty($email)) {
    $add_error('email', 'Email address is required. Enter an email like name@example.com.');
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $add_error('email', 'Email format is not valid. Use a format like name@example.com.');
} elseif (mb_strlen($email) > 254) {
    $add_error('email', 'Email address is too long. Use 254 characters or fewer.');
}

if (mb_strlen($subject) > 200) {
    $add_error('subject', 'Subject is too long. Use 200 characters or fewer.');
}

if (empty($message)) {
    $add_error('message', 'Message is required. Tell us how we can help.');
} elseif (mb_strlen($message) < 10) {
    $add_error('message', 'Message is too short. Please add at least 10 characters.');
} elseif (mb_strlen($message) > 5000) {
    $add_error('message', 'Message is too long. Limit your message to 5,000 characters.');
}

// B4: Store errors + form values in session, NOT GET URL params
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_field_errors'] = $field_errors;
    $_SESSION['form_values'] = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
    ];
    header('Location: contact.php');
    exit;
}

// Build email
$admin_email = 'admin@wipeyourpaws.net';

// B3 FIX: Use $subject directly in plain-text email - htmlspecialchars()
// converts & to &amp; which is wrong in a plain-text email body.
$email_subject = '[wipeyourpaws.net] ' . (!empty($subject) ? $subject : 'New message from contact form');

$email_body = "You have a new message from the wipeyourpaws.net contact form.\n\n";
$email_body .= "------------------------------\n";
$email_body .= "Name    : {$name}\n";
$email_body .= "Email   : {$email}\n";
$email_body .= "Subject : {$subject}\n";
$email_body .= "------------------------------\n\n";
$email_body .= "Message:\n{$message}\n\n";
$email_body .= "------------------------------\n";
$email_body .= 'Sent    : ' . date('Y-m-d H:i:s T') . "\n";

$ip_candidate = $_SERVER['REMOTE_ADDR'] ?? '';
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && is_string($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $forwarded = trim($parts[0] ?? '');
    if (filter_var($forwarded, FILTER_VALIDATE_IP)) {
        $ip_candidate = $forwarded;
    }
}

$safe_ip = filter_var($ip_candidate, FILTER_VALIDATE_IP) ?: 'unknown';
$email_body .= "IP      : {$safe_ip}\n";

// B2 FIX: Sanitize the Reply-To header value to prevent email header injection.
// Strip any carriage-return or newline characters that could inject extra headers.
$safe_email = str_replace(["\r", "\n", "\t"], '', $email);

$headers = "From: noreply@wipeyourpaws.net\r\n";
$headers .= "Reply-To: {$safe_email}\r\n";
$headers .= 'X-Mailer: PHP/' . PHP_VERSION . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "MIME-Version: 1.0\r\n";

// Also sanitize the subject line against header injection.
$safe_subject = str_replace(["\r", "\n"], '', $email_subject);

// Send
// Suppress PHP mail() warnings; rely on return value for success detection.
// For production, replace with PHPMailer + SMTP for reliable delivery.
$sent = @mail($admin_email, $safe_subject, $email_body, $headers);

// B4: Store result in session, redirect (PRG pattern)
if ($sent) {
    $_SESSION['form_sent'] = true;
} else {
    $_SESSION['form_error'] = true;
    // Preserve form values so user doesn't have to retype.
    $_SESSION['form_values'] = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
    ];
}

header('Location: contact.php');
exit;
