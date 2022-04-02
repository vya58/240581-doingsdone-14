<?php
require_once('helpers.php');
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
$projects = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks = [
    [
    'Задача' => 'Собеседование в IT компании',
    'Дата выполнения' => '01.12.2019',
    'Категория' => 'Работа',
    'Выполнен' => false
    ],
    [
    'Задача' => 'Выполнить тестовое задание',
    'Дата выполнения' => '25.12.2019',
    'Категория' => 'Работа',
    'Выполнен' => false
    ],
    [
    'Задача' => 'Сделать задание первого раздела',
    'Дата выполнения' => '21.12.2019',
    'Категория' => 'Учеба',
    'Выполнен' => true
    ],
    [
    'Задача' => 'Встреча с другом',
    'Дата выполнения' => '22.12.2019',
    'Категория' => 'Входящие',
    'Выполнен' => false
    ],
    [
    'Задача' => 'Купить корм для кота',
    'Дата выполнения' => null,
    'Категория' => 'Домашние дела',
    'Выполнен' => false
    ],
    [
    'Задача' => 'Заказать пиццу',
    'Дата выполнения' => null,
    'Категория' => 'Домашние дела',
    'Выполнен' => false
    ]
];

function count_tasks_in_project(array $tasks, $project) {
    # Подсчет количества задач в проекте
    $count = 0;
    foreach ($tasks as $task) {
        if(htmlspecialchars($task['Категория']) == $project) {
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

