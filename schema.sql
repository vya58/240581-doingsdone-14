CREATE DATABASE doings_done;

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    email CHAR(255) NOT NULL UNIQUE,
    username CHAR(50) NOT NULL UNIQUE,
    pass CHAR(255) NOT NULL,
    date_add DATETIME
);

CREATE TABLE projects (
    project_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT (10) NOT NULL,
    name_project VARCHAR(30) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (user_id)
);

CREATE TABLE tasks (
    task_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT (10) NOT NULL,
    project_id INT (10) NOT NULL,
    name_task VARCHAR(50) NOT NULL,
    date_create DATETIME,
    task_status TINYINT(1) DEFAULT 0,
    file_task VARCHAR(255) UNIQUE,
    deadline DATETIME,
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    FOREIGN KEY (project_id) REFERENCES projects (project_id)
);

