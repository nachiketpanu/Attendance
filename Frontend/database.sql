CREATE DATABASE attendance_db;
USE attendance_db;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    role ENUM('admin','user') NOT NULL
);

-- Insert Users
INSERT INTO users (name, role) VALUES 
('Nisha', 'admin'),
('Pranav', 'user'),
('Megha', 'user'),
('Geeta', 'user');

-- Attendance Table
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('Check-In','Check-Out') NOT NULL,
    photo LONGBLOB NOT NULL,
    time DATETIME NOT NULL,
    duration_hours decimal(5,2) DEFAULT 0.00,
    salary decimal(10,2) DEFAULT 0.00
    FOREIGN KEY (user_id) REFERENCES users(id)
);
