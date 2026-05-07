<?php

declare(strict_types=1);

// This bootstrap runs early so the rest of the app can call getenv()/wyp_env()
// without caring where variables came from.
// We intentionally make .env mandatory here to fail fast if configuration is missing.
$dotenvPath = dirname(__DIR__) . '/.env';
$dotenvLines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($dotenvLines === false) {
  // Keep server logs actionable, but avoid leaking absolute paths to users if display_errors is enabled.
  error_log('Mandatory .env file could not be loaded at: ' . $dotenvPath);
  throw new RuntimeException('Application configuration is missing. Contact the site administrator.');
}

// Parse each .env line into KEY=VALUE and publish to process/server env arrays.
foreach ($dotenvLines as $dotenvLine) {
  $trimmedLine = trim($dotenvLine);

  // Allow blank lines and comments for readability in .env files.
  if ($trimmedLine === '' || str_starts_with($trimmedLine, '#')) {
    continue;
  }

  // Accept "export KEY=value" format as a convenience (common in shell-style env files).
  if (str_starts_with($trimmedLine, 'export ')) {
    $trimmedLine = trim(substr($trimmedLine, 7));
  }

  // Split only on the first "=" so values can still contain "=" characters.
  [$key, $value] = array_pad(explode('=', $trimmedLine, 2), 2, '');
  $key = trim($key);
  $value = trim($value);

  // Remove surrounding quotes if present.
  // This keeps values like "my value with spaces" or 'literal string' usable.
  if (
    (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
    (str_starts_with($value, "'") && str_ends_with($value, "'"))
  ) {
    $value = substr($value, 1, -1);
  }

  // Store values in all common places:
  // - putenv(): native process environment
  // - $_ENV / $_SERVER: convenience for libraries/frameworks expecting those arrays
  putenv($key . '=' . $value);
  $_ENV[$key] = $value;
  $_SERVER[$key] = $value;
}

// Unified accessor used across the app.
// We keep a default parameter so callers can opt into a fallback for non-critical keys.
function wyp_env(string $key, string $default = ''): string
{
  $value = getenv($key);
  if ($value !== false) {
    // Trim guards against accidental whitespace in .env assignments.
    return trim((string) $value);
  }

  return $default;
}

/**
 * Start a hardened session with explicit cookie attributes.
 * Call this only on pages that actually require session state.
 */
function wyp_start_secure_session(): void
{
  if (session_status() === PHP_SESSION_ACTIVE) {
    return;
  }

  $isHttpsRequest = (
    (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
  );

  $existingCookieParams = session_get_cookie_params();
  session_set_cookie_params([
    'lifetime' => 0,
    'path' => $existingCookieParams['path'] ?? '/',
    'domain' => $existingCookieParams['domain'] ?? '',
    'secure' => $isHttpsRequest,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);

  session_start();
}
