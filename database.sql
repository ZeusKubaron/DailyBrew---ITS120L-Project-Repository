-- DailyBrew Database Setup Script
-- Run this in phpMyAdmin or MySQL to create the database

CREATE DATABASE IF NOT EXISTS dailybrew;
USE dailybrew;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    tour_completed TINYINT(1) DEFAULT 0
);

-- Academic Schedule Table
CREATE TABLE IF NOT EXISTS academic_schedule (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    subject VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    color VARCHAR(20) DEFAULT '#4a90d9',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tasks Table
CREATE TABLE IF NOT EXISTS tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    due_date DATE NOT NULL,
    due_time TIME,
    ai_priority ENUM('high', 'medium', 'low') DEFAULT 'medium',
    user_priority ENUM('high', 'medium', 'low'),
    complexity INT DEFAULT 5,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Study Blocks Table
CREATE TABLE IF NOT EXISTS study_blocks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    task_id INT,
    title VARCHAR(200) NOT NULL,
    scheduled_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    profile ENUM('early_crammer', 'seamless', 'late_crammer') DEFAULT 'seamless',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE SET NULL
);

-- User Preferences Table
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    earliest_time_start TIME DEFAULT '08:00:00',
    latest_time_end TIME DEFAULT '22:00:00',
    study_block_duration INT DEFAULT 30,
    default_profile ENUM('early_crammer', 'seamless', 'late_crammer') DEFAULT 'seamless',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default preferences trigger (runs on user creation)
DELIMITER //
CREATE TRIGGER after_user_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO user_preferences (user_id) VALUES (NEW.id);
END//
DELIMITER ;

-- Sample data for testing (optional)
-- INSERT INTO users (first_name, last_name, email, password) VALUES 
-- ('John', 'Doe', 'john@example.com', '$2y$10$abcdefghijklmnopqrstuv');

