<?php
require_once __DIR__ . '/../config/config.php';
$page_title = 'Student Registration';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/student/dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $student_id = strtoupper(trim($_POST['student_id'] ?? ''));
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? 'Computer Science & Engineering');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($student_id)) $errors[] = 'Student Registration ID is required.';
    if (empty($name)) $errors[] = 'Full Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid college email address is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters long.';
    if ($password !== $confirm_password) $errors[] = 'Password confirmation does not match.';

    if (empty($errors)) {
        try {
            $pdo = get_db_connection();

            // Check if student_id or email already exists
            $check = $pdo->prepare("SELECT id FROM users WHERE student_id = ? OR email = ?");
            $check->execute([$student_id, $email]);
            if ($check->rowCount() > 0) {
                $errors[] = 'A user with this Student ID or Email address already exists.';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $ins = $pdo->prepare("
                    INSERT INTO users (student_id, name, email, password, role, phone, department, status)
                    VALUES (?, ?, ?, ?, 'student', ?, ?, 'active')
                ");
                $ins->execute([$student_id, $name, $email, $hashed, $phone, $department]);

                set_flash('success', 'Registration successful! You can now sign in with your Student ID.');
                header('Location: ' . BASE_URL . '/auth/login.php');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = 'Registration error: ' . $e->getMessage();
        }
    }
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 600px; margin: 2rem auto;">
    <div class="card" style="box-shadow: var(--shadow-xl); border-top: 4px solid var(--accent);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">&#127891;</div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #ffffff;">Create Student Account</h2>
            <p style="font-size: 0.88rem; color: var(--text-muted);">Enroll in SIMATS Central Digital Library Portal</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <ul style="margin-left: 1rem;">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/auth/register.php" method="POST" data-validate="true">
            <?php echo csrf_field(); ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="student_id">Student Registration ID *</label>
                    <input type="text" id="student_id" name="student_id" class="form-control" placeholder="e.g. 192325016" value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="name">Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Bhanuteja Reddy" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="email">College Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="e.g. bhanu@college.edu" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Contact Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="e.g. 9848022338" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="department">Academic Department</label>
                <select id="department" name="department" class="form-control">
                    <option value="Computer Science & Engineering">Computer Science & Engineering</option>
                    <option value="Artificial Intelligence & Machine Learning">Artificial Intelligence & Machine Learning</option>
                    <option value="Information Technology">Information Technology</option>
                    <option value="Electronics & Communication Engineering">Electronics & Communication Engineering</option>
                    <option value="Data Science & Analytics">Data Science & Analytics</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Create Password *</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.8rem;">
                &#10004; Complete Registration
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid rgba(255,255,255,0.06);">
            <p style="font-size: 0.88rem; color: var(--text-muted);">
                Already have an account? <a href="<?php echo BASE_URL; ?>/auth/login.php" style="color: var(--accent); font-weight: 600; text-decoration: none;">Sign in here</a>
            </p>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
