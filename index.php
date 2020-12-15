<?php

require __DIR__ . "/vendor/autoload.php";

use http\Message\Uri;

$request = new \http\Message\Request();

$uri = (new Uri("https://usr:PASS@d.phalcon.ld:8080/action?par=val#frag"));

var_dump("Get url Components => ", $uri->getUrlComponents());

echo "Get Scheme => " . $uri->getScheme() . PHP_EOL;

echo "Get Authority => " . $uri->getAuthority() . PHP_EOL;

echo "Get UserInfo => " . $uri->getUserInfo() . PHP_EOL;

echo "Get Host => " . $uri->getHost() . PHP_EOL;

echo "Get Port => " . $uri->getPort() . PHP_EOL;

echo "Get Path => " . $uri->getPath() . PHP_EOL;

echo "Get Query => " . $uri->getQuery() . PHP_EOL;

echo "Get Fragment => " . $uri->getFragment() . PHP_EOL;

var_dump("With Scheme => ", $uri->withScheme('http'));

var_dump("With with User Info => ", $uri->withUserInfo('matthew', 'bisnar'));

var_dump("With Host => ", $uri->withHost('google.com'));

var_dump("With Port => ", $uri->withPort(8080));

var_dump("With Path => ", $uri->withPath("/sample-path"));

var_dump("With Path => ", $uri->withQuery("datasample=data1&datasample2=data2"));

var_dump("With Path => ", $uri->withFragment("#sampleFragment"));