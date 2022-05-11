<?php

require_once('init.php');

//Массив с функциями для валидации полей формы запроса
$rules = [
    'email' => function($value) {
        return validate_email($value);
    },

    'password' => function($value) {
        return validate_field_length($value, 6, 20);
    },

    'name' => function($value) {
        return validate_field_length($value, 0, 50);
    }
];

//Валидация данных, введённых в поля формы
if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $new_user = filter_input_array(INPUT_POST, ['email' => FILTER_DEFAULT, 'name' => FILTER_DEFAULT, 'password' => FILTER_DEFAULT], true);

    foreach ($new_user as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }
    $errors = array_filter($errors);
    
    $new_user_email = [$new_user['email']];
   
    //Проверка email-адреса на отсутствие в БД
    $sql = "SELECT user_email FROM users WHERE user_email = ?";

    $sql_result = get_result_prepare_sql($link, $sql, $new_user_email);
    
    if (mysqli_fetch_row($sql_result)) {
        $errors['email'] = 'Указанный e-mail уже используется другим пользователем.';
    }
   
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
    $new_user['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
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
    'title' => 'Document'
]); 

print($form_content);

