<?php namespace LiyaSharipova\SimplePhpWebServer;

use LiyaSharipova\SimplePhpWebServer\CustomException;
use LiyaSharipova\SimplePhpWebServer\Request;

class Server
{

    protected $host = null;


    protected $port = null;


    protected $socket = null;


    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = (int)$port;

        // create a socket
        $this->createSocket();

        // bind the socket
        $this->bind();
    }


    protected function createSocket()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
    }


    protected function bind()
    {
        if (!socket_bind($this->socket, $this->host, $this->port)) {
            throw new CustomException('Could not bind: ' . $this->host . ':' . $this->port . ' - ' . socket_strerror(socket_last_error()));
        }
    }


    public function listen($callback)
    {
        // check if the callback is valid
        if (!is_callable($callback)) {
            throw new CustomException('The given argument should be callable.');
        }

        while (1) {
            // listen for connections
            socket_listen($this->socket);

            // try to get the client socket resource
            // if false we got an error close the connection and continue
            if (!$client = socket_accept($this->socket)) {
                socket_close($client);
                continue;
            }

            // create new request instance with the clients header.
            // In the real world of course you cannot just fix the max size to 1024..
            $request = Request::withHeaderString(socket_read($client, 1024));

            // execute the callback
            $response = call_user_func($callback, $request);

            // check if we really recived an Response object
            // if not return a 404 response object
            if (!$response || !$response instanceof Response) {
                $response = Response::error(404);
            }

            // make a string out of our response
            $response = (string)$response;

            // write the response to the client socket
            socket_write($client, $response, strlen($response));

            // close the connetion so we can accept new ones
            socket_close($client);
        }
    }
}