# Experiment 07: PHP Form Handling, Input Sanitization & Server Validation

## Title
Build a secure PHP form processor implementing `POST` data extraction, `htmlspecialchars()` sanitization, and server-side validation.

## Aim
To prevent Cross-Site Scripting (XSS) and malformed data entry by implementing robust server-side sanitization and validation using PHP `filter_var()` and regular expressions.

## Algorithm
1. Check if the HTTP request method is `POST` via `$_SERVER['REQUEST_METHOD']`.
2. Extract input values and sanitize using `trim()`, `htmlspecialchars()`, and `filter_var()`.
3. Validate required constraints: 9-digit student ID regex, non-empty book title, valid email address, integer copy range (1-3).
4. Store any failure descriptions in an `$errors` array.
5. If errors exist, render them inside a warning block.
6. If validation passes, construct the confirmed payload and output a success alert.

## Source Code
See `index.php` in this directory.

## Sample Input
- ID: `192325016`
- Title: `Internet & World Wide Web`
- Email: `bhanu@college.edu`
- Copies: `1`

## Expected Output
"Reservation Recorded!" box showing sanitized fields and formatted server timestamp.

## Execution Instructions
1. Run via XAMPP or `php -S localhost:8000`.
2. Open `http://localhost:8000/LAB/07_php_forms/index.php`.
