<?php
/**
 * Experiment 11: User Authentication with BCrypt
 */
require_once __DIR__ . '/../../config/config.php';
$pdo = get_db_connection();

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($action === "register") {
        $name = trim($_POST["name"] ?? "Demo Student");
        $student_id = "REG" . rand(100000, 999999);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $ins = $pdo->prepare("INSERT INTO users (student_id, name, email, password, role) VALUES (?, ?, ?, ?, 'student')");
        try {
            $ins->execute([$student_id, $name, $email, $hash]);
            $message = "<span style='color: #10b981;'>User registered successfully! Password stored as BCrypt hash: $hash</span>";
        } catch (Exception $e) {
            $message = "<span style='color: #ef4444;'>Registration failed: " . $e->getMessage() . "</span>";
        }
    } elseif ($action === "login") {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            $message = "<span style='color: #10b981;'>&#10004; Authentication SUCCESS for: " . htmlspecialchars($user["name"]) . "</span>";
        } else {
            $message = "<span style='color: #ef4444;'>&#9888; Authentication FAILED: Invalid password hash.</span>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 11 - User Authentication</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #fff; padding: 2rem; }
        .card { max-width: 500px; margin: 0 auto; background: #1e293b; padding: 1.5rem; border-radius: 8px; border: 1px solid #334155; }
        input { width: 100%; padding: 0.6rem; background: #0f172a; border: 1px solid #334155; border-radius: 4px; color: #fff; margin-bottom: 0.75rem; box-sizing: border-box; }
        button { background: #2563eb; color: #fff; border: none; padding: 0.6rem 1.25rem; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Secure User Authentication (BCrypt)</h2>
        <?php if ($message): ?><p><?php echo $message; ?></p><?php endif; ?>

        <h3>Test Registration (password_hash)</h3>
        <form method="POST">
            <input type="hidden" name="action" value="register">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register with BCrypt</button>
        </form>

        <h3 style="margin-top: 2rem;">Test Login (password_verify)</h3>
        <form method="POST">
            <input type="hidden" name="action" value="login">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Verify Credentials</button>
        </form>
    </div>
</body>
</html>
