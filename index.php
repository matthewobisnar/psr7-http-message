<?php

require __DIR__ . "/vendor/autoload.php";

use http\Message\Uri;

$request = new \http\Message\Request();

$uri = (new Uri("http://d.phalcon.ld:8080/action?par=val#frag"));

echo $uri;