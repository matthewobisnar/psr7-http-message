<?php

require __DIR__ . "/vendor/autoload.php";

use Http\Message\Uri;

print_r($request->withUri(new Uri("http://localhost:8080/action//data?par=val#frag")));