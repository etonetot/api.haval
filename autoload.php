<?php
ini_set('display_errors', 1);

define (BASE_DIR, __DIR__);


spl_autoload_register(function ($class){
    $path = __DIR__ . "/" .str_replace('\\', '/', $class). '.php';
    //print $path;
    require_once __DIR__ . "/" .str_replace('\\', '/', $class). '.php';
});


$app = new \core\Router();
$app->run();

