#!/usr/bin/env php
<?php 

use LiyaSharipova\SimplePhpWebServer\Server;
use LiyaSharipova\SimplePhpWebServer\Request;
use LiyaSharipova\SimplePhpWebServer\Response;

require 'vendor/autoload.php';

// we never need the first argument
array_shift( $argv );

// the next argument should be the port if not use 80
if ( empty( $argv ) )
{
	$port = 8090;
} else {
	$port = array_shift( $argv );
}

// create a new startup.php instance
$server = new Server( '127.0.0.1', $port );

// start listening
$server->listen( function( Request $request ) 
{
	// print information that we recived the request
	echo $request->method() . ' ' . $request->uri() . "\n";
	
	// return a response containing the request information
	return new Response( '<pre>'.print_r( $request, true ).'</pre>' );
//    return new Response( '<pre>'."asjdhs".'</pre>' );

});