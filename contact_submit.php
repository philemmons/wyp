<?php
/**
 * contact_submit.php — Form handler
 * wipeyourpaws.net · PHP 8.5 · WCAG 2.1 AA rev.2
 *
 * Bug fixes applied:
 *   B1 — Honeypot check moved BEFORE validation
 *   B2 — Reply-To header sanitised against email header injection
 *   B3 — htmlspecialchars() removed from plain-text email subject
 *   B4 — Flash messages and form values stored in session, NOT URL GET params
 *   R1 — CSRF token validated with hash_equals() (timing-safe comparison)
 */

session_start();

// ── Only process POST ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

// ── R1: CSRF validation ───────────────────────────────────────────────
$submitted_token = $_POST['csrf_token'] ?? '';
$session_token   = $_SESSION['csrf_token'] ?? '';

if (
    empty($submitted_token) ||
    empty($session_token)   ||
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

// ── B1: Honeypot check FIRST — before any validation ──────────────────
// Bots that fill hidden fields are silently redirected (appear to succeed)
if (!empty($_POST['website'] ?? '')) {
    $_SESSION['form_sent'] = true;
    header('Location: contact.php');
    exit;
}

// ── Collect & sanitise inputs ──────────────────────────────────────────
$name    = trim(strip_tags($_POST['name']    ?? ''));
$email   = trim(strip_tags($_POST['email']   ?? ''));
$subject = trim(strip_tags($_POST['subject'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

// ── Validation ────────────────────────────────────────────────────────
$errors = [];

if (empty($name)) {
    $errors[] = 'Please enter your name.';
} elseif (mb_strlen($name) > 120) {
    $errors[] = 'Your name must be 120 characters or fewer.';
}

if (empty($email)) {
    $errors[] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please provide a valid email address.';
} elseif (mb_strlen($email) > 254) {
    $errors[] = 'Email address is too long.';
}

if (empty($message)) {
    $errors[] = 'Please enter a message.';
} elseif (mb_strlen($message) < 10) {
    $errors[] = 'Your message is a little short — please add a bit more detail.';
} elseif (mb_strlen($message) > 5000) {
    $errors[] = 'Your message exceeds the maximum length of 5,000 characters.';
}

// ── B4: Store errors + form values in session, NOT GET URL params ──────
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_values'] = [
        'name'    => $name,
        'email'   => $email,
        'subject' => $subject,
        'message' => $message,
    ];
    header('Location: contact.php');
    exit;
}

// ── Build email ────────────────────────────────────────────────────────
$admin_email = 'admin@wipeyourpaws.net';

// B3 FIX: Use $subject directly in plain-text email — htmlspecialchars()
// converts & → &amp; which is wrong in a plain-text email body
$email_subject = '[wipeyourpaws.net] ' . (!empty($subject) ? $subject : 'New message from contact form');

$email_body  = "You have a new message from the wipeyourpaws.net contact form.\n\n";
$email_body .= "─────────────────────────────────────────\n";
$email_body .= "Name    : {$name}\n";
$email_body .= "Email   : {$email}\n";
$email_body .= "Subject : {$subject}\n";
$email_body .= "─────────────────────────────────────────\n\n";
$email_body .= "Message:\n{$message}\n\n";
$email_body .= "─────────────────────────────────────────\n";
$email_body .= "Sent    : " . date('Y-m-d H:i:s T') . "\n";
$email_body .= "IP      : " . filter_var(
    $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    FILTER_VALIDATE_IP
) . "\n";

// B2 FIX: Sanitise the Reply-To header value to prevent email header injection.
// Strip any carriage-return or newline characters that could inject extra headers.
$safe_email = str_replace(["\r", "\n", "\t"], '', $email);

$headers  = "From: noreply@wipeyourpaws.net\r\n";
$headers .= "Reply-To: {$safe_email}\r\n";
$headers .= "X-Mailer: PHP/" . PHP_VERSION . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "MIME-Version: 1.0\r\n";

// Also sanitise the subject line against header injection
$safe_subject = str_replace(["\r", "\n"], '', $email_subject);

// ── Send ───────────────────────────────────────────────────────────────
// Suppress PHP mail() warnings; rely on return value for success detection.
// For production, replace with PHPMailer + SMTP for reliable delivery.
$sent = @mail($admin_email, $safe_subject, $email_body, $headers);

// ── B4: Store result in session, redirect (PRG pattern) ───────────────
if ($sent) {
    $_SESSION['form_sent'] = true;
} else {
    $_SESSION['form_error'] = true;
    // Preserve form values so user doesn't have to retype
    $_SESSION['form_values'] = [
        'name'    => $name,
        'email'   => $email,
        'subject' => $subject,
        'message' => $message,
    ];
}

header('Location: contact.php');
exit;
