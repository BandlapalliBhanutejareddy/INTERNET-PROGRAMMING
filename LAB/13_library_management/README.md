# Experiment 13: Library Inventory & Holdings Management

## Title
Implement catalog inventory aggregation and real-time stock balance tracking using SQL GROUP BY queries.

## Aim
To calculate departmental book totals, shelf availability, and circulation utilization metrics using SQL aggregates.

## Algorithm
1. Connect to MySQL via PDO.
2. Execute aggregate query `SELECT category, COUNT(*), SUM(total_copies), SUM(available_copies) FROM books GROUP BY category`.
3. Compute circulating copies `($total - $available)` and percentage utilization rate.
4. Render inventory status table.

## Execution Instructions
Open `http://localhost:8000/LAB/13_library_management/books.php`.
