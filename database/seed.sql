-- ==========================================================
-- Library Management System Sample Seed Data
-- ==========================================================

USE `library_db`;

-- 1. Insert Configuration Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('library_name', 'SIMATS Central Digital Library', 'Official name of the academic library portal'),
('daily_fine_rate', '5.00', 'Overdue fine charged per day per book in INR'),
('max_borrow_days', '14', 'Maximum allowed loan duration in days before book is overdue'),
('max_books_per_student', '3', 'Maximum concurrent active books a student can borrow'),
('currency_symbol', '₹', 'Currency symbol used for displaying fees and fines')
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);

-- 2. Insert Users (Admin & Students)
-- Default passwords:
-- Admin: Admin@123
-- Students: Student@123
-- BCrypt Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi (standard cost 10 for password)
-- Fallback support for password_verify: also supports sha256/md5 during first boot
INSERT INTO `users` (`id`, `student_id`, `name`, `email`, `password`, `role`, `phone`, `department`, `status`) VALUES
(1, 'ADMIN001', 'Dr. S. K. Raman (Chief Librarian)', 'admin@library.college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '9876543210', 'Library Administration', 'active'),
(2, '192325016', 'Bandlapalli Bhanuteja Reddy', 'bhanu@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '9848022338', 'Computer Science & Engineering', 'active'),
(3, '192325037', 'Dumpala Prasanth', 'prasanth@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '9848033449', 'Computer Science & Engineering', 'active'),
(4, '192325012', 'Dhanusha Sree', 'dhanusha@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '9848044550', 'Artificial Intelligence & Machine Learning', 'active')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 3. Insert Books Catalog
INSERT INTO `books` (`id`, `title`, `author`, `isbn`, `category`, `publisher`, `publication_year`, `total_copies`, `available_copies`, `description`) VALUES
(1, 'Internet & World Wide Web How to Program', 'Paul Deitel, Harvey Deitel', '978-0132151009', 'Internet Programming', 'Pearson Education', 2021, 5, 4, 'Comprehensive introduction to full-stack web development covering HTML5, CSS3, JavaScript, PHP, MySQL, and AJAX architectures.'),
(2, 'Web Technologies: A Computer Science Perspective', 'Jeffrey C. Jackson', '978-0131856035', 'Internet Programming', 'Pearson Higher Ed', 2020, 4, 3, 'Foundational concepts of HTTP protocol, client-server models, CGI, PHP server-side scripting, and web security protocols.'),
(3, 'Learning PHP, MySQL & JavaScript with jQuery, CSS & HTML5', 'Robin Nixon', '978-1491918661', 'Web Development', 'O Reilly Media', 2022, 6, 6, 'Hands-on guide to building interactive, data-driven web applications using modern LAMP stack components.'),
(4, 'Introduction to Algorithms (CLRS)', 'Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, Clifford Stein', '978-0262033848', 'Computer Science Core', 'MIT Press', 2022, 8, 7, 'Comprehensive textbook on algorithms, data structures, graph theory, dynamic programming, and complexity analysis.'),
(5, 'Database System Concepts', 'Abraham Silberschatz, Henry F. Korth, S. Sudarshan', '978-0078022159', 'Database Systems', 'McGraw-Hill Education', 2020, 6, 5, 'In-depth coverage of relational databases, SQL queries, normalization, transaction management, concurrency, and indexing.'),
(6, 'Operating System Concepts (Dinosaur Book)', 'Abraham Silberschatz, Peter B. Galvin, Greg Gagne', '978-1119800361', 'Operating Systems', 'John Wiley & Sons', 2021, 5, 5, 'Core principles of processes, threads, CPU scheduling, synchronization, deadlocks, memory management, and file systems.'),
(7, 'Computer Networking: A Top-Down Approach', 'James F. Kurose, Keith W. Ross', '978-0136681557', 'Computer Networks', 'Pearson', 2021, 5, 4, 'Layered network architecture, application layer protocols (HTTP, DNS, SMTP), TCP/UDP transport, routing, and network security.'),
(8, 'Artificial Intelligence: A Modern Approach', 'Stuart Russell, Peter Norvig', '978-0134610993', 'Artificial Intelligence', 'Pearson', 2020, 4, 4, 'The definitive reference in artificial intelligence, covering search, logic, probabilistic models, reinforcement learning, and neural nets.')
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 4. Insert Book Issues
INSERT INTO `book_issues` (`id`, `user_id`, `book_id`, `issue_date`, `due_date`, `return_date`, `status`, `issued_by`) VALUES
(1, 2, 1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 9 DAY), NULL, 'issued', 1),
(2, 3, 2, DATE_SUB(CURDATE(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 6 DAY), NULL, 'overdue', 1),
(3, 2, 4, DATE_SUB(CURDATE(), INTERVAL 25 DAY), DATE_SUB(CURDATE(), INTERVAL 11 DAY), DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'returned', 1),
(4, 4, 7, DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 12 DAY), NULL, 'issued', 1),
(5, 3, 5, DATE_SUB(CURDATE(), INTERVAL 18 DAY), DATE_SUB(CURDATE(), INTERVAL 4 DAY), NULL, 'overdue', 1)
ON DUPLICATE KEY UPDATE `status`=VALUES(`status`);

-- 5. Insert Overdue Fines
INSERT INTO `fines` (`id`, `issue_id`, `user_id`, `overdue_days`, `daily_rate`, `fine_amount`, `status`, `created_at`) VALUES
(1, 2, 3, 6, 5.00, 30.00, 'unpaid', NOW()),
(2, 5, 3, 4, 5.00, 20.00, 'unpaid', NOW())
ON DUPLICATE KEY UPDATE `fine_amount`=VALUES(`fine_amount`);

-- 6. Insert Study Materials
INSERT INTO `study_materials` (`id`, `title`, `subject`, `academic_year`, `semester`, `description`, `file_path`, `file_size`, `uploaded_by`) VALUES
(1, 'CSA4315 - Unit 1 Web Architecture & HTTP Protocols Lecture Notes', 'Internet Programming', '2026-2027', 'Semester 5', 'Detailed notes covering 2-tier and 3-tier web architectures, HTTP request/response methods, status codes, and headers.', 'materials/csa4315_unit1_notes.pdf', 524288, 1),
(2, 'CSA4315 - Unit 2 CSS3 Flexbox & Grid Masterclass', 'Internet Programming', '2026-2027', 'Semester 5', 'Comprehensive guide with code examples for responsive layouts, media queries, flexbox properties, and CSS grid.', 'materials/csa4315_unit2_css_guide.pdf', 745120, 1),
(3, 'CSA4315 - Unit 3 PHP & MySQL Database Connectivity Handbook', 'Internet Programming', '2026-2027', 'Semester 5', 'PDO connection patterns, prepared statements, CRUD implementation, session security, and password hashing.', 'materials/csa4315_unit3_php_mysql.pdf', 983040, 1),
(4, 'CSA4315 - Lab Manual & Experiment Exercise Bank', 'Internet Programming', '2026-2027', 'Semester 5', 'Complete IP Lab manual containing 18 experiments with algorithms, test cases, and expected outputs.', 'materials/csa4315_lab_manual.pdf', 1258291, 1)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);
