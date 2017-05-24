<?php namespace LiyaSharipova\SimplePhpWebServer;

class Server
{


    protected $host = null;


    protected $port = null;


    protected $socket = null;


    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = (int)$port;

        // создаем сокет
        $this->createSocket();

        // присваиваем сокету адрес и порт
        $this->bind();
    }


    protected function createSocket()
    {
        //AF_INET - ipv4
        //SOCK_STREAM - технология передачи байтов
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
        // проверяем callback функцию
        if (!is_callable($callback)) {
            throw new CustomException('The given argument should be callable.');
        }

        while (1) {
            // слушает соединения
            socket_listen($this->socket);

            // пытаемся получить ресурс сокета клиента
            // если получаем ошибку, закрываем соединение и продолжаем слушать
            if (!$client = socket_accept($this->socket)) {
                socket_close($client);
                continue;
            }

            // получаем данные у клиента длиной 1024 символа.
            // создаем из них request с headers, кот нам отправил клиент
            $request = Request::withHeaderString(socket_read($client, 1024));

            // вызываем функ callback и передаем request
            $response = call_user_func($callback, $request);

            //если response нет, задаем статус 404
            if (!$response || !$response instanceof Response) {
                $response = Response::error(404);
            }

            // вызываем toString у response
            $response = (string)$response;

            // отдаем клиенту response и указываем длину строки
            socket_write($client, $response, strlen($response));

            // закрываем сокет
            socket_close($client);
        }
    }
}