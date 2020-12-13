<?php

require __DIR__ . "/vendor/autoload.php";

use http\Message\Uri;

$request = new \http\Message\Request();

$uri = (new Uri("https://htp.google.com"));

var_dump($uri->withQuery("?name=matthew@data=matthew"));

