-- ==========================================================
-- Library Management System Database Schema
-- Compatible with MySQL 5.7+ / 8.0+ / MariaDB (XAMPP default)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS library_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_db;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    phone VARCHAR(20) DEFAULT NULL,
    department VARCHAR(100) DEFAULT 'Computer Science & Engineering',
    status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_role (role),
    INDEX idx_user_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Books Catalog Table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(30) NOT NULL UNIQUE,
    category VARCHAR(100) NOT NULL,
    publisher VARCHAR(150) DEFAULT NULL,
    publication_year INT DEFAULT NULL,
    total_copies INT NOT NULL DEFAULT 1,
    available_copies INT NOT NULL DEFAULT 1,
    description TEXT DEFAULT NULL,
    cover_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_book_category (category),
    INDEX idx_book_title (title),
    INDEX idx_book_author (author)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Book Issues & Returns Table
CREATE TABLE IF NOT EXISTS book_issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    status ENUM('issued', 'returned', 'overdue') NOT NULL DEFAULT 'issued',
    issued_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_issue_status (status),
    INDEX idx_issue_user (user_id),
    INDEX idx_issue_due (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Overdue Fines Table
CREATE TABLE IF NOT EXISTS fines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    issue_id INT NOT NULL UNIQUE,
    user_id INT NOT NULL,
    overdue_days INT NOT NULL DEFAULT 0,
    daily_rate DECIMAL(8,2) NOT NULL DEFAULT 5.00,
    fine_amount DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    status ENUM('unpaid', 'paid') NOT NULL DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (issue_id) REFERENCES book_issues(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_fine_status (status),
    INDEX idx_fine_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Fine Payments Table
CREATE TABLE IF NOT EXISTS fine_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fine_id INT NOT NULL,
    user_id INT NOT NULL,
    amount_paid DECIMAL(8,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'Cash',
    transaction_reference VARCHAR(100) DEFAULT NULL,
    received_by INT DEFAULT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fine_id) REFERENCES fines(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Study Materials Table
CREATE TABLE IF NOT EXISTS study_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    semester VARCHAR(20) NOT NULL,
    description TEXT DEFAULT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT DEFAULT 0,
    uploaded_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_material_acad (academic_year, semester)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Configuration Settings Table
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value VARCHAR(255) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
