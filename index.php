<?php

require __DIR__ . "/vendor/autoload.php";

$serverRequest = new \Http\Message\ServerRequest($_SERVER);
$stream = new \Http\Message\Stream(file_get_contents('php://input'));

$Request2 = $serverRequest->withHeader('Content-Type', 'application/json')->withHeader('Header','Header');

class Router
{
    public function addRoute($uri)
    {
        
    }
}
