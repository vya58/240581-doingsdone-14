<?php
require_once('init.php');

// Запрос в БД списка проектов и количества задач в каждом из них с помощью подготовленных выражений
$sql_data = [$user['user_id']];

$sql = "SELECT project_name, p.project_id, COUNT(task_name) AS count_tasks FROM projects p LEFT JOIN tasks t ON t.project_id = p.project_id WHERE p.user_id = ? GROUP BY project_name, p.project_id";

$sql_result = get_result_prepare_sql($link, $sql, $sql_data);

$projects = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
$project_names = array_column($projects, 'project_name');

$errors = [];

// Валидация данных, введённых в поля формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получение запроса из формы
    $project = filter_input_array(INPUT_POST, ['name' => FILTER_DEFAULT], true);
    $project_name = $project['name'];

    // Фильтрация запроса
    $project_name = filter_string($project_name);

    // Проверка заполненного поля на ошибки
    $errors['name'] = validate_field_length($project_name, 0, 30);
    if (!$errors['name']) {
        $errors['name'] = validate_project_name($link, $user, $project_name);
    }

    // Вывод сообщений об ошибочно заполненных полях формы добавления задачи
    if ($errors['name']) {
        $form_content = include_template('add_project.php', [
            'projects' => $projects,
            'user' => $user,
            'errors' => $errors
        ]);

        $layout_content = include_template('layout.php', [
            'content' => $form_content,
            'title' => $title,
            'user' => $user,
            'year' => $year
        ]);

        print($layout_content);
        exit;
    }

    // Формирование и выполнение SQL-запроса в БД, в случае успешной проверки формы, на добавление нового проекта
    $project['name'] = $project_name;

    array_unshift($project, $user['user_id']);

    $sql = "INSERT INTO projects (user_id, project_name) VALUES (?, ?);";

    $stmt = get_prepare_stmt($link, $sql, $project);

    if (false === $stmt) {
        output_error_sql($link);
    }

    $result = mysqli_stmt_execute($stmt);

    if (false === $result) {
        output_error_sql($link);
    }

    $sql_result = mysqli_stmt_get_result($stmt);

    // Переадресация пользователя на главную страницу после успешного добавления новой задачи
    if (false === $sql_result) {

        header("Location: index.php");
    } else {
        output_error_sql($link);
    }
}


// Подключение шаблона с формой добавления задачи
$form_content = include_template('add_project.php', [
    'projects' => $projects,
]);

$layout_content = include_template('layout.php', [
    'content' => $form_content,
    'title' => $title,
    'user' => $user,
    'year' => $year
]);

print($layout_content);
