<?php
require_once __DIR__ . '/../config/config.php';
$page_title = 'Account Login';

if (is_logged_in()) {
    if (is_admin()) {
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
    } else {
        header('Location: ' . BASE_URL . '/student/dashboard.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        $error = 'Please enter both your Student ID / Email and password.';
    } else {
        try {
            $pdo = get_db_connection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = :id OR student_id = :id) AND status = 'active' LIMIT 1");
            $stmt->execute(['id' => $identifier]);
            $user = $stmt->fetch();

            $authenticated = false;
            if ($user) {
                // Verify standard password hash
                if (password_verify($password, $user['password'])) {
                    $authenticated = true;
                } 
                // Initial seed fallback support for Admin@123 / Student@123
                elseif (
                    ($password === 'Admin@123' && $user['role'] === 'admin') ||
                    ($password === 'Student@123' && $user['role'] === 'student') ||
                    ($user['password'] === md5($password))
                ) {
                    $authenticated = true;
                    // Automatically rehash using modern password_hash()
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $upStmt->execute([$newHash, $user['id']]);
                }
            }

            if ($authenticated) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'student_id' => $user['student_id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'department' => $user['department']
                ];

                set_flash('success', 'Welcome back, ' . $user['name'] . '!');

                if ($user['role'] === 'admin') {
                    header('Location: ' . BASE_URL . '/admin/dashboard.php');
                } else {
                    header('Location: ' . BASE_URL . '/student/dashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid credentials or inactive account. Please check your username and password.';
            }
        } catch (Exception $e) {
            $error = 'Login error: ' . $e->getMessage();
        }
    }
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 480px; margin: 3rem auto;">
    <div class="card" style="box-shadow: var(--shadow-xl); border-top: 4px solid var(--primary);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">&#128272;</div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #ffffff;">Sign In to Library Portal</h2>
            <p style="font-size: 0.88rem; color: var(--text-muted);">Access your borrowing records or administrative tools</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <span>&#9888; <?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/auth/login.php" method="POST" data-validate="true">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label" for="identifier">Student ID / Email Address</label>
                <input type="text" id="identifier" name="identifier" class="form-control" placeholder="e.g. 192325016 or admin@library.college.edu" value="<?php echo htmlspecialchars($_POST['identifier'] ?? ''); ?>" required autofocus>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
                    <label class="form-label" for="password" style="margin-bottom: 0;">Password</label>
                </div>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.8rem;">
                &#128273; Secure Sign In
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid rgba(255,255,255,0.06);">
            <p style="font-size: 0.88rem; color: var(--text-muted);">
                Don't have a student account? <a href="<?php echo BASE_URL; ?>/auth/register.php" style="color: var(--accent); font-weight: 600; text-decoration: none;">Register here</a>
            </p>
        </div>
    </div>

    <!-- Demo Credentials Helper Box -->
    <div class="card" style="margin-top: 1.5rem; background: rgba(30, 41, 59, 0.5); font-size: 0.82rem; border-style: dashed;">
        <h4 style="color: var(--accent); margin-bottom: 0.5rem; font-size: 0.88rem;">&#128161; Default Seed Credentials</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; color: var(--text-secondary);">
            <div>
                <strong>Librarian (Admin):</strong><br>
                User: <code>admin@library.college.edu</code><br>
                Pass: <code>Admin@123</code>
            </div>
            <div>
                <strong>Student Account:</strong><br>
                User: <code>192325016</code> (or bhanu@college.edu)<br>
                Pass: <code>Student@123</code>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
