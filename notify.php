<?php

require_once('init.php');

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

// Запрос на получение id всех пользователей, у которых есть невыполненные задачи с датой выполнения, совпадающей с текущей
$sql = "SELECT DISTINCT user_id FROM tasks WHERE task_status = 0 AND task_deadline = CURDATE()";

$sql_result = mysqli_query($link, $sql);

if (false === $sql_result) {
    output_error_sql($link);
}

$users_deadline_id = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

$user_ids = [];

// Создание массива с id отобранных пользователей
foreach ($users_deadline_id as $user_id) {
    $user_ids[] = $user_id['user_id'];
}

if (!$user_ids) {
    exit;
}

$count = count($user_ids);

// Перебор всех пользователей, имеющих невыполненные "горящие" задачи
for ($i = 0; $i < $count; $i++) {

    // Запрос в БД на получение данных по всем невыполненным "горящим" задачам i-того пользователя
    $sql_data = [$user_ids[$i]];
    $sql = "SELECT user_name, user_email, task_name, task_deadline FROM users u INNER JOIN tasks t ON u.user_id = t.user_id WHERE u.user_id = ? AND task_status = 0 AND task_deadline = CURDATE()";

    $sql_result = get_result_prepare_sql($link, $sql, $sql_data);

    $users_deadlines = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

    $count_tasks = count($users_deadlines);

    // Элементы текста и пунктуации текста сообщения, если у пользователя одна "горящая" задача
    $you = 'У Вас запланирована задача ';
    $hyphen = '';
    $pointing = '.';

    // Элементы текста и пунктуации текста сообщения, если у пользователя более одой "горящей" задачи
    if ($count_tasks > 1) {
        $you = 'У Вас запланированы задачи: '  . "<br>";
        $hyphen = '- ';
        $pointing = ';';
    }

    // Начало сообщения в зависимости от количества "горящих" задач
    $text_message = 'Уважаемый(ая), ' . $users_deadlines[0]['user_name'] . '!' . "<br>" . "<br>" . $you;

    $counter = -1;

    foreach ($users_deadlines as $user_deadline) {

        $counter++;

        if ($counter == ($count_tasks - 1)) {
            $pointing = '.';
        }

        // Добавление в текст сообщения подписи и email отправителя
        $text_message = $text_message . $hyphen . '"' . $user_deadline['task_name'] . '"' . ' на ' . date("Y-m-d", strtotime($user_deadline['task_deadline'])) . $pointing . "<br>";
    }

    $text_message = $text_message . "<br>" . 'Ваш сервис «Дела в порядке»' . "<br>" . "<a href=" . $email_send_server . ">" . $email_send_server . "</a>";

    // Создание массива для хранения параметров отправляемых email
    $deadline_message = [
        'from' => $email_send_server,
        'to' => $users_deadlines[0]['user_email'],
        'subject' => 'Уведомление от сервиса «Дела в порядке»',
        'text' => $text_message
    ];
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
