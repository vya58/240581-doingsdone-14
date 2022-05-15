<?php

require_once('init.php');

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

// Запрос в БД на получение всех невыполненных "горящих" задач всех пользователей (если задача не отмечена как выполненная и дата выполнения равна текущей)
$sql = "SELECT user_name, user_email, GROUP_CONCAT(task_name SEPARATOR ';') AS task_names, GROUP_CONCAT(task_deadline SEPARATOR ';') AS task_deadlines FROM tasks t LEFT JOIN users u ON t.user_id = u.user_id WHERE task_status = 0 AND task_deadline = NOW() GROUP BY t.user_id";

$sql_result = mysqli_query($link, $sql);

if (false === $sql_result) {
    output_error_sql($link);
}

$users_deadlines = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

// Разбивка "горящих" задач по каждому пользователю
foreach ($users_deadlines as $user_deadline) {
    // Создание массивов наименований задач и дат их выполнения из строк, созданных с помощью "GROUP_CONCAT" в SQL-запросе
    $user_task_names = explode(";", $user_deadline['task_names']);
    $user_task_deadlines = explode(";", $user_deadline['task_deadlines']);

    // Создание массива для хранения параметров отправляемых email
    $deadline_message = [
        'from' => $email_send_server,
        'to' => $user_deadline['user_email'],
        'subject' => 'Уведомление от сервиса «Дела в порядке»',
        'text' => ''
    ];

    $count = count($user_task_names);

    // Элементы текста и пунктуации текста сообщения, если у пользователя одна "горящая" задача
    $you = 'У Вас запланирована задача ';
    $hyphen = '';
    $pointing = '.';

    // Элементы текста и пунктуации текста сообщения, если у пользователя более одой "горящей" задачи
    if ($count > 1) {
        $you = 'У Вас запланированы задачи: '  . "<br>";
        $hyphen = '- ';
        $pointing = ';';
    }

    // Начало сообщения в зависимости от количества "горящих" задач
    $text_message = 'Уважаемый(ая), ' . $user_deadline['user_name'] . '!' . "<br>" . "<br>" . $you;

    // Объединение всех "горящих" задач в один текст
    for ($i = 0; $i < $count; $i++) {

        if ($i == ($count - 1)) {
            $pointing = '.';
        }

        $text_message = $text_message . $hyphen . '"' . $user_task_names[$i] . '"' . ' на ' . date("Y-m-d", strtotime($user_task_deadlines[$i])) . $pointing . "<br>";
    }

    // Добавление в текст сообщения подписи и email отправителя
    $text_message = $text_message . "<br>" . 'Ваш сервис «Дела в порядке»' . "<br>" . $email_send_server;

    $deadline_message['text'] = $text_message;

    $transport = Transport::fromDsn($dsn);

    // Формирование сообщения
    $message = new Email();
    $message->to($deadline_message['to']);
    $message->from($email_send_server);
    $message->subject($deadline_message['subject']);
    $message->html($deadline_message['text']);

    // Отправка сообщения
    $mailer = new Mailer($transport);
    $mailer->send($message);
}
