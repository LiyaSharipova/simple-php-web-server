#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

use LiyaSharipova\SimplePhpWebServer\Server;
use LiyaSharipova\SimplePhpWebServer\Request;
use LiyaSharipova\SimplePhpWebServer\Response;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


// Извлекаем первое значение массива $argv и возвращает его, сокращая размер $argv на один элемент
// Название скрипта не нужно
array_shift($argv);

// Указываем в качестве порта значение из опции, если он не пустой
if (empty($argv)) {
    $port = 8022;
} else {
    $port = array_shift($argv);
}

//создаем сервер на указанном адресе и порту
$server = new Server('127.0.0.1', $port);

//метод listen слушает запросы на сокете и регистрирует callback функцию
$server->listen(function (Request $request) {

    // Создаем логгер с именем loggerName
    $loggerName = 'server';
    $logger = new Logger($loggerName);
    // Логгер будет выводить на консоль (STandarD OUTput) текст с уровнем не ниже INFO
    $logger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));

    //получаем адресс ресурса на этом сайте
    $resourceAddress = $request->resourceAddress();

    $logger->info("Requested content: " . $request->method() . ' ' . $resourceAddress);

    //формируем body response
    $response = '';
    switch (true) {
        case (match($resourceAddress, "/^\/$/")): {
            $response = "this is root web-page";
            break;
        }
        case (match($resourceAddress, "/^\/hello\/?$/")): {
            $response = "this is hello web-page";
            break;
        }
        default:
            $logger->error("A controller for url=" . "\"" . $resourceAddress . "\"" . " is not defined!");
    }

    return new Response('<pre>' . $response . '</pre>');

});

/**
 * Проверка роутинга
 * @param String $uri
 * @param String $regex
 * @return bool
 */
function match(String $uri, String $regex): bool
{
    if (preg_match($regex, $uri) == 1)
        return true;
    else return false;
}