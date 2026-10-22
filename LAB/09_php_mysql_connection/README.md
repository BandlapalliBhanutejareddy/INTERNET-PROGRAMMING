# Experiment 09: PHP MySQL Database Connection using PDO

## Title
Establish a robust, exception-handled database connection to MySQL using PHP Data Objects (PDO).

## Aim
To connect PHP to a MySQL/MariaDB database using PDO, configure `ATTR_ERRMODE => ERRMODE_EXCEPTION`, and handle connection failures gracefully with try-catch blocks.

## Algorithm
1. Define database connection parameters: host, database name, user, password, and character set (`utf8mb4`).
2. Construct the Data Source Name (DSN) string: `mysql:host=$host;dbname=$db;charset=$charset`.
3. Set PDO configuration options to enable exceptions and disable emulated prepared statements.
4. Instantiate `new PDO($dsn, $user, $pass, $options)` inside a `try` block.
5. Catch `PDOException` to capture error details without exposing credentials.
6. Run a diagnostic query (`SELECT VERSION()`) to confirm connectivity.

## Source Code
See `db_connect.php` and `test_connection.php` in this directory.

## Sample Input
Database parameters: `localhost`, `library_db`, `root`, empty password.

## Expected Output
"Connected successfully to database [library_db] on [localhost]" along with MySQL server version.

## Execution Instructions
1. Ensure MySQL is running in XAMPP.
2. Execute `php -S localhost:8000`.
3. Browse to `http://localhost:8000/LAB/09_php_mysql_connection/test_connection.php`.
