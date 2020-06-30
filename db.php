<?php
    $host = 'https://rudy.zzz.com.ua/mysql/'; // адрес сервера 
    $database = 'razvalik'; // имя базы данных
    $user = 'testinbeejee'; // имя пользователя
    $password = 'Beejee1'; // пароль

    // строка подключения
    $link = mysqli_connect($host, $user, $password, $database);
    
    // запускаем сессию
    session_start();