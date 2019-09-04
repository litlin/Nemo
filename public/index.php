<?php
try {
    chdir(dirname(__DIR__));
    include_once 'vendor/autoload.php';

    if (preg_match('/index\.php\/[^\/]*\/.*/', $_SERVER['PHP_SELF'])) {
        die("测试时无法响应类似请求");
    }

    BaseServices\Services\BaseService::bootstrap()->run();
} catch (Exception $e) {
    var_dump($e);
} finally {
    // var_dump($_SERVER);
}
