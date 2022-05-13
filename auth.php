<?php

require_once('init.php');

$error_message = 'Пожалуйста, исправьте ошибки в форме';

//Массив с функциями для валидации полей формы запроса
$rules = [
    'email' => function ($value) {
        return validate_email($value);
    },
    'password' => function ($value) {
        return validate_field_length($value, 0, 255);
    }
];

//Валидация данных, введённых в поля формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $guest = filter_input_array(INPUT_POST, ['email' => FILTER_DEFAULT, 'password' => FILTER_DEFAULT], true);

    foreach ($guest as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }
    $errors = array_filter($errors);

    //Вывод сообщений о пустых полях формы
    if (count($errors)) {
        $form_content = include_template('auth.php', [
            'errors' => $errors,
            'error_message' => $error_message
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

    $guest_email = [$guest['email']];
    $user_data = [];

    //Проверка email-адреса на отсутствие в БД
    if (!count($errors)) {
        $sql = "SELECT user_email, user_password, user_id, user_name FROM users WHERE user_email = ?";
        $sql_result = get_result_prepare_sql($link, $sql, $guest_email);
        $user_data = mysqli_fetch_array($sql_result, MYSQLI_ASSOC);
    }
    //Перенаправление зарегистрированного пользователя при валидном пароле 
    if ($user_data && password_verify($_POST['password'], $user_data['user_password'])) {
        $user_password = $user_data['user_password'];
        $_SESSION['user_id'] = $user_data['user_id'];
        $_SESSION['user_name'] = $user_data['user_name'];
        header("Location: index.php");
    } else {
        //Вывод сообщения о неверном пароле при несовпаденых email или пароля
        $error_message = 'Вы ввели неверный email/пароль';
        $errors['email'] = "";
        $errors['password'] = "Неверный пароль";
        $form_content = include_template('auth.php', [
            'errors' => $errors,
            'error_message' => $error_message
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
}

//Подключение шаблона с формой
$form_content = include_template('auth.php');

$layout_content = include_template('layout.php', [
    'content' => $form_content,
    'title' => $title,
    'user' => $user,
    'year' => $year
]);

print($layout_content);
