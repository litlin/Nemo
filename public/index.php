<?php

const gate_port = 6789;
try {
    chdir(dirname(__DIR__));
    include_once 'vendor/autoload.php';

    Home\Service\BaseService::bootstrap()->run();
} catch (Exception $e) {
    var_dump($e);
} finally {
    // var_dump($_SERVER);
}
