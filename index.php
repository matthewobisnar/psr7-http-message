<?php

use Http\Message\Stream;

require __DIR__ . "/vendor/autoload.php";

$uri = new \Http\Message\Uri(sprintf(
    "%s://%s%s",
    $_SERVER['REQUEST_SCHEME'], 
    $_SERVER['HTTP_HOST'], 
    $_SERVER['REQUEST_URI']
));

$serverRequest = new \Http\Message\ServerRequest(
    $_SERVER['REQUEST_METHOD'], 
    $_SERVER, 
    $uri
);

($serverRequest->getQuery('data'));