# Experiment 12: Book Search and Multi-Criteria Filtering

## Title
Develop a multi-criteria search engine querying library books using SQL `LIKE` operators and dynamic WHERE clauses.

## Aim
To query relational databases dynamically based on variable user input while preventing SQL injection using prepared statements.

## Algorithm
1. Read user search terms (`q`) and category filter via `$_GET`.
2. Construct dynamic query string beginning with `SELECT * FROM books WHERE 1=1`.
3. If search term is present, append `AND (title LIKE :q OR author LIKE :q OR isbn LIKE :q)` and bind `%keyword%`.
4. If category is selected, append `AND category = :cat`.
5. Execute prepared statement with parameter array.
6. Render matching results in an HTML table.

## Execution Instructions
Open `http://localhost:8000/LAB/12_book_search/search.php`.
