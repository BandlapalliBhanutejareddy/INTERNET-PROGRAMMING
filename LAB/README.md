# Internet Programming Lab (CSA4315) &mdash; Experiment Index

This directory contains all 18 structured Internet Programming Lab experiments. Each experiment is completely runnable, self-contained, and accompanied by its individual `README.md` detailing the title, aim, algorithm, source code, sample inputs, expected outputs, and execution instructions.

---

## Complete Lab Experiments Index

| # | Experiment Folder | Topic & Core Concept | Key Technologies |
|---|-------------------|----------------------|------------------|
| **01** | [`01_basic_html/`](01_basic_html/README.md) | HTML5 Semantic Elements & Document Layout | Semantic Tags, Tables, Lists |
| **02** | [`02_html_forms/`](02_html_forms/README.md) | HTML5 Form Controls & Native Validation | Input Types, Select, Radios |
| **03** | [`03_css_styling/`](03_css_styling/README.md) | Responsive CSS3 Flexbox & Grid Layouts | CSS Grid, Flexbox, Media Queries |
| **04** | [`04_javascript_validation/`](04_javascript_validation/README.md) | Client-side Form Validation with Regex | JavaScript, Regex, DOM Errors |
| **05** | [`05_dom_manipulation/`](05_dom_manipulation/README.md) | DOM Manipulation & Real-time Filtering | Dynamic DOM, Events, Filtering |
| **06** | [`06_php_basics/`](06_php_basics/README.md) | PHP Variables, Arrays, Functions & Loops | PHP Types, Arrays, Foreach |
| **07** | [`07_php_forms/`](07_php_forms/README.md) | PHP Form Handling & Data Sanitization | POST/GET, htmlspecialchars, filter_var |
| **08** | [`08_php_sessions/`](08_php_sessions/README.md) | Session State Management & Route Protection | session_start, $_SESSION, Auth |
| **09** | [`09_php_mysql_connection/`](09_php_mysql_connection/README.md) | PDO MySQL Database Connection | PDO DSN, Exception Handling |
| **10** | [`10_crud_operations/`](10_crud_operations/README.md) | Database CRUD Operations with Prepared Statements | INSERT, SELECT, UPDATE, DELETE |
| **11** | [`11_user_authentication/`](11_user_authentication/README.md) | BCrypt User Authentication | password_hash(), password_verify() |
| **12** | [`12_book_search/`](12_book_search/README.md) | Multi-criteria Book Search & Filtering | SQL LIKE, Prepared Statements |
| **13** | [`13_library_management/`](13_library_management/README.md) | Inventory Aggregation & Stock Balance | SQL GROUP BY, SUM, COUNT |
| **14** | [`14_issue_return_system/`](14_issue_return_system/README.md) | ACID-compliant Book Checkout & Returns | Transactions, Row Locks (FOR UPDATE) |
| **15** | [`15_fine_calculation/`](15_fine_calculation/README.md) | Overdue Fine Calculation Engine | DateTime::diff, Configurable Rates |
| **16** | [`16_ajax_search/`](16_ajax_search/README.md) | Asynchronous Live Search via AJAX | Fetch API, JSON Backend |
| **17** | [`17_study_materials/`](17_study_materials/README.md) | Controlled File Uploads & Downloads | MIME Validation, Safe Renaming |
| **18** | [`18_complete_library_project/`](18_complete_library_project/README.md) | Integrated Full-Stack Library Portal | Complete End-to-End Application |

---

## How to Execute the Lab Programs

### Static Web Experiments (Exp 01 - 05, Exp 16)
These client-side experiments run directly in any modern browser:
1. Open your browser (Chrome / Edge / Firefox).
2. Press `Ctrl + O` and browse to the experiment's `index.html`.

### PHP Server Experiments (Exp 06 - 17)
Run using PHP built-in web server or XAMPP:
```bash
# From the project root folder:
php -S localhost:8000
```
Then visit: `http://localhost:8000/LAB/<experiment_folder>/`

### Integrated Portal (Exp 18 & Root Project)
The integrated Library Management System runs in the root directory:
1. Import `database/schema.sql` and `database/seed.sql` in phpMyAdmin.
2. Open `http://localhost:8000/index.php`.
