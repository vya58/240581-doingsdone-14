<?php

require_once('init.php');

//Подключение лейаута для анонимного посетителя
if (!$user['user_id']) {
    $guest_content = include_template('guest.php');

    $layout_content = include_template('layout.php', [
        'content' => $guest_content,
        'title' => $title,
        'user' => $user,
        'year' => $year
    ]);

    print($layout_content);
    exit;
}

// Получение параметров чекбокса задачи и её id из GET-запроса 
$tasks_check = filter_input(INPUT_GET, 'check', FILTER_SANITIZE_NUMBER_INT);
$task_id = filter_input(INPUT_GET, 'task_id', FILTER_SANITIZE_NUMBER_INT);
$tasks_status = [
    'tasks_status' => $tasks_check,
    'user_id' => $user['user_id'],
    'task_id' => $task_id
];
// SQL-запрос на инверование статуса задачи
$sql = "UPDATE tasks SET task_status = ? WHERE user_id = ? AND task_id = ?;";

$stmt = get_prepare_stmt($link, $sql, $tasks_status);

if (false === $stmt) {
    output_error_sql($link);
}

$result = mysqli_stmt_execute($stmt);

if (false === $result) {
    output_error_sql($link);
}

$sql_result = mysqli_stmt_get_result($stmt);

// Запрос в БД списка проектов и количества задач в каждом из них
$sql_data = [$user['user_id']];

$sql = "SELECT project_name, p.project_id, COUNT(task_name) AS count_tasks FROM projects p LEFT JOIN tasks t ON t.project_id = p.project_id WHERE p.user_id = ? GROUP BY project_name, p.project_id";

$sql_result = get_result_prepare_sql($link, $sql, $sql_data);

$projects = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

//Получение параметров чекбокса "Показывать выполненные" из GET-запроса
$show_complete_tasks = filter_input(INPUT_GET, 'show_completed', FILTER_SANITIZE_NUMBER_INT);

if (!$show_complete_tasks) {
    $show_complete_tasks = 0;
}

// Получение id проекта из GET-запроса для фильтрации задач по проектам
$project_id  = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Получение значения filter из GET-запроса для фильтрации задач в блоке фильтров
$filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_NUMBER_INT);

// Запрос к БД на получение списка задач
if ($project_id) {
    // Запрос к БД на получение списка задач в выбранном проекте в зависимости от состояния блока фильтров задач
    $sql_add = preparation_insert_filtration($filter);

    $sql = "SELECT task_id, task_name, task_deadline, project_name, task_status, task_file FROM tasks t "
        . "INNER JOIN projects p ON t.project_id = p.project_id "
        . "WHERE t.user_id = ? AND t.project_id = ? " . $sql_add . "ORDER BY task_date_create";

    $sql_data = [$user['user_id'], $project_id];
    $sql_result = get_result_prepare_sql($link, $sql, $sql_data);

    // Вывод ошибки 404 при несуществующем id проекта в полученном запросе
    $existence_project = mysqli_num_rows($sql_result);
    if (false === $existence_project) {
        $content = include_template('404.php');
        $layoutContent = include_template('layout.php', [
            'content' => $content,
            'title' => $title,
            'year' => $year
        ]);
        http_response_code(404);
        print($layoutContent);
        exit();
    }
} else {
    // Запрос к БД на получение списка всех задач пользователя в зависимости от состояния блока фильтров задач
    $sql_add = preparation_insert_filtration($filter);

    $sql = "SELECT task_id, task_name, task_deadline, project_name, task_status, task_file FROM tasks t "
        . "LEFT JOIN projects p ON t.project_id = p.project_id "
        . "WHERE t.user_id = ? " . $sql_add . "ORDER BY task_date_create";

    $sql_data = [$user['user_id']];
    $sql_result = get_result_prepare_sql($link, $sql, $sql_data);
}

$tasks = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

// Полнотекстовый поиск по задачам пользователя
$search = $_GET['search'] ?? '';
$not_found = false;

if ($search) {
    // Установка $show_complete_tasks в 1, чтобы в поиске отображались и выполненные задачи
    $show_complete_tasks = 1;
    $search_request = trim($search) . '*';

    $sql = "SELECT task_id, task_name, task_deadline, project_name, task_status, task_file FROM tasks t "
        . "INNER JOIN projects p ON t.project_id = p.project_id "
        . "WHERE t.user_id = ? AND MATCH (t.task_name) AGAINST(? IN BOOLEAN MODE)";

    $sql_data = [$user['user_id'], $search_request];
    $sql_result = get_result_prepare_sql($link, $sql, $sql_data);
    $tasks = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

    if (!$tasks) {
        $not_found = 'Ничего не найдено по вашему запросу';
    }
}

// Полнотекстовый поиск по задачам пользователя
$main_content = include_template('main.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'show_complete_tasks' => $show_complete_tasks,
    'project_id' => $project_id,
    'not_found' => $not_found,
    'search' => $search,
    'filter' => $filter
]);

$layout_content = include_template('layout.php', [
    'content' => $main_content,
    'title' => $title,
    'user' => $user,
    'year' => $year
]);

print($layout_content);
