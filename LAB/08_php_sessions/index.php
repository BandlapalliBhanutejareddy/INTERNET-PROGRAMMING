<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    // Simulated Authentication
    if ($username === "student123" && $password === "pass123") {
        $_SESSION["student_user"] = $username;
        $_SESSION["login_time"] = date("d M Y, h:i A");
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials. Use 'student123' and 'pass123'.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 08 - PHP Sessions Login</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #fff; padding: 2rem; display: flex; justify-content: center; }
        .card { background: #1e293b; padding: 2rem; border-radius: 8px; border: 1px solid #334155; width: 360px; }
        h2 { color: #38bdf8; margin-top: 0; text-align: center; }
        input { width: 100%; padding: 0.65rem; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #fff; margin-bottom: 1rem; box-sizing: border-box; }
        button { width: 100%; background: #2563eb; color: #fff; padding: 0.75rem; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .err { color: #ef4444; font-size: 0.85rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Library Session Login</h2>
        <?php if ($error): ?><div class="err"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="student123" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="pass123" required>
            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>
