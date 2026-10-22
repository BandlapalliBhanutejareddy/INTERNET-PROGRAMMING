# SIMATS Central Digital Library Management System & IP Lab Repository

[![Course](https://img.shields.io/badge/Course-CSA4315%20Internet%20Programming-blue.svg)](https://github.com/BandlapalliBhanutejareddy/INTERNET-PROGRAMMING)
[![Department](https://img.shields.io/badge/Department-Computer%20Science%20%26%20Engineering-green.svg)](https://github.com/BandlapalliBhanutejareddy/INTERNET-PROGRAMMING)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B%20%2F%208.x-purple.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-orange.svg)](https://www.mysql.com/)

A full-stack, responsive academic **Library Management System** website and comprehensive **Internet Programming Lab (CSA4315)** practical repository. Developed for the Department of Computer Science & Engineering at Saveetha School of Engineering (SIMATS).

---

## 1. Project Overview

This repository contains:
1. **The Core Library Management System**: A multi-user, database-driven web application featuring dedicated portals for **Librarians/Administrators** and **Students**, book inventory tracking, ACID-compliant circulation (issue/return), automated overdue fine calculations, and a study materials repository.
2. **The 18 IP Lab Experiments (`LAB/`)**: A structured collection of 18 beginner-friendly, standalone practical programs covering HTML5 semantics, CSS3 layouts, JavaScript validation, DOM manipulation, PHP fundamentals, sessions, MySQL PDO operations, CRUD, BCrypt authentication, AJAX search, secure file uploads, and full-stack integration.

---

## 2. Technology Stack

- **Structure**: HTML5 Semantic markup (`<header>`, `<nav>`, `<section>`, `<article>`, `<footer>`).
- **Styling**: Modern Vanilla CSS3 with CSS Grid, Flexbox, custom design tokens, dark mode accents, responsive media queries, and micro-animations.
- **Client-Side Logic**: Vanilla JavaScript (ES6+) for form validation, real-time table filtering, and asynchronous Fetch API.
- **Server-Side Architecture**: PHP (compatible with PHP 7.4+ and PHP 8.x) utilizing object-oriented PDO, secure sessions, CSRF tokens, and prepared statements.
- **Database Engine**: MySQL / MariaDB (XAMPP standard) with InnoDB engine, foreign keys, cascade constraints, and row-level locking (`FOR UPDATE`).

---

## 3. Key Application Features

### A. Librarian / Administrator Portal
- **KPI Metrics Dashboard**: Real-time counters for catalog titles, physical copies, active loans, overdue books, enrolled students, and fine revenue.
- **Book Inventory Management**: Add new books, update metadata, track total and available copies, and safely delete books (with safeguards against deleting checked-out volumes).
- **Circulation Issue & Return**:
  - Issue books with student loan quota verification (max 3 books per student) and automated due date assignment.
  - Process book returns with instantaneous inventory restock and automated overdue fine calculations.
- **Fine Management**: Track unpaid vs settled overdue fines and record in-person cash payments.
- **Study Materials Publisher**: Upload lecture notes, syllabi, and question banks filtered by academic year and semester.
- **Student Registry**: Inspect student borrowing records, active loans, and fine balances.
- **Configurable Settings**: Modify daily fine rates (default: ₹5.00/day) and standard loan periods (default: 14 days).

### B. Student Portal
- **Self-Service Dashboard**: Quick glance at active borrowed books, return deadlines, and pending fines.
- **Personal Borrowing History**: Permanent audit trail of all issued, returned, and overdue volumes.
- **My Fines**: Itemized breakdown of overdue penalties with days late and payment status.
- **Searchable Book Catalog**: Real-time filtering by keyword, author, category, or ISBN.
- **Study Materials Download**: Browse and securely download approved academic notes by semester and subject.
- **Profile Management**: Update contact details and change passwords securely.

---

## 4. Database Architecture & Setup

### Database Tables Overview
The database (`library_db`) is normalized in 3NF and includes 7 core tables:
- `users`: Stores administrator and student accounts with BCrypt-hashed passwords.
- `books`: Stores catalog details with `total_copies` and `available_copies` counters.
- `book_issues`: Tracks borrowing transactions, due dates, return dates, and loan statuses (`issued`, `returned`, `overdue`).
- `fines`: Records late return penalties tied to specific issue transactions.
- `fine_payments`: Audit log of fine payments and settlement methods.
- `study_materials`: Archives course notes organized by academic year and semester.
- `settings`: Key-value configuration for library policies.

### Local Installation in XAMPP
1. **Clone or Copy Repository**:
   Place the project folder into your XAMPP web root:
   ```bash
   C:\xampp\htdocs\INTERNET-PROGRAMMING
   ```
2. **Start Services**:
   Open **XAMPP Control Panel** and start both **Apache** and **MySQL**.
3. **Import Database Schema & Sample Data**:
   - Open your browser and navigate to: `http://localhost/phpmyadmin/`
   - Click **New** and create a database named `library_db` (or simply import the files below).
   - In the **Import** tab, upload and run `database/schema.sql`.
   - Then upload and run `database/seed.sql` to populate sample books, students, and circulation records.
4. **Launch the Application**:
   Navigate to:
   ```text
   http://localhost/INTERNET-PROGRAMMING/index.php
   ```

### Default Credentials
| Role | Identifier / Email | Password |
|------|--------------------|----------|
| **Librarian (Admin)** | `admin@library.college.edu` | `Admin@123` |
| **Student (Bhanuteja)** | `192325016` (or `bhanu@college.edu`) | `Student@123` |
| **Student (Prasanth)** | `192325037` (or `prasanth@college.edu`) | `Student@123` |
| **Student (Dhanusha)** | `192325012` (or `dhanusha@college.edu`) | `Student@123` |

---

## 5. Repository Directory Structure

```text
INTERNET-PROGRAMMING/
├── index.php                      # Public homepage & hero catalog showcase
├── .gitignore                     # Git ignore rules for caches, secrets, OS metadata
├── README.md                      # Primary project documentation
│
├── config/
│   └── config.php                 # Database credentials, session security, CSRF & helper functions
│
├── database/
│   ├── schema.sql                 # MySQL table definitions, constraints, foreign keys
│   └── seed.sql                   # Realistic sample data for books, users, issues, and fines
│
├── assets/
│   ├── css/
│   │   └── style.css              # Custom CSS3 academic portal design system
│   └── js/
│       └── main.js                # Form validation, live table filters, alert auto-dismiss
│
├── includes/
│   ├── header.php                 # Global HTML head, fonts, flash alerts
│   ├── navbar.php                 # Navigation bar with role-based links & user profile
│   └── footer.php                 # Global footer with university credits & script links
│
├── auth/
│   ├── login.php                  # Authentication handler supporting both admin & student
│   ├── register.php               # Student account enrollment with client/server validation
│   └── logout.php                 # Secure session destruction and cookie cleanup
│
├── books/
│   ├── catalog.php                # Public book catalog with multi-criteria filtering
│   ├── view.php                   # Detailed book profile, metadata, and availability
│   └── search_ajax.php            # JSON API endpoint for asynchronous live search
│
├── student/
│   ├── dashboard.php              # Student overview (active loans, overdue items, fines)
│   ├── my_loans.php               # Personal borrowing records with return due dates
│   ├── my_fines.php               # Itemized list of overdue penalties and payment status
│   ├── materials.php              # Study material repository filtered by year and semester
│   └── profile.php                # Student profile details and password modification
│
├── admin/
│   ├── dashboard.php              # Librarian KPI dashboard with live circulation metrics
│   ├── manage_books.php           # Catalog table with search, edit, issue, and delete actions
│   ├── add_book.php               # Add new book with duplicate ISBN checks
│   ├── edit_book.php              # Update existing book details and total physical holdings
│   ├── delete_book.php            # Safe deletion with active loan safeguards
│   ├── issue_book.php             # Issue book with student quota and stock validation
│   ├── return_book.php            # Process book return and compute overdue fines
│   ├── transactions.php           # Master audit log of all borrowing transactions
│   ├── fines.php                  # Overdue fine ledger with in-person settlement
│   ├── materials.php              # Publish and manage lecture notes and lab manuals
│   ├── students.php               # Student account directory and loan quota overview
│   └── settings.php               # Modify daily fine rate and standard loan durations
│
├── materials/
│   └── download.php               # Secure download streaming script preventing path traversal
│
├── ASSESSMENTS/
│   └── 05_CO1_AT1.pdf             # Preserved academic assessment document
├── CAPSTONE/                      # Preserved directory for capstone project artifacts
├── HAND'S ON/                     # Preserved directory for class hands-on activities
│
└── LAB/                           # Complete 18 IP Lab Experiments Index
    ├── README.md                  # Comprehensive index & execution manual for all 18 experiments
    ├── 01_basic_html/             # Exp 1: HTML5 Document Structure & Elements
    ├── 02_html_forms/             # Exp 2: HTML5 Form Controls & Validation
    ├── 03_css_styling/            # Exp 3: Responsive CSS3 Flexbox & Grid
    ├── 04_javascript_validation/  # Exp 4: Client-Side Regex Form Validation
    ├── 05_dom_manipulation/       # Exp 5: Dynamic DOM Traversal & Real-Time Filtering
    ├── 06_php_basics/             # Exp 6: Variables, Arrays, Functions & Loops
    ├── 07_php_forms/              # Exp 7: POST/GET Handling & Data Sanitization
    ├── 08_php_sessions/           # Exp 8: Session State Management & Route Protection
    ├── 09_php_mysql_connection/   # Exp 9: PDO Database Connection & Error Handling
    ├── 10_crud_operations/        # Exp 10: Database CRUD Operations with Prepared Statements
    ├── 11_user_authentication/    # Exp 11: BCrypt Password Hashing (hash & verify)
    ├── 12_book_search/            # Exp 12: Dynamic Multi-Criteria SQL Search
    ├── 13_library_management/     # Exp 13: Catalog Aggregations & Inventory Tracking
    ├── 14_issue_return_system/    # Exp 14: ACID Transactions & Row Locking
    ├── 15_fine_calculation/       # Exp 15: Automated Overdue Penalty Calculations
    ├── 16_ajax_search/            # Exp 16: Asynchronous Live Search with Fetch API
    ├── 17_study_materials/        # Exp 17: Controlled File Upload & Streaming Download
    └── 18_complete_library_project/# Exp 18: Integrated Full-Stack Architecture Documentation
```

---

## 6. Internet Programming Lab Experiments (Quick Reference)

All 18 lab programs are housed inside [`LAB/`](LAB/README.md) and include standalone source code and an individual `README.md` containing the title, aim, algorithm in numbered steps, sample input, expected output, and execution instructions:

1. **[01 Basic HTML](LAB/01_basic_html/README.md)**: Semantic page structure (`header`, `nav`, `section`, `table`, `lists`).
2. **[02 HTML Forms](LAB/02_html_forms/README.md)**: Student registration form with diverse HTML5 controls and native validation.
3. **[03 CSS Styling](LAB/03_css_styling/README.md)**: Responsive 3-column book card catalog using CSS Grid, Flexbox, and media queries.
4. **[04 JavaScript Validation](LAB/04_javascript_validation/README.md)**: Client-side validation using regular expressions and DOM error reporting.
5. **[05 DOM Manipulation](LAB/05_dom_manipulation/README.md)**: Dynamic book item creation, deletion, and real-time live search filtering.
6. **[06 PHP Basics](LAB/06_php_basics/README.md)**: Scalar types, associative arrays, fine calculation functions, and loops.
7. **[07 PHP Forms](LAB/07_php_forms/README.md)**: Server-side validation, `POST` data processing, and `htmlspecialchars()` sanitization.
8. **[08 PHP Sessions](LAB/08_php_sessions/README.md)**: State preservation across requests, login protection, and session destruction.
9. **[09 PHP MySQL Connection](LAB/09_php_mysql_connection/README.md)**: Exception-handled MySQL database connection using PDO.
10. **[10 CRUD Operations](LAB/10_crud_operations/README.md)**: Create, Read, Update, and Delete operations using prepared statements.
11. **[11 User Authentication](LAB/11_user_authentication/README.md)**: Salted password storage with `password_hash()` and verification with `password_verify()`.
12. **[12 Book Search](LAB/12_book_search/README.md)**: Parameterized SQL `LIKE` queries for searching by title, author, or ISBN.
13. **[13 Library Management](LAB/13_library_management/README.md)**: Stock balance and shelf availability aggregation with SQL `GROUP BY`.
14. **[14 Issue & Return System](LAB/14_issue_return_system/README.md)**: Transactional checkouts using `beginTransaction()`, `commit()`, and `rollBack()`.
15. **[15 Fine Calculation](LAB/15_fine_calculation/README.md)**: Overdue fine calculation engine using PHP `DateTime::diff`.
16. **[16 AJAX Search](LAB/16_ajax_search/README.md)**: Asynchronous catalog search consuming JSON backend using JavaScript `fetch()`.
17. **[17 Study Materials](LAB/17_study_materials/README.md)**: File upload validation (MIME checking, size limits, safe renaming).
18. **[18 Complete Library Project](LAB/18_complete_library_project/README.md)**: Complete full-stack system architecture, component mapping, and execution walkthrough.

---

## 7. Security Best Practices Implemented

- **SQL Injection Defense**: 100% of database queries use PDO parameterized prepared statements (`execute([$param])`). Emulated prepares are disabled.
- **Cross-Site Scripting (XSS) Prevention**: All dynamic output rendered to the browser passes through `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- **CSRF Token Validation**: State-changing POST requests require a valid cryptographic session token (`csrf_token()`).
- **Cryptographic Password Hashing**: User passwords are encrypted with industry-standard BCrypt (cost 10) via `password_hash()`.
- **Role-Based Authorization**: Administrative routes strictly enforce `require_admin()` on the server side; student endpoints isolate records by `user_id`.
- **Path Traversal Protection**: Study material download requests validate absolute paths against allowed base directories before streaming.

---

## 8. Author & Repository Information

- **Student Name**: Bandlapalli Bhanuteja Reddy
- **Registration Number**: 192325016
- **Institution**: Saveetha School of Engineering (SIMATS)
- **Course**: Internet Programming (CSA4315) &bull; Semester 5
- **GitHub Repository**: [https://github.com/BandlapalliBhanutejareddy/INTERNET-PROGRAMMING](https://github.com/BandlapalliBhanutejareddy/INTERNET-PROGRAMMING)\n