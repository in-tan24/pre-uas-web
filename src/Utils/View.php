<?php
declare(strict_types=1);

namespace App\Utils;

final class View
{
    /** @param array<string, mixed> $data */
    public static function render(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo "View not found: " . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }

        extract($data, EXTR_SKIP);
        require __DIR__ . '/../Views/layouts/header.php';
        require $viewFile;
        require __DIR__ . '/../Views/layouts/footer.php';
    }
}

