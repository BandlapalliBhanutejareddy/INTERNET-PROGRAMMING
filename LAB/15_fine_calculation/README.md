# Experiment 15: Configurable Overdue Fine Calculation

## Title
Compute overdue penalties based on calendar date differences (`DateTime::diff`) and configurable daily rates.

## Aim
To calculate overdue days and monetary fines accurately without charging penalties on timely returns.

## Algorithm
1. Instantiate `new DateTime($due_date)` and `new DateTime($return_date)`.
2. Compare both dates using comparison operator `>`:
   - If `$return_date <= $due_date`: Fine = ₹0.00.
   - If `$return_date > $due_date`: Calculate interval using `$due_date->diff($return_date)`.
3. Extract total days difference via `$interval->format('%a')`.
4. Multiply overdue days by `$daily_fine_rate`.

## Execution Instructions
Open `http://localhost:8000/LAB/15_fine_calculation/calculate_fine.php`.
