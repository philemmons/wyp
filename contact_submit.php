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
$inquiry_type_raw = $post_string('inquiry_type');
$subject_raw = $post_string('subject');
$message_raw = $post_string('message');

$name = $normalize_whitespace(strip_tags($name_raw));
$email = strtolower($email_raw);
$inquiry_type = $normalize_whitespace(strip_tags($inquiry_type_raw));
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

if (mb_strlen($inquiry_type) > 80) {
    $add_error('inquiry_type', 'Inquiry type is too long. Use 80 characters or fewer.');
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
        'inquiry_type' => $inquiry_type,
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
$email_body .= "Type    : " . (!empty($inquiry_type) ? $inquiry_type : 'General inquiry') . "\n";
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

// Build and send user auto-reply (multipart: plain text + HTML).
$first_name = $name;
if (preg_match('/^\S+/u', $name, $name_match) === 1) {
    $first_name = $name_match[0];
}
$first_name = mb_substr($first_name, 0, 60);
$greeting_name = $first_name !== '' ? $first_name : 'there';

$resolved_inquiry_type = !empty($inquiry_type)
    ? $inquiry_type
    : (!empty($subject) ? $subject : 'General inquiry');

$message_compact = preg_replace('/\s+/u', ' ', $message) ?? $message;
$message_preview = trim(mb_substr($message_compact, 0, 220));
if (mb_strlen($message_compact) > 220) {
    $message_preview .= '...';
}

$reply_subject = "🐾 We received your message — Wipe Your Paws";
$safe_reply_subject = str_replace(["\r", "\n"], '', $reply_subject);
$encoded_reply_subject = mb_encode_mimeheader($safe_reply_subject, 'UTF-8');

$first_name_html = htmlspecialchars($greeting_name, ENT_QUOTES, 'UTF-8');
$inquiry_type_html = htmlspecialchars($resolved_inquiry_type, ENT_QUOTES, 'UTF-8');
$message_preview_html = htmlspecialchars($message_preview, ENT_QUOTES, 'UTF-8');

$reply_html = '<!doctype html>'
    . '<html lang="en"><body style="margin:0;padding:0;background-color:#f8f3ee;">'
    . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f8f3ee;">'
    . '<tr><td align="center" style="padding:24px 12px;">'
    . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="640" style="max-width:640px;width:100%;background-color:#ffffff;border:2px solid #8b6b4a;border-radius:10px;">'
    . '<tr><td style="padding:18px 24px;background-color:#f6e8da;border-bottom:1px solid #c7aa8a;color:#5b3f27;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:700;line-height:1.4;">🐾 Wipe Your Paws</td></tr>'
    . '<tr><td style="padding:24px;font-family:Arial,Helvetica,sans-serif;color:#2f2a26;">'
    . '<h1 style="margin:0 0 12px 0;font-size:26px;line-height:1.25;color:#2f2a26;">Thanks for reaching out, ' . $first_name_html . '.</h1>'
    . '<p style="margin:0 0 14px 0;font-size:16px;line-height:1.6;">We received your message and our team will get back to you soon.</p>'
    . '<p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#5b3f27;"><strong>What happens next:</strong> a team member will review your note and follow up by email.</p>'
    . '<h2 style="margin:18px 0 10px 0;font-size:18px;line-height:1.3;color:#2f2a26;">Your message summary</h2>'
    . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid #d8c3ad;background-color:#fffaf5;">'
    . '<tr><td style="padding:10px 12px;border-bottom:1px solid #ead9c8;font-size:14px;line-height:1.5;color:#2f2a26;font-family:Arial,Helvetica,sans-serif;"><strong>Inquiry type:</strong> ' . $inquiry_type_html . '</td></tr>'
    . '<tr><td style="padding:10px 12px;font-size:14px;line-height:1.5;color:#2f2a26;font-family:Arial,Helvetica,sans-serif;"><strong>Message preview:</strong> ' . $message_preview_html . '</td></tr>'
    . '</table>'
    . '<p style="margin:18px 0 0 0;font-size:14px;line-height:1.6;color:#5b3f27;">Need to add details? Reply to this email and our team will attach your update.</p>'
    . '<p style="margin:20px 0 0 0;"><a href="https://wipeyourpaws.net/contact.php" style="display:inline-block;background-color:#b24a14;color:#ffffff;text-decoration:none;font-weight:700;padding:10px 14px;border-radius:6px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.2;" aria-label="Open the Wipe Your Paws contact page to send another message">Contact Wipe Your Paws</a></p>'
    . '</td></tr>'
    . '<tr><td style="padding:14px 24px;background-color:#f6e8da;border-top:1px solid #c7aa8a;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.5;color:#5b3f27;">🐾 Thank you for helping us keep things clean, calm, and dog-friendly.</td></tr>'
    . '</table></td></tr></table></body></html>';

$reply_text = "Wipe Your Paws\n"
    . "------------------------------\n"
    . "Hi {$greeting_name},\n\n"
    . "Thanks for contacting us. We received your message, and someone from our team will respond soon.\n\n"
    . "Your message summary:\n"
    . "Inquiry type: {$resolved_inquiry_type}\n"
    . "Message preview: {$message_preview}\n\n"
    . "If you want to add details, reply to this email.\n"
    . "Contact page: https://wipeyourpaws.net/contact.php\n\n"
    . "Thank you,\n"
    . "Wipe Your Paws Team\n"
    . "------------------------------\n";

$boundary = 'wyp_' . bin2hex(random_bytes(12));
$reply_headers = "From: Wipe Your Paws <noreply@wipeyourpaws.net>\r\n";
$reply_headers .= "Reply-To: admin@wipeyourpaws.net\r\n";
$reply_headers .= "MIME-Version: 1.0\r\n";
$reply_headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";

$reply_body = "--{$boundary}\r\n";
$reply_body .= "Content-Type: text/plain; charset=UTF-8\r\n";
$reply_body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$reply_body .= $reply_text . "\r\n";
$reply_body .= "--{$boundary}\r\n";
$reply_body .= "Content-Type: text/html; charset=UTF-8\r\n";
$reply_body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$reply_body .= $reply_html . "\r\n";
$reply_body .= "--{$boundary}--\r\n";

$auto_reply_sent = @mail($email, $encoded_reply_subject, $reply_body, $reply_headers);
if (!$auto_reply_sent) {
    error_log('Auto-reply failed to send for contact form submission.');
}

// B4: Store result in session, redirect (PRG pattern)
if ($sent) {
    $_SESSION['form_sent'] = true;
} else {
    $_SESSION['form_error'] = true;
    // Preserve form values so user doesn't have to retype.
    $_SESSION['form_values'] = [
        'name' => $name,
        'email' => $email,
        'inquiry_type' => $inquiry_type,
        'subject' => $subject,
        'message' => $message,
    ];
}

header('Location: contact.php');
exit;
