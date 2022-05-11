<?php
session_start();
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

// Устанавливаем id и имя пользователя
if (isset($_SESSION['user_id'])) {
    $user['user_id'] = $_SESSION['user_id'];
    $user['user_name'] = $_SESSION['user_name'];
} else {
    $user['user_id'] = false;
    $user['user_name'] = false;
}


// Разрешённые для загрузки типы файлов
$mime_types = array(

    'txt' => 'text/plain',
    'htm' => 'text/html',
    'html' => 'text/html',
    'css' => 'text/css',
    'xml' => 'application/xml',

    // images
    'png' => 'image/png',
    'jpe' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'gif' => 'image/gif',
    'bmp' => 'image/bmp',
    'ico' => 'image/vnd.microsoft.icon',
    'tiff' => 'image/tiff',
    'tif' => 'image/tiff',
    'svg' => 'image/svg+xml',
    'svgz' => 'image/svg+xml',

    // archives
    'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed',

    // audio/video
    'mp3' => 'audio/mpeg',
    'qt' => 'video/quicktime',
    'mov' => 'video/quicktime',

    // adobe
    'pdf' => 'application/pdf',
    'psd' => 'image/vnd.adobe.photoshop',

    // ms office
    'doc' => 'application/msword',
    'rtf' => 'application/rtf',
    'xls' => 'application/vnd.ms-excel',
    'ppt' => 'application/vnd.ms-powerpoint',

    // open office
    'odt' => 'application/vnd.oasis.opendocument.text',
    'ods' => 'application/vnd.oasis.opendocument.spreadsheet'
);
