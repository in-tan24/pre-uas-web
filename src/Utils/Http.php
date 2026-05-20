<?php
declare(strict_types=1);

namespace App\Utils;

final class Http
{
    public static function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}

