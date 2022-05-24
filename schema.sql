CREATE DATABASE doings_done
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    user_email VARCHAR(255) NOT NULL UNIQUE,
    user_name VARCHAR(50) NOT NULL,
    /* Длина столбца "user_password" фиксирована */
    user_password CHAR(255) NOT NULL,
    user_date_add DATETIME NOT NULL
);

CREATE TABLE projects (
    project_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    project_name VARCHAR(30) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (user_id)
);

ALTER TABLE projects
 ADD UNIQUE user_project_uk (user_id, project_name);

CREATE TABLE tasks (
    task_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    project_id INT NOT NULL,
    task_name VARCHAR(50) NOT NULL,
    task_date_create DATETIME NOT NULL,
    task_status TINYINT DEFAULT 0 check (task_status in (0, 1)),
    task_file VARCHAR(255) UNIQUE,
    task_deadline DATETIME,
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    FOREIGN KEY (project_id) REFERENCES projects (project_id)
);

/*
SQL-инструкция на создание полнотекстового индекса для поля «название» в таблице задач
*/
CREATE FULLTEXT INDEX task_title_search
ON tasks(task_name);
