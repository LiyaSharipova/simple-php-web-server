#!/usr/bin/env php
<?php

use LiyaSharipova\SimplePhpWebServer\Server;
use LiyaSharipova\SimplePhpWebServer\Request;
use LiyaSharipova\SimplePhpWebServer\Response;

use lf4php\LoggerFactory;

require 'vendor/autoload.php';


// we never need the first argument
array_shift($argv);

// the next argument should be the port if not use 80
if (empty($argv)) {
    $port = 8015;
} else {
    $port = array_shift($argv);
}

// create a new startup.php instance
$server = new Server('127.0.0.1', $port);

// start listening
$server->listen(function (Request $request) {

    $logger = LoggerFactory::getLogger(basename(__FILE__));

    $uri = $request->uri();

    var_dump($logger->isInfoEnabled());
    $logger->info($request->method() . ' ' . $uri);

    $response = '';
    switch (true) {
        case (match($uri, "/^\/$/")): {
            $response = "this is root web-page";
            break;
        }
        case (match($uri, "/^\/hello\/?$/")): {
            $response = "this is hello web-page";
            break;
        }
        default:
            $logger->error("A controller for url=" . "\"" . $uri . "\"" . " is not defined!");
    }

    return new Response('<pre>' . $response . '</pre>');

});

function match(String $uri, String $regex): bool
{
    if (preg_match($regex, $uri) == 1)
        return true;
    else return false;
}