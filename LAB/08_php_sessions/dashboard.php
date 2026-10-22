<?php
session_start();
if (!isset($_SESSION["student_user"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 08 - Protected Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #fff; padding: 2rem; }
        .box { max-width: 500px; margin: 0 auto; background: #1e293b; padding: 2rem; border-radius: 8px; border: 1px solid #334155; text-align: center; }
        h2 { color: #10b981; }
        a { color: #ef4444; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2>&#10004; Authenticated Session Active</h2>
        <p>Logged in as: <strong><?php echo htmlspecialchars($_SESSION["student_user"]); ?></strong></p>
        <p>Session started at: <?php echo htmlspecialchars($_SESSION["login_time"]); ?></p>
        <p><a href="logout.php">&larr; Destroy Session & Logout</a></p>
    </div>
</body>
</html>
