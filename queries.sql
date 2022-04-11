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

-- Запрос на получение списка из всех проектов для пользователя 'anton', отсортированных в алфавитном порядке
SELECT project_name
  FROM 
    users
    INNER JOIN projects
            ON users.user_id = projects.user_id
 WHERE user_name = 'anton'
ORDER BY project_name;

-- Запрос на получение списка всех задач для одного проекта (Работа), отсортированных по дате создания
SELECT task_name
  FROM tasks
 WHERE project_id = 4
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


-- Запрос на добавление нового пользователя 'Константин'

 INSERT INTO users (user_date_add, user_email, user_name, user_password) 
     VALUES ('2022-02-15 10:31:25', 'konst@imail.ru', 'Константин', '$2y$10$PVMR6YEMtQ.2Uw3qm.dDMud9GF.KDoRRKgTAZSVSGXKKK0Se8OK4S'),
;

-- Запрос на добавление проектов пользователя 'Константин'

INSERT INTO projects (project_name, user_id)
     VALUES ('Входящие', 4),
	  ('Учеба', 4),
	  ('Работа', 4),
	  ('Домашние дела', 4),
	  ('Авто', 4);


-- Запрос на добавление задачи 'Поиск подходящих вакансий' пользователя 'Константин'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2022-02-16 16:50:13', 1, 'Поиск подходящих вакансий', '2022-02-25 00:00:00', 4,
            (SELECT project_id
               FROM projects
              WHERE user_id = 4
                AND project_name = 'Работа'
            )
       );

-- Запрос на добавление задачи 'Составить резюме' пользователя 'Константин'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2022-02-16 18:00:23', 1, 'Составить резюме', '2022-02-20 00:00:00', 4,
            (SELECT project_id
               FROM projects
              WHERE user_id = 4
                AND project_name = 'Работа'
            )
       );



-- Запрос на добавление задачи 'Встреча с другом' пользователя 'Константин'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
VALUES ('2022-03-01 19:44:07', 0, 'Встреча с другом', '2020-03-07 19:00:00', 4,
            (SELECT project_id
               FROM projects
              WHERE user_id = 4
                AND project_name = 'Входящие'
            )
       );

-- Запрос на добавление задачи 'Собеседование в IT компании' пользователя 'Константин'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
VALUES ('2022-03-10 19:44:07', 1, 'Собеседование в IT компании', '2022-03-11 12:00:00', 4,
            (SELECT project_id
               FROM projects
              WHERE user_id = 4
                AND project_name = 'Работа'
            )
       );	 

-- Запрос на добавление задачи 'Выполнить тестовое задание' пользователя 'Константин'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2022-03-12 16:32:55', 1, 'Выполнить тестовое задание', '2022-03-19 00:00:00', 4,
            (SELECT project_id
               FROM projects
              WHERE user_id = 4
                AND project_name = 'Работа'
            )
       );

-- Запрос на добавление задачи 'Сделать задание второго раздела' пользователя 'Константин'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2022-04-02 10:25:17', 0, 'Сделать задание первого раздела', '2022-04-20 00:00:00', 4,
            (SELECT project_id
               FROM projects
              WHERE user_id = 4
                AND project_name = 'Учеба'
            )
       );

-- Запрос на добавление задачи 'Встреча с другом' пользователя 'Константин'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2022-04-08 10:08:00', 0, 'Встреча с другом', '2022-04-17 19:00:00', 4,
            (SELECT project_id
               FROM projects
              WHERE user_id = 4
                AND project_name = 'Входящие'
            )
       );

-- Запрос на добавление задачи 'Купить корм для кота' пользователя 'Константин'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2022-04-09 20:21:41', 0, 'Купить корм для кота', '2022-04-14 00:00:00', 4,
            (SELECT project_id
               FROM projects
              WHERE user_id = 4
                AND project_name = 'Домашние дела'
            )
       );

-- Запрос на добавление задачи 'Записаться на ТО' пользователя 'Константин'
INSERT INTO tasks (task_date_create, task_status, task_name, task_deadline, user_id, project_id) 
     VALUES ('2022-04-10 10:23:31', 0, 'Записаться на ТО', '2022-04-15 00:00:00', 4,
            (SELECT project_id
               FROM projects
              WHERE user_id = 4
                AND project_name = 'Авто'
            )
       );
