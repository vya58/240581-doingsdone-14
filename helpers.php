<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function validate_date(string $date) {
    $format_to_check = 'YYYY-mm-dd';
    $dateTimeObj = date_create_from_format($format_to_check, $date);
    if ($dateTimeObj !== false && array_sum(date_get_last_errors()) === 0) {
        return 'Введите дату в формате «ГГГГ-ММ-ДД»';
    }
    return false;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function output_error_sql($link) {
    # вывод ошибки запроса в базу данных
    $error = mysqli_error($link);
    $content = include_template('error.php', ['error' => $error]);
    $layout_content = include_template('layout.php', [
        'content' => $content,
        'title' => 'Дела в порядке'
    ]);
    print($layout_content);
    exit;
}

/**
 * Для запросов SELECT!
 * Выдаёт результат подготовленного выражения на основе SQL запроса на чтение из ДБ и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_result результат выполнения подготовленного запроса
 */
function get_result_prepare_sql($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if (false === $stmt) {
        return false;
    }
    
    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            return false;
        }
    }

    $result = mysqli_stmt_execute($stmt);

    if (false === $result) {
        return false;
    }
    
    return mysqli_stmt_get_result($stmt);
}

function get_post_val($name) {
    return filter_input(INPUT_POST, $name);
}


// Проверка проекта по его id на наличие в списке проектов пользователя
function validate_project($id, $allowed_list) {
    if (!in_array($id, $allowed_list)) {
        return 'Указан несуществующий проект';
    }
    return null;
}

// Проверка соответсвия длины строки на min и max допустимое значение
function validate_field_length($value, $min, $max) {
    if ($value) {
        $len = strlen($value);
        if ($len < $min) {
            return "Минимальное количество символов должно быть больше $min";
        }
        if ($len > $max) {
            return "Максимальное количество символов должно быть меньше $max";
        }
        return null;
    }
    return "Заполните поле!";
}

// Валидация email
function validate_email($value) {
    
    if (!$value) {
        return 'Поле "e-mail" должно быть заполнено!';
    }

    $value = filter_var($value, FILTER_VALIDATE_EMAIL);
    if (!$value) {
        return 'E-mail введён некорректно.';
    }
    return null;
}

/* 
Для запросов INSERT, UPDATE, DELETE!
Подготавливает SQL выражение к выполнению
$link mysqli - Ресурс соединения
$sql string - SQL запрос с плейсхолдерами вместо значений
array $data - Данные для вставки на место плейсхолдеров
*/
function get_prepare_stmt($link, $sql,  array $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if (false === $stmt) {
        return false;
    }
    
    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            return false;
        }
    }

    return $stmt;
}
