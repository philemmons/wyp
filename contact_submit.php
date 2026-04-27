<?php
/**
 * contact_submit.php — Form handler with confirmation step
 * wipeyourpaws.net · PHP 8.5 · WCAG 2.2 AAA
 */
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: contact.php');
  exit;
}

$submitted_token = $_POST['csrf_token'] ?? '';
$session_token   = $_SESSION['csrf_token'] ?? '';

if (
  empty($submitted_token) ||
  empty($session_token) ||
  !hash_equals($session_token, $submitted_token)
) {
  $_SESSION['form_error'] = true;
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  header('Location: contact.php');
  exit;
}

// Rotate token after each valid POST.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$is_confirm = isset($_POST['confirm_send']);

if (!$is_confirm) {
  // Honeypot check first.
  if (!empty($_POST['website'] ?? '')) {
    $_SESSION['form_sent'] = true;
    header('Location: contact.php');
    exit;
  }

  $name    = trim(strip_tags($_POST['name'] ?? ''));
  $email   = trim(strip_tags($_POST['email'] ?? ''));
  $subject = trim(strip_tags($_POST['subject'] ?? ''));
  $message = trim(strip_tags($_POST['message'] ?? ''));

  $errors = [];

  if ($name === '') {
    $errors[] = 'Please enter your name.';
  } elseif (mb_strlen($name) > 120) {
    $errors[] = 'Your name must be 120 characters or fewer.';
  }

  if ($email === '') {
    $errors[] = 'Email address is required.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please provide a valid email address.';
  } elseif (mb_strlen($email) > 254) {
    $errors[] = 'Email address is too long.';
  }

  if ($message === '') {
    $errors[] = 'Please enter a message.';
  } elseif (mb_strlen($message) < 10) {
    $errors[] = 'Your message is a little short. Please add more detail.';
  } elseif (mb_strlen($message) > 5000) {
    $errors[] = 'Your message must be 5,000 characters or fewer.';
  }

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

  $_SESSION['form_review'] = [
    'name'    => $name,
    'email'   => $email,
    'subject' => $subject,
    'message' => $message,
  ];
  $_SESSION['form_values'] = $_SESSION['form_review'];

  header('Location: contact.php?review=1');
  exit;
}

$review = $_SESSION['form_review'] ?? null;
if (!is_array($review)) {
  $_SESSION['form_error'] = true;
  header('Location: contact.php');
  exit;
}

$name    = trim((string) ($review['name'] ?? ''));
$email   = trim((string) ($review['email'] ?? ''));
$subject = trim((string) ($review['subject'] ?? ''));
$message = trim((string) ($review['message'] ?? ''));

$admin_email = 'admin@wipeyourpaws.net';
$email_subject = '[wipeyourpaws.net] ' . ($subject !== '' ? $subject : 'New message from contact form');

$email_body  = "You have a new message from the wipeyourpaws.net contact form.\n\n";
$email_body .= "Name    : {$name}\n";
$email_body .= "Email   : {$email}\n";
$email_body .= "Subject : {$subject}\n\n";
$email_body .= "Message:\n{$message}\n\n";
$email_body .= "Sent    : " . date('Y-m-d H:i:s T') . "\n";
$email_body .= "IP      : " . filter_var(
  $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown',
  FILTER_VALIDATE_IP
) . "\n";

$safe_email = str_replace(["\r", "\n", "\t"], '', $email);
$safe_subject = str_replace(["\r", "\n"], '', $email_subject);

$headers  = "From: noreply@wipeyourpaws.net\r\n";
$headers .= "Reply-To: {$safe_email}\r\n";
$headers .= "X-Mailer: PHP/" . PHP_VERSION . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "MIME-Version: 1.0\r\n";

$sent = @mail($admin_email, $safe_subject, $email_body, $headers);

if ($sent) {
  $_SESSION['form_sent'] = true;
  unset($_SESSION['form_review'], $_SESSION['form_values']);
} else {
  $_SESSION['form_error'] = true;
  $_SESSION['form_values'] = [
    'name'    => $name,
    'email'   => $email,
    'subject' => $subject,
    'message' => $message,
  ];
}

header('Location: contact.php');
exit;
