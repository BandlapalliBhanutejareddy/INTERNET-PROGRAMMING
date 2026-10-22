<?php
require_once "db_connect.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 09 - PDO MySQL Connection Test</title>
    <style>
        body { font-family: monospace; background: #0f172a; color: #fff; padding: 2rem; }
        .card { max-width: 600px; margin: 0 auto; background: #1e293b; padding: 2rem; border-radius: 8px; border: 1px solid #334155; }
        h2 { color: #38bdf8; margin-top: 0; }
        .ok { color: #10b981; }
        .fail { color: #ef4444; }
    </style>
</head>
<body>
    <div class="card">
        <h2>PDO Database Connection Diagnostics</h2>
        <p><strong>Status:</strong> <span class="<?php echo ($pdo) ? 'ok' : 'fail'; ?>"><?php echo htmlspecialchars($connection_status); ?></span></p>

        <?php if ($pdo): ?>
            <?php
            $stmt = $pdo->query("SELECT VERSION() AS ver, DATABASE() AS cur_db");
            $info = $stmt->fetch();
            ?>
            <p><strong>MySQL Version:</strong> <?php echo htmlspecialchars($info['ver']); ?></p>
            <p><strong>Active Database:</strong> <?php echo htmlspecialchars($info['cur_db']); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
