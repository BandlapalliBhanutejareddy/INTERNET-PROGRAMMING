<?php
/**
 * Experiment 15: Overdue Fine Calculation Engine
 */
require_once __DIR__ . '/../../config/config.php';

$daily_rate = 5.00; // Rs. 5.00 per day
$sample_due_date = "2026-10-12";
$sample_return_date = date("Y-m-d"); // Today: 2026-10-22

$dueDate = new DateTime($sample_due_date);
$returnDate = new DateTime($sample_return_date);

$is_overdue = $returnDate > $dueDate;
$overdue_days = 0;
$fine_amount = 0.00;

if ($is_overdue) {
    $diff = $dueDate->diff($returnDate);
    $overdue_days = (int)$diff->format("%a");
    $fine_amount = $overdue_days * $daily_rate;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 15 - Overdue Fine Calculation</title>
    <style>
        body { font-family: monospace; background: #0f172a; color: #fff; padding: 2rem; }
        .box { max-width: 600px; margin: 0 auto; background: #1e293b; padding: 1.5rem; border-radius: 8px; border: 1px solid #334155; }
        h2 { color: #38bdf8; }
        .danger { color: #ef4444; font-size: 1.25rem; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Automated Overdue Penalty Calculator</h2>
        <p><strong>Configured Daily Rate:</strong> ₹<?php echo number_format($daily_rate, 2); ?> per day</p>
        <p><strong>Scheduled Due Date:</strong> <?php echo $sample_due_date; ?></p>
        <p><strong>Actual Return Date:</strong> <?php echo $sample_return_date; ?></p>
        <p><strong>Status:</strong> <?php echo ($is_overdue) ? '<span style="color: #ef4444;">OVERDUE</span>' : '<span style="color: #10b981;">ON TIME</span>'; ?></p>
        <p><strong>Overdue Days:</strong> <?php echo $overdue_days; ?> days</p>
        <p><strong>Calculated Fine:</strong> <span class="danger">₹<?php echo number_format($fine_amount, 2); ?></span></p>
    </div>
</body>
</html>
