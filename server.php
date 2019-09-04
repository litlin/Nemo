<?php
// 使用方法
// 当前目录：php -S localhost:8251 -t public server.php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri) && is_file(__DIR__ . '/public' . $uri)) {
    return false;
}

include_once 'vendor/autoload.php';

BaseServices\Services\BaseService::bootstrap()->run();
