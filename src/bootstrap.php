<?php
declare(strict_types=1);

require __DIR__ . '/config/env.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/autoload.php';

date_default_timezone_set(env('APP_TZ', 'Asia/Bangkok') ?? 'Asia/Bangkok');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

