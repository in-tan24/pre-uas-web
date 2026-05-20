<?php
declare(strict_types=1);

namespace App\Utils;

final class Flash
{
    public static function set(string $type, string $message): void
    {
        $_SESSION['_flash'] = ['type' => $type, 'message' => $message];
    }

    /** @return array{type: string, message: string}|null */
    public static function get(): ?array
    {
        $flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);
        return is_array($flash) ? $flash : null;
    }
}

