# Experiment 18: Integrated Full-Stack Library Management System

## Title
Design, implement, and deploy a complete, responsive, role-based Library Management System portal using HTML5, CSS3, JavaScript, PHP, and MySQL.

## Aim
To synthesize all concepts acquired in Experiments 01 through 17 into an end-to-end web application featuring:
1. Dual role authentication (Administrator/Librarian vs Student).
2. Book catalog management with full CRUD capability.
3. ACID-compliant book borrowing and return workflows with inventory decrement/increment.
4. Automatic overdue fine calculations at configurable daily rates.
5. Study material repository organized by academic year and semester.
6. Client-side and server-side validation with CSRF and SQL injection protection.

## System Architecture
```
[ Browser / Client ]
        |
        v  (HTTP / HTTPS)
[ Apache Web Server (XAMPP) ]
        |
        +---> [ Front Controller & Auth Router ] (index.php, auth/login.php)
        |
        +---> [ Student Portal Modules ] (student/dashboard.php, my_loans.php, my_fines.php)
        |
        +---> [ Admin / Librarian Modules ] (admin/dashboard.php, manage_books.php, issue_book.php, return_book.php)
        |
        +---> [ Database Layer (PDO Prepared Statements) ]
                    |
                    v
          [ MySQL Database: library_db ]
          (users, books, book_issues, fines, study_materials, settings)
```

## Database Schema Mapping
- `users`: Stores admin and student credentials with BCrypt password hashes.
- `books`: Stores catalog details with `total_copies` and `available_copies` inventory counters.
- `book_issues`: Tracks loan transactions with `issue_date`, `due_date`, and `return_date`.
- `fines`: Records overdue penalties computed as `overdue_days * daily_rate`.
- `study_materials`: Archives course notes organized by academic year and semester.
- `settings`: Configures portal settings such as daily fine rate and maximum loan periods.

## How to Run the Integrated Project
1. Copy or clone this repository into your XAMPP web root:
   `C:\xampp\htdocs\INTERNET-PROGRAMMING`
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin/` and import:
   - `database/schema.sql`
   - `database/seed.sql`
4. Access the portal at:
   `http://localhost/INTERNET-PROGRAMMING/index.php`
5. Test Login:
   - **Admin:** `admin@library.college.edu` | Password: `Admin@123`
   - **Student:** `192325016` | Password: `Student@123`
