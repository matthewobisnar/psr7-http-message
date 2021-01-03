<?php

require __DIR__ . "/vendor/autoload.php";

// var_dump($_SERVER['REQUEST_URI']);

// phpinfo();

$uri = sprintf("%s://%s%s%s", 
$_SERVER['REQUEST_SCHEME'], 
$_SERVER['HTTP_HOST'], 
$_SERVER['REQUEST_URI'], 
$_SERVER['QUERY_STRING'] ?? null);

// var_dump($uri);

$request = new \Http\Message\Request(
    headers_list(),
    $_SERVER['REQUEST_METHOD'],
    $uri
);

// $request = new \Http\Message\ServerRequest(
//     $_SERVER['REQUEST_METHOD'], 
//     $uri,
//     headers_list()
// );

echo $request->getUri();