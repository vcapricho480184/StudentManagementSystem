-- =============================================
-- Student Management System — Database Schema
-- =============================================

CREATE DATABASE IF NOT EXISTS student_management_db;
USE student_management_db;

-- =============================================
-- Admins Table
-- =============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Students Table
-- =============================================
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    birthdate DATE DEFAULT NULL,
    course VARCHAR(100) NOT NULL,
    year_level TINYINT NOT NULL DEFAULT 1,
    address TEXT DEFAULT NULL,
    status ENUM('Active', 'Inactive', 'Graduated') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Default Admin Account
-- Username: admin
-- Password: admin123
-- =============================================
INSERT INTO admins (username, password, full_name) VALUES
('admin', '$2y$10$TmVwzOFF6xHbfBNy2c1s6.iinoJVFccIzeBqIo1onGu2ersqw6aFS', 'System Administrator');

-- =============================================
-- Sample Student Data (Optional)
-- =============================================
INSERT INTO students (student_id, first_name, last_name, email, phone, gender, birthdate, course, year_level, address, status) VALUES
('STU-2024-001', 'Juan', 'Dela Cruz', 'juan.delacruz@email.com', '09171234567', 'Male', '2003-05-15', 'BS Computer Science', 2, 'Manila, Philippines', 'Active'),
('STU-2024-002', 'Maria', 'Santos', 'maria.santos@email.com', '09189876543', 'Female', '2002-11-20', 'BS Information Technology', 3, 'Quezon City, Philippines', 'Active'),
('STU-2024-003', 'Jose', 'Rizal', 'jose.rizal@email.com', '09201112233', 'Male', '2001-06-19', 'BS Computer Engineering', 4, 'Calamba, Laguna', 'Graduated'),
('STU-2024-004', 'Ana', 'Reyes', 'ana.reyes@email.com', '09223344556', 'Female', '2004-01-10', 'BS Information Systems', 1, 'Cebu City, Philippines', 'Active'),
('STU-2024-005', 'Carlos', 'Garcia', 'carlos.garcia@email.com', '09157788990', 'Male', '2003-08-25', 'BS Computer Science', 2, 'Davao City, Philippines', 'Inactive');
