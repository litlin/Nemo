<?php
try {
    chdir(dirname(__DIR__));
    include_once 'vendor/autoload.php';

    BaseServices\Services\BaseService::bootstrap()->run();
} catch (Exception $e) {
    var_dump($e);
} finally {
    // var_dump($_SERVER);
}
