<?php
declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
    $path = __DIR__ . DIRECTORY_SEPARATOR . $relativePath;
    if (is_file($path)) {
        require $path;
    }
});

