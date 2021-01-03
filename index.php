<?php

use Http\Message\Uri;

require __DIR__ . "/vendor/autoload.php";

// var_dump($_SERVER['REQUEST_URI']);

// phpinfo();
$uri = new Uri(sprintf("%s://%s%s%s", 
$_SERVER['REQUEST_SCHEME'], 
$_SERVER['HTTP_HOST'], 
$_SERVER['REQUEST_URI'], 
$_SERVER['QUERY_STRING'] ?? null));


$request = new \Http\Message\Request(
    $_SERVER['REQUEST_METHOD'],
    $uri
);

var_dump($request);