<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Проверка режима обслуживания (maintenance mode)
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Подключение автозагрузчика Composer
require __DIR__.'/../vendor/autoload.php';

// Запуск приложения и обработка запроса
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());