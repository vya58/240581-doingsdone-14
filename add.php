<?php

require_once('init.php');
$user = intval(4);
$sql_data = [$user];
$sql = "SELECT project_name, p.project_id, COUNT(task_name) AS count_tasks FROM projects p INNER JOIN tasks t ON t.project_id = p.project_id WHERE p.user_id = ? GROUP BY project_name, p.project_id";

$sql_result = get_result_prepare_sql($link, $sql, $sql_data);

if ($sql_result) {
    $projects = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
    $project_ids = array_column($projects, 'project_id');
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required = [$_POST['name'], $_POST['project'], $_POST['date']];
    
    $errors = [];

    $rules = [
        'name' => function($value) {
            return is_length_valid($value, 50);
        },
        'project' => function($value) use ($project_ids) {
            return is_project_valid($value, $project_ids);
        },
        'date' => function($date) {
            return is_date_valid($date);
        }
    ];

    $task = filter_input_array(INPUT_POST, ['name' => FILTER_DEFAULT, 'project' => FILTER_DEFAULT, 'date' => FILTER_DEFAULT], true);

    foreach ($task as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    
        if (in_array($key, $required) && empty($value)) {
            $errors[$key] = "Поле $key надо заполнить!";
        }
    };
    
    $errors = array_filter($errors);
   
    if (!empty($_FILES['file']['name'])) {
        $tmp_name = $_FILES['file']['tmp_name'];
        $file_type = $_FILES['file']['type'];
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_name_separated = explode(".", $file_name);
        $file_extension = strtolower(end($file_name_separated));
        if (!in_array($file_extension, $permitted_file_extensions)) {
            $errors['file'] = 'Недопустимый тип файла';
            } elseif ($file_size > 2097152) {
                $errors['file'] = 'Превышен максимальный размер файла';
            } else {
                $file_name = uniqid() . "." . $file_extension;
                move_uploaded_file($tmp_name, 'uploads/' . $file_name);
                $task['task_file'] = $file_name;
            }
    } else {
        $task['task_file'] = null;
    }

    if (count($errors)) {
        
        $form_content = include_template('add.php', [
            'projects' => $projects,
            'title' => 'Document',
            'errors' => $errors
        ]); 

        print($form_content);
    } else {
        if (empty($_POST['date'])) {
            $task['date'] = null;
        }

        $sql = "INSERT INTO tasks (user_id, task_name, project_id, task_date_create,task_deadline, task_file) VALUES (4, ?, ?, now(), ?, ?);";

        $stmt = get_result_prepare_sql($link, $sql, $task);
      
        if (false === $stmt) {
            header("Location: index.php");
        }
    }
}

$form_content = include_template('add.php', [
    'projects' => $projects,
    'title' => 'Document'
]); 

print($form_content);