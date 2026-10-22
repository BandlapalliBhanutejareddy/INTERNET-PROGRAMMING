<?php
/**
 * Experiment 13: Library Management Inventory Module
 */
require_once __DIR__ . '/../../config/config.php';
$pdo = get_db_connection();

// Aggregate holdings stats
$stmt = $pdo->query("
    SELECT category,
           COUNT(*) AS total_titles,
           SUM(total_copies) AS total_holdings,
           SUM(available_copies) AS available_holdings
    FROM books
    GROUP BY category
");
$summary = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 13 - Library Inventory Management</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #fff; padding: 2rem; }
        .box { max-width: 750px; margin: 0 auto; background: #1e293b; padding: 1.5rem; border-radius: 8px; border: 1px solid #334155; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #334155; padding: 0.75rem; text-align: left; }
        th { background: #0f172a; color: #38bdf8; }
        .badge { background: #10b981; color: #fff; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Departmental Inventory Holdings Report</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Unique Titles</th>
                    <th>Total Volumes</th>
                    <th>Available on Shelf</th>
                    <th>Utilization Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($summary as $row): ?>
                    <?php
                    $issued = $row['total_holdings'] - $row['available_holdings'];
                    $rate = ($row['total_holdings'] > 0) ? round(($issued / $row['total_holdings']) * 100, 1) : 0;
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['category']); ?></strong></td>
                        <td><?php echo $row['total_titles']; ?></td>
                        <td><?php echo $row['total_holdings']; ?></td>
                        <td><span class="badge"><?php echo $row['available_holdings']; ?> Available</span></td>
                        <td><?php echo $rate; ?>% borrowed</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
