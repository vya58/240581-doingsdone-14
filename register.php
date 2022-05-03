<?php

require_once('init.php');

//Запрос в БД имеющихся email-адресов для проверки на уникальность при регистрации
$sql = mysqli_query($link, "SELECT user_email FROM users");

$sql_result = mysqli_fetch_all($sql, MYSQLI_ASSOC);

foreach ($sql_result as $email) {
    $emails[]= $email['user_email'];
 }

$errors = [];

//Массив с функциями для валидации полей формы запроса
$rules = [
    'email' => function($value) use ($emails) {
        return is_email_valid($value, $emails);
    },

    'password' => function($value) {
        return is_length_valid($value, 6, 20);
    },
    
    'name' => function($value) {
        return is_length_valid($value, 0, 50);
    },
];

//Валидация данных, введённых в поля формы
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required = [$_POST['email'], $_POST['password'], $_POST['name']];
    
    $new_user = filter_input_array(INPUT_POST, ['email' => FILTER_DEFAULT, 'name' => FILTER_DEFAULT, 'password' => FILTER_DEFAULT], true);
    
    foreach ($new_user as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }

    if (empty($_POST['password'])) {
        $errors['password'] = "Введите пароль!";
    }

    if (empty($_POST['name'])) {
        $errors['name'] = "Введите имя!";
    } 

    $errors = array_filter($errors);

//Вывод сообщений об ошибочно заполненных полях формы
    if (count($errors)) {
        $form_content = include_template('register.php', [
            'title' => 'Document',
            'errors' => $errors
        ]);
        print($form_content);
        exit;
    }
//Хеширование пароля 
    $hech = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $new_user['password'] = $hech;
//Запрос в БД на добавление нового пользователя
    $sql = "INSERT INTO users (user_email, user_name, user_password, user_date_add) VALUES (?, ?, ?, now());";

    $stmt = get_prepare_stmt($link, $sql, $new_user);

    if (false === $stmt) {
        output_error_sql($link);
    }

    $result = mysqli_stmt_execute($stmt);

    if (false === $result) {
        output_error_sql($link);
    }

    $sql_result = mysqli_stmt_get_result($stmt);

//Переадресация пользователя на главную страницу после успешной регистрации
    if (false === $sql_result) {
        
        header("Location: index.php");

    } else {
        output_error_sql($link);
    }

}

//Подключение шаблона с формой
$form_content = include_template('register.php', [
    'projects' => $projects,
    'title' => 'Document'
]); 

print($form_content);

