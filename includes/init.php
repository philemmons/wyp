<?php

declare(strict_types=1);

// This bootstrap runs early so the rest of the app can call getenv()/wyp_env()
// without caring where variables came from.
// We intentionally make .env mandatory here to fail fast if configuration is missing.
$dotenvPath = dirname(__DIR__) . '/.env';
$lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false) {
  // RuntimeException is deliberate: we want a hard failure in misconfigured environments
  // rather than silently running with missing secrets.
  throw new RuntimeException('Mandatory .env file could not be loaded at: ' . $dotenvPath);
}

// Parse each .env line into KEY=VALUE and publish to process/server env arrays.
foreach ($lines as $line) {
  $trimmedLine = trim($line);

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
