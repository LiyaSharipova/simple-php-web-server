<?php

if (count($argv) > 1) {
    $host = $argv[1];
    $port = $argv[2];
}

// Иницализация библиотеки curl
if ($curl = @curl_init()) {
    $headers = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Encoding: gzip, deflate',
        'Accept-Language: en-US,en;q=0.5',
        'Cache-Control: no-cache',
        'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
        'Referer: http://www.vk.com/',
        'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
    ];

    // Устанавливаем URL запроса
    if (!empty($host) && !empty($port))
        @curl_setopt($curl, CURLOPT_URL, 'http://' . $host . ':' . $port . '/');
    else
        @curl_setopt($curl, CURLOPT_URL, 'http://localhost:8021/hello');
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    // При значении true CURL включает в вывод заголовки
    @curl_setopt($curl, CURLOPT_HEADER, true);
    // Куда помещать результат выполнения запроса:
    //  false - в стандартный поток вывода,
    //  true - в виде возвращаемого значения функции curl_exec.
    @curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // Максимальное время ожидания в секундах
    @curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
    // Выполнение запроса
    $data = @curl_exec($curl);
    echo "curl executed\n";
    // Вывести полученные данные
    echo $data;
    // Особождение ресурса
    @curl_close($curl);
}