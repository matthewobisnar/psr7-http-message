<?php

require __DIR__ . "/vendor/autoload.php";

use http\Message\Uri;

$request = new \http\Message\Request();

$uri = (new Uri("https://usr:pass@d.phalcon.ld:8080/action?par=val#frag"));

