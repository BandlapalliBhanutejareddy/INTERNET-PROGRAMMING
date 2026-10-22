# Experiment 11: User Authentication with BCrypt Password Hashing

## Title
Implement secure user authentication using PHP's `password_hash()` and `password_verify()` with prepared statements.

## Aim
To safeguard user credentials against dictionary attacks and rainbow tables by storing salted BCrypt hashes instead of plain-text passwords.

## Algorithm
1. Receive registration credentials over HTTP POST.
2. Hash password using `password_hash($password, PASSWORD_DEFAULT)` which applies the Blowfish/BCrypt algorithm with auto-generated salt and cost factor.
3. Insert user record into MySQL `users` table.
4. On login, retrieve record by unique identifier (`email` or `student_id`).
5. Verify provided password against hash using `password_verify($password, $user['password'])`.
6. Grant session on true; reject with error message on false.

## Source Code
See `auth_demo.php` in this directory, and integrated modules `auth/register.php` and `auth/login.php`.

## Expected Output
- Registration generates a `$2y$10$...` hash string.
- Valid login verifies hash and grants authentication.

## Execution Instructions
1. Open `http://localhost:8000/LAB/11_user_authentication/auth_demo.php`.
