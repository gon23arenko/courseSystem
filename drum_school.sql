CREATE DATABASE drum_school;

USE drum_school;

CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    teacher_id INT NOT NULL,
    has_experience ENUM('Да', 'Нет') NOT NULL,
    age INT NOT NULL,
    course_duration_months INT NOT NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);

-- Добавим несколько преподавателей для примера
INSERT INTO teachers (full_name) VALUES 
('Иванов Петр Сергеевич'),
('Смирнова Анна Владимировна'),
('Кузнецов Дмитрий Алексеевич');