<?php namespace LiyaSharipova\SimplePhpWebServer;

class Request
{

    protected $method = null;


    protected $resourceAddress = null;


    protected $parameters = [];


    protected $headers = [];


    public static function withHeaderString($header)
    {
        $lines = explode("\n", $header);

        list($method, $resourceAddress) = explode(' ', array_shift($lines));

        $headers = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (strpos($line, ': ') !== false) {
                list($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }

        return new static($method, $resourceAddress, $headers);
    }


    public function __construct($method, $resourceAddress, $headers = [])
    {
        $this->headers = $headers;
        $this->method = strtoupper($method);

        @list($this->resourceAddress, $params) = explode('?', $resourceAddress);

        parse_str($params, $this->parameters);
    }


    public function method()
    {
        return $this->method;
    }


    public function resourceAddress()
    {
        return $this->resourceAddress;
    }


    public function header($key, $default = null)
    {
        if (!isset($this->headers[$key])) {
            return $default;
        }

        return $this->headers[$key];
    }


    public function param($key, $default = null)
    {
        if (!isset($this->parameters[$key])) {
            return $default;
        }

        return $this->parameters[$key];
    }
}