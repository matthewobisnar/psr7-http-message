<?php
require __DIR__ . "/vendor/autoload.php";

$request = new \Http\Message\Request($_SERVER);

var_dump($request);