# Experiment 14: ACID-Compliant Issue and Return System

## Title
Implement database transactions ensuring Atomicity, Consistency, Isolation, and Durability (ACID) during book checkout.

## Aim
To prevent race conditions and inventory corruption using PDO transactions (`beginTransaction`, `commit`, `rollBack`) and row locking (`FOR UPDATE`).

## Algorithm
1. Initiate transaction with `$pdo->beginTransaction()`.
2. Inspect target book availability using `SELECT available_copies FROM books WHERE id = ? FOR UPDATE`.
3. If `available_copies <= 0`, invoke `$pdo->rollBack()` and reject loan.
4. Otherwise, insert loan record into `book_issues` and decrement `available_copies`.
5. Execute `$pdo->commit()` to atomically finalize the database changes.

## Execution Instructions
Open `http://localhost:8000/LAB/14_issue_return_system/issue_return.php`.
