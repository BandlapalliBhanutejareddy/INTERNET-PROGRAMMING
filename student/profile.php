<?php
require_once __DIR__ . '/../config/config.php';
require_login();
$page_title = 'My Profile';

$user = current_user();
$userId = $user['id'];
$success = '';
$error = '';

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();
} catch (Exception $e) {
    die('Database error: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'update_info') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($name)) {
            $error = 'Full name cannot be empty.';
        } else {
            $up = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
            $up->execute([$name, $phone, $userId]);
            $_SESSION['user']['name'] = $name;
            $success = 'Profile details updated successfully!';
            $profile['name'] = $name;
            $profile['phone'] = $phone;
        }
    } elseif ($action === 'change_password') {
        $current_pwd = $_POST['current_password'] ?? '';
        $new_pwd = $_POST['new_password'] ?? '';
        $confirm_pwd = $_POST['confirm_password'] ?? '';

        if (!password_verify($current_pwd, $profile['password'])) {
            $error = 'Current password is incorrect.';
        } elseif (strlen($new_pwd) < 6) {
            $error = 'New password must be at least 6 characters long.';
        } elseif ($new_pwd !== $confirm_pwd) {
            $error = 'New password and confirmation do not match.';
        } else {
            $newHash = password_hash($new_pwd, PASSWORD_DEFAULT);
            $up = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $up->execute([$newHash, $userId]);
            $success = 'Your password has been changed successfully!';
        }
    }
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 800px; margin: 0 auto;">
    <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff; margin-bottom: 0.5rem;">User Account Profile</h1>
    <p style="color: var(--text-muted); font-size: 0.92rem; margin-bottom: 2rem;">Manage your personal details and account credentials</p>

    <?php if ($success): ?>
        <div class="alert alert-success">&#10004; <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error">&#9888; <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-2" style="margin-bottom: 2rem;">
        <!-- Information Card -->
        <div class="card">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin-bottom: 1.25rem;">Personal Information</h3>
            <form action="<?php echo BASE_URL; ?>/student/profile.php" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="update_info">

                <div class="form-group">
                    <label class="form-label">Student ID / Username</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($profile['student_id']); ?>" disabled style="background: rgba(255,255,255,0.05); color: var(--text-muted);">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($profile['email']); ?>" disabled style="background: rgba(255,255,255,0.05); color: var(--text-muted);">
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($profile['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Contact Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Academic Department</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($profile['department']); ?>" disabled style="background: rgba(255,255,255,0.05); color: var(--text-muted);">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Profile Changes</button>
            </form>
        </div>

        <!-- Security Card -->
        <div class="card">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin-bottom: 1.25rem;">Change Password</h3>
            <form action="<?php echo BASE_URL; ?>/student/profile.php" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                </div>

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Min. 6 characters" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter new password" required>
                </div>

                <button type="submit" class="btn btn-outline" style="width: 100%; margin-top: 1.5rem;">Update Password</button>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
