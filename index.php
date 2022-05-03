<?php

require_once('init.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$sql_data = [$user];
// Запрос в БД списка проектов и количества задач в каждом из них
$sql = "SELECT project_name, p.project_id, COUNT(task_name) AS count_tasks FROM projects p INNER JOIN tasks t ON t.project_id = p.project_id WHERE p.user_id = ? GROUP BY project_name, p.project_id";

$sql_result = get_result_prepare_sql($link, $sql, $sql_data);

$projects = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

// Получение id проекта в запросе
$project_id  = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Запрос к БД на получение списка задач
if ($project_id) {// Запрос к БД на получение списка задач в выбранном проекте
    $sql = "SELECT task_name, task_deadline, project_name, task_status, task_file FROM tasks t "
     . "INNER JOIN projects p ON t.project_id = p.project_id "
     . "WHERE t.user_id = ? AND t.project_id = ? ORDER BY task_date_create";
    
    $sql_data = [$user, $project_id];
    $sql_result = get_result_prepare_sql($link, $sql, $sql_data);

    // Вывод ошибки 404 при несуществующем id проекта в полученном запросе
    $existence_project = mysqli_num_rows($sql_result);
    if (!$existence_project) {
        $content = include_template('404.php');
        $layoutContent = include_template('layout.php',[
        'content' => $content,
        'title' => 'Дела в порядке'
    ]);
    http_response_code(404);
    print($layoutContent);
    exit();
}
    
} else {// Запрос к БД на получение списка всех задач пользователя
    $sql = "SELECT task_name, task_deadline, project_name, task_status, task_file FROM tasks t "
     . "INNER JOIN projects p ON t.project_id = p.project_id "
     . "WHERE t.user_id = ? ORDER BY task_date_create";

    $sql_data = [$user];
    $sql_result = get_result_prepare_sql($link, $sql, $sql_data);
}

$tasks = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

$main_content = include_template('main.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'show_complete_tasks' => $show_complete_tasks,
    'project_id' => $project_id
]);                                         

$layout_content = include_template('layout.php', [
    'content' => $main_content,
    'title' => 'Дела в порядке'
]);

print($layout_content);

