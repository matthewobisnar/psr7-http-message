<?php

require __DIR__ . "/vendor/autoload.php";

use Psr\Http\Message\ResponseInterface;
use Http\Message\Interfaces\UdServerRequestInterface;


$serverRequest = new \Http\Message\ServerRequest($_SERVER);

var_dump($serverRequest);
