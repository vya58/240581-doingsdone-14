<?php

require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    #$content = 'Ошибка соединения с базой данных: ';
    $content = include_template('error.php', ['error' => $error]);
    $layout_content = include_template('layout.php', [
        'content' => $content,
        'title' => 'Дела в порядке'
    ]);
    print($layout_content);
    exit;
}

$user = intval(4);
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

    // запрос в БД списка категорий с помощью подготовленных выражений
$sql = "SELECT project_name FROM projects WHERE user_id = ? ORDER BY project_id";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    output_error_sql($link);
}

$projects = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql = "SELECT task_name, task_deadline, project_name, task_status FROM tasks "
     . "INNER JOIN projects ON tasks.project_id = projects.project_id "
     . "WHERE tasks.user_id = ? ORDER BY task_date_create";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    output_error_sql($link);
}

$tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

$main_content = include_template('main.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'show_complete_tasks' => $show_complete_tasks
]);                                         

$layout_content = include_template('layout.php', [
    'content' => $main_content,
    'title' => 'Дела в порядке'
]);

print($layout_content);

