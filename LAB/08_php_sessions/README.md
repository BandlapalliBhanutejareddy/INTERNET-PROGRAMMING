# Experiment 08: PHP Session State Management & Access Control

## Title
Demonstrate stateful authentication using PHP sessions (`session_start`, `$_SESSION`, session protection, and `session_destroy`).

## Aim
To preserve user login state across multiple page requests, protect restricted routes against unauthenticated access, and terminate sessions securely.

## Algorithm
1. Invoke `session_start()` at the beginning of each PHP script.
2. On login submission, verify credentials; on success, store username and session timestamp in `$_SESSION`.
3. In protected pages (`dashboard.php`), check `isset($_SESSION['student_user'])`. If not set, redirect to `index.php`.
4. Create `logout.php` to unset session variables using `session_unset()` and destroy session storage with `session_destroy()`.

## Source Code
See `index.php`, `dashboard.php`, and `logout.php` in this directory.

## Sample Input
Credentials: Username `student123`, Password `pass123`.

## Expected Output
Successful login redirects to protected dashboard; attempting to visit `dashboard.php` directly without login redirects back to `index.php`.

## Execution Instructions
1. Run via XAMPP or `php -S localhost:8000`.
2. Open `http://localhost:8000/LAB/08_php_sessions/index.php`.
