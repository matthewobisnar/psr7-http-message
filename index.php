<?php

require __DIR__ . "/vendor/autoload.php";

use http\Message\Uri;
$uri = (new Uri("http://localhost:8080/action//data?par=val#frag"));


$request = new \http\Message\Request();

print_r(
    $request->withHeader('Referer', 'https://fonts.googleapis.com')
    ->withAddedHeader('referer', 'dssdf')
    ->getHeaderLine('Referer')
);