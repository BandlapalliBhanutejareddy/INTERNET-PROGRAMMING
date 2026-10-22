<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Library System Settings';

$pdo = get_db_connection();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $settings_to_update = [
        'library_name' => trim($_POST['library_name'] ?? 'SIMATS Central Digital Library'),
        'daily_fine_rate' => number_format((float)($_POST['daily_fine_rate'] ?? 5.00), 2, '.', ''),
        'max_borrow_days' => max(1, (int)($_POST['max_borrow_days'] ?? 14)),
        'max_books_per_student' => max(1, (int)($_POST['max_books_per_student'] ?? 3)),
        'currency_symbol' => trim($_POST['currency_symbol'] ?? '₹')
    ];

    try {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        foreach ($settings_to_update as $k => $v) {
            $stmt->execute([$k, $v]);
        }
        $success = 'System configuration settings updated successfully!';
    } catch (Exception $e) {
        set_flash('error', 'Failed to save settings: ' . $e->getMessage());
    }
}

$current_settings = [];
$sRows = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
foreach ($sRows as $row) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 700px; margin: 0 auto;">
    <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff; margin-bottom: 0.5rem;">Library Configuration Settings</h1>
    <p style="color: var(--text-muted); font-size: 0.92rem; margin-bottom: 2rem;">Configure overdue penalty formulas, borrowing thresholds, and portal branding</p>

    <?php if ($success): ?>
        <div class="alert alert-success">&#10004; <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="card" style="padding: 2.5rem; border-top: 4px solid var(--warning);">
        <form action="<?php echo BASE_URL; ?>/admin/settings.php" method="POST">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label" for="library_name">Official Library Portal Name</label>
                <input type="text" id="library_name" name="library_name" class="form-control" value="<?php echo htmlspecialchars($current_settings['library_name'] ?? 'SIMATS Central Digital Library'); ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="daily_fine_rate">Daily Overdue Fine Rate (₹ per day)</label>
                    <input type="number" step="0.50" min="0" id="daily_fine_rate" name="daily_fine_rate" class="form-control" value="<?php echo htmlspecialchars($current_settings['daily_fine_rate'] ?? '5.00'); ?>" required>
                    <small style="color: var(--text-muted); font-size: 0.78rem;">Charged for each day past due date</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="currency_symbol">Currency Display Symbol</label>
                    <input type="text" id="currency_symbol" name="currency_symbol" class="form-control" value="<?php echo htmlspecialchars($current_settings['currency_symbol'] ?? '₹'); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="max_borrow_days">Standard Loan Duration (Days)</label>
                    <input type="number" min="1" max="180" id="max_borrow_days" name="max_borrow_days" class="form-control" value="<?php echo htmlspecialchars($current_settings['max_borrow_days'] ?? '14'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="max_books_per_student">Max Concurrent Loans Per Student</label>
                    <input type="number" min="1" max="20" id="max_books_per_student" name="max_books_per_student" class="form-control" value="<?php echo htmlspecialchars($current_settings['max_books_per_student'] ?? '3'); ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem; padding: 0.85rem;">
                &#128190; Save Configuration Settings
            </button>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
