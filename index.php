<?php

require __DIR__ . "/vendor/autoload.php";

$serverRequest = new \Http\Message\ServerRequest($_SERVER);

$stream = new \Http\Message\Stream(file_get_contents('php://input'));

$Request2 = $serverRequest->withHeader('Content-Type', 'application/json')->withBody($stream);

$Request1 = $serverRequest->withHeader('Content-Type', 'application/x-www-form-urlencoded');

// Converts it into a PHP object

var_dump($Request2->getParsedBody());
var_dump($Request1->getParsedBody());

// var_dump($Request2->getParsedBody());