<?php
declare(strict_types=1);

/**
 * Minimal .env loader (no dependencies).
 * Usage: put `.env` in project root (same level as this repository root).
 */
function env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    return $default;
}

$envPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
if (is_file($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($lines)) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || substr($line, 0, 1) === '#') {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $k = trim($parts[0]);
            $v = trim($parts[1]);
            $v = trim($v, "\"'");

            if ($k !== '' && getenv($k) === false) {
                putenv($k . '=' . $v);
                $_ENV[$k] = $v;
            }
        }
    }
}
