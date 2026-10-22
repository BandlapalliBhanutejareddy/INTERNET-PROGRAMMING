# Experiment 06: PHP Basics, Variables, Functions & Arrays

## Title
Write a PHP script demonstrating core syntax: scalar types, multidimensional arrays, control structures, and user-defined functions.

## Aim
To understand server-side programming in PHP, variable declarations, associative arrays, foreach loops, and reusable function definitions.

## Algorithm
1. Define constants and scalar variables for institute name, course code, fine rates, and loan durations.
2. Initialize a multidimensional associative array storing book catalog records.
3. Define a custom function `calculate_fine($days_borrowed, $max_allowed, $rate_per_day)`.
4. Inside the function, compare days borrowed against the threshold; calculate `($days - $max) * $rate` if overdue, or return `0.00`.
5. Iterate through the array using a `foreach` loop and output an HTML table displaying computed fine values.

## Source Code
See `index.php` in this directory.

## Sample Input
Predefined array with loan records: 20 days kept, 10 days kept, and 17 days kept.

## Expected Output
An HTML table showing:
- Book 101 (20 days): ₹30.00 fine (6 days overdue * ₹5)
- Book 102 (10 days): ₹0.00 fine (within 14 days limit)
- Book 103 (17 days): ₹15.00 fine (3 days overdue * ₹5)

## Execution Instructions
1. Run using PHP built-in server or XAMPP:
   `php -S localhost:8000`
2. Open `http://localhost:8000/LAB/06_php_basics/index.php`.
