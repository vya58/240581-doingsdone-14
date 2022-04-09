-- Запрос на добавление пользователtей 'anton' и 'ira'. Пароль (pass): хэшированный пароль пользователя. Хэш-сумма получена с помощью функции password_hash()
INSERT INTO users (user_date_add, user_email, user_name, user_password) 
     VALUES ('2020-07-31 17:30:09', 'anton@imail.ru', 'anton', '$2y$10$ck4xIa/ydoRjQpV87vjIIOWA/pvnWPAc1Ry0ELhft8KO.JpKHMHdm'),
('2020-10-20 14:10:45', 'ira@imail.ru', 'ira', '$2y$10$cTkl/mpuVvQ7gxoAIBK.leLknw8cAgOZsptyB.igngQ8AcjzG7EwG');

-- Запрос на добавление проекта 'Входящие' пользователя 'anton'
INSERT INTO projects (project_name, user_id) 
     VALUES ('Входящие', 1);

-- Запрос на добавление проекта 'Входящие' пользователя 'ira'
INSERT INTO projects (project_name, user_id) 
     VALUES ('Входящие', 2);

-- Запрос на добавление проекта 'Учеба' пользователя 'ira'
INSERT INTO projects (project_name, user_id) 
     VALUES ('Учеба', 2);

-- Запрос на добавление проекта 'Работа' пользователя 'anton'
INSERT INTO projects (project_name, user_id) 
     VALUES ('Работа', 1);

-- Запрос на добавление проекта 'Домашние дела' пользователя 'ira'
INSERT INTO projects (project_name, user_id) 
     VALUES ('Домашние дела', 2);

-- Запрос на добавление проекта 'Домашние дела' пользователя 'anton'
INSERT INTO projects (project_name, user_id) 
     VALUES ('Домашние дела', 1);

-- Запрос на добавление проекта 'Авто' пользователя 'anton'
INSERT INTO projects (project_name, user_id) 
     VALUES ('Авто', 1);

-- Запрос на добавление задачи 'Собеседование в IT компании' пользователя 'anton'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2020-07-31 17:50:12', 0, 'Собеседование в IT компании', '2020-08-15 00:00:00', 1,
            (SELECT project_id
               FROM projects
              WHERE user_id = 1
                AND project_name = 'Работа'
            )
       );

-- Запрос на добавление задачи 'Встреча с другом' пользователя 'anton'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
VALUES ('2020-08-01 18:14:57', 0, 'Встреча с другом', '2020-08-02 11:00:00', 1,
            (SELECT project_id
               FROM projects
              WHERE user_id = 1
                AND project_name = 'Входящие'
            )
       );


-- Запрос на добавление задачи 'Выполнить тестовое задание' пользователя 'anton'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2020-08-10 15:12:44', 0, 'Выполнить тестовое задание', '2020-08-25 00:00:00', 1,
            (SELECT project_id
               FROM projects
              WHERE user_id = 1
                AND project_name = 'Работа'
            )
       );

-- Запрос на добавление задачи 'Сделать задание первого раздела' пользователя 'ira'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2020-10-21 10:25:17', 0, 'Сделать задание первого раздела', '2020-10-30 00:00:00', 2,
            (SELECT project_id
               FROM projects
              WHERE user_id = 2
                AND project_name = 'Учеба'
            )
       );

-- Запрос на добавление задачи 'Встреча с другом' пользователя 'ira'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2022-04-01 19:04:00', 0, 'Встреча с другом', '2022-04-20 20:30:00', 2,
            (SELECT project_id
               FROM projects
              WHERE user_id = 2
                AND project_name = 'Входящие'
            )
       );

-- Запрос на добавление задачи 'Купить корм для кота' пользователя 'ira'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2022-04-07 18:51:01', 0, 'Купить корм для кота', '2020-04-14 00:00:00', 2,
            (SELECT project_id
               FROM projects
              WHERE user_id = 2
                AND project_name = 'Домашние дела'
            )
       );

-- Запрос на добавление задачи 'Заказать пиццу' пользователя 'anton'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2022-04-07 18:51:01', 0, 'Заказать пиццу', '2020-04-14 00:00:00', 1,
            (SELECT project_id
               FROM projects
              WHERE user_id = 1
                AND project_name = 'Домашние дела'
            )
       );

-- Запрос на получение списка всех задач для одного проекта (Работа), отсортированных по дате создания
SELECT task_name
  FROM tasks
  INNER JOIN projects
          ON tasks.project_id = projects.project_id
 WHERE project_name = 'Работа'
ORDER BY task_date_create;

-- Запрос на пометку задачи "'Собеседование в IT компании' пользователя 'anton'", как выполненной
UPDATE tasks
   SET task_status = 1
 WHERE user_id = 1
   AND task_name = 'Собеседование в IT компании'
   AND task_date_create = '2020-07-31 17:50:12';


-- Запрос на обновление названия задачи 'Собеседование в IT компании' по её идентификатору
UPDATE tasks
   SET task_name = 'Собеседование в ИТ компании'
 WHERE task_id = 1;