<?php
declare(strict_types=1);

namespace App\Http;

use App\Utils\View;

final class Router
{
    /** @var array<string, array<string, array{0: class-string, 1: string}>> */
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path = $this->resolvePath();

        $handler = $this->routes[$method][$path] ?? null;
        if ($handler === null) {
            http_response_code(404);
            View::render('errors/404', ['path' => $path]);
            return;
        }

        [$class, $action] = $handler;
        $controller = new $class();
        $controller->$action();
    }

    private function resolvePath(): string
    {
        if (!empty($_GET['r']) && is_string($_GET['r'])) {
            $r = '/' . ltrim($_GET['r'], '/');
            return $r === '//' ? '/' : $r;
        }

        $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);
        $path = is_string($path) ? $path : '/';

        // Remove script dir (/pre-uas/public)
        $scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '');
        $baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if ($baseDir !== '' && str_starts_with($path, $baseDir)) {
            $path = substr($path, strlen($baseDir)) ?: '/';
        }

        $path = '/' . ltrim($path, '/');
        $trimmed = rtrim($path, '/');
        return $path === '//' ? '/' : (($trimmed !== '') ? $trimmed : '/');
    }
}
