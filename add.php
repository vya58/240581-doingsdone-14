<?php

require_once('init.php');
// Запрос в БД списка проектов и количества задач в каждом из них с помощью подготовленных выражений
$sql_data = [$user];
$sql = "SELECT project_name, p.project_id, COUNT(task_name) AS count_tasks FROM projects p INNER JOIN tasks t ON t.project_id = p.project_id WHERE p.user_id = ? GROUP BY project_name, p.project_id";
/*
$stmt = get_prepare_stmt($link, $sql, $sql_data);

if (false === $stmt) {
    output_error_sql($link);
}

$result = mysqli_stmt_execute($stmt);

if (false === $result) {
    output_error_sql($link);
}

$sql_result = mysqli_stmt_get_result($stmt);
*/

$sql_result = get_result_prepare_sql($link, $sql, $sql_data);

$projects = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
$project_ids = array_column($projects, 'project_id');

$errors = [];

//Массив с функциями для валидации полей формы запроса
$rules = [
    'name' => function($value) {
        return is_length_valid($value, 0, 5);
    },
    'project' => function($value) use ($project_ids) {
        return is_project_valid($value, $project_ids);
    },
    'date' => function($date) {
        return is_date_valid($date);
    }
];

//Валидация данных, введённых в поля формы
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required = [$_POST['name'], $_POST['project'], $_POST['date']];
 
    $task = filter_input_array(INPUT_POST, ['name' => FILTER_DEFAULT, 'project' => FILTER_DEFAULT, 'date' => FILTER_DEFAULT], true);
    
    foreach ($task as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }
//Если дата завершения задачи пользователем не указана, устанавливаем null
    if (empty($_POST['date'])) {
        $task['date'] = null;
    }

    if ($_POST['date'] < date('Y-m-d') && null != $task['date']) {
        $errors['date'] = "Дата выполнения задачи не может быть ранее текущей!";
    }

    if (empty($_POST['name'])) {
        $errors['name'] = "Необходимо добавить название задачи!";
    }

    $errors = array_filter($errors);
   
//Получение данных о загруженном файле   
    $tmp_name = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    $file_name = $_FILES['file']['name'];
    $file_size = $_FILES['file']['size'];

    $file_name_separated = explode(".", $file_name);
    $file_extension = strtolower(end($file_name_separated));

//Валидация загруженного файла
    //Проверка допустимости типа файла
    if (!empty($file_name) && !in_array($file_type, $mime_types)) {
        $errors['file'] = 'Недопустимый тип файла';
    }

    if ($file_size > 2097152) {
        $errors['file'] = 'Превышен максимальный размер файла';
    }

//Переименование файла
    $file_name = uniqid() . "." . $file_extension;

    $task['task_file'] = $file_name;
//Если файл пользователем не загружен, устанавливаем null
    if (empty($_FILES['file']['name'])) {
            $task['task_file'] = null;
    }
//Вывод сообщений об ошибочно заполненных полях формы
    if (count($errors)) {
        $form_content = include_template('add.php', [
            'projects' => $projects,
            'title' => 'Document',
            'errors' => $errors
        ]);
        print($form_content);
        exit;
    }

//Формирование и выполнение SQL-запроса в БД, в случае успешной проверки формы, на добавление новой задачи
    array_unshift($task, $user);
   
    $sql = "INSERT INTO tasks (user_id, task_name, project_id, task_date_create, task_deadline, task_file) VALUES (?, ?, ?, now(), ?, ?);";

    $stmt = get_prepare_stmt($link, $sql, $task);

    if (false === $stmt) {
        output_error_sql($link);
    }

    $result = mysqli_stmt_execute($stmt);

    if (false === $result) {
        output_error_sql($link);
    }

    $sql_result = mysqli_stmt_get_result($stmt);

    //Cохранение файла
    move_uploaded_file($tmp_name, 'uploads/' . $file_name);
//Переадресация пользователя на главную страницу после успешного добавления новой задачи
    if (false === $sql_result) {
        
        header("Location: index.php");

    } else {
        output_error_sql($link);
    }

}
//Подключение шаблона с формой
$form_content = include_template('add.php', [
    'projects' => $projects,
    'title' => 'Document'
]); 

print($form_content);