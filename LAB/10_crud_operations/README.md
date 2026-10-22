# Experiment 10: Complete Database CRUD Operations using PDO

## Title
Implement Create, Read, Update, and Delete (CRUD) operations on MySQL records using parameterized prepared statements.

## Aim
To demonstrate SQL injection prevention and data persistence across the complete lifecycle of catalog records.

## Algorithm
1. Connect to MySQL database via PDO.
2. **Create**: Use `INSERT INTO books (...) VALUES (?, ...)` with bound parameters.
3. **Read**: Retrieve rows using `SELECT * FROM books ORDER BY id DESC` and display in an HTML table.
4. **Update**: Retrieve record by ID, populate form, and submit `UPDATE books SET ... WHERE id = ?`.
5. **Delete**: Execute safe deletion with constraints: `DELETE FROM books WHERE id = ?`.

## Source Code
See `index.php` in this directory, and integrated admin modules `admin/add_book.php`, `admin/edit_book.php`, `admin/delete_book.php`.

## Expected Output
Complete book management table with create, view, modify, and delete actions.

## Execution Instructions
1. Open `http://localhost:8000/LAB/10_crud_operations/index.php`.
