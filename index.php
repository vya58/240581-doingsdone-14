<?php
require_once('helpers.php');

// подключение к БД 'database'
$link = mysqli_connect('127.0.0.1', 'root', '', 'doings_done');
mysqli_set_charset($link, "utf8");

if (!$link) {
   print("Ошибка подключения: " . mysqli_connect_error());
}

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$user = 4;

// запрос в БД списка категорий
$sql = "SELECT project_name FROM projects WHERE user_id = $user ORDER BY project_id";
$result = mysqli_query($link, $sql);

if (!$result) {
    $error = mysqli_error($link);
	print("Ошибка MySQL: " . $error);
 }
 else {
    $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
 }

 // запрос в БД списка задач
$sql = "SELECT task_name, task_deadline, project_name, task_status FROM tasks "
     . "INNER JOIN projects ON tasks.project_id = projects.project_id "
     . "WHERE tasks.user_id = $user ORDER BY task_date_create";
$result = mysqli_query($link, $sql);

if (!$result) {
    $error = mysqli_error($link);
	print("Ошибка MySQL: " . $error);
 }
 else {
    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
 }


function count_tasks_in_project(array $tasks, $project) {
    # Подсчет количества задач в проекте
    $count = 0;
    foreach ($tasks as $task) {
        if($task['project_name'] == $project) {
            $count++;
        }
    }
    return $count;
}


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

