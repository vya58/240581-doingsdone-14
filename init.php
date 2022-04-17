<?php

require_once('helpers.php');

$db = require_once('db.php');
// Подключение к БД
$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, "utf8");

// Вывод ошибки подключения к БД 
if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
    $layout_content = include_template('layout.php', [
        'content' => $content,
        'title' => 'Дела в порядке'
    ]);
    print($layout_content);
    exit;
}

$projects = [];
$content = '';
