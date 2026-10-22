<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Process Book Return';

$issue_id = (int)($_GET['issue_id'] ?? 0);
$pdo = get_db_connection();
$errors = [];
$active_loans = [];

try {
    $stmt = $pdo->query("
        SELECT bi.*, b.title, b.isbn, u.name AS student_name, u.student_id,
               DATEDIFF(CURDATE(), bi.due_date) AS days_late
        FROM book_issues bi
        JOIN books b ON bi.book_id = b.id
        JOIN users u ON bi.user_id = u.id
        WHERE bi.status IN ('issued', 'overdue')
        ORDER BY bi.due_date ASC
    ");
    $active_loans = $stmt->fetchAll();
} catch (Exception $e) {
    $errors[] = 'Database error: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $sel_issue_id = (int)($_POST['issue_id'] ?? 0);
    $return_date = trim($_POST['return_date'] ?? date('Y-m-d'));

    if ($sel_issue_id <= 0) {
        $errors[] = 'Please select a valid borrowing record to return.';
    } else {
        try {
            $pdo->beginTransaction();

            // Fetch issue with lock
            $iStmt = $pdo->prepare("SELECT * FROM book_issues WHERE id = ? FOR UPDATE");
            $iStmt->execute([$sel_issue_id]);
            $issue = $iStmt->fetch();

            if (!$issue || $issue['status'] === 'returned') {
                $pdo->rollBack();
                $errors[] = 'This book has already been marked as returned.';
            } else {
                // Update issue record
                $upIssue = $pdo->prepare("UPDATE book_issues SET return_date = ?, status = 'returned' WHERE id = ?");
                $upIssue->execute([$return_date, $sel_issue_id]);

                // Increment available copies
                $upBook = $pdo->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE id = ?");
                $upBook->execute([$issue['book_id']]);

                // Calculate fine if overdue
                $dueDate = new DateTime($issue['due_date']);
                $retDate = new DateTime($return_date);
                $diff = $dueDate->diff($retDate);

                if ($retDate > $dueDate) {
                    $overdue_days = (int)$diff->format('%a');
                    $daily_rate = (float)get_setting('daily_fine_rate', 5.00);
                    $fine_amount = $overdue_days * $daily_rate;

                    $fStmt = $pdo->prepare("
                        INSERT INTO fines (issue_id, user_id, overdue_days, daily_rate, fine_amount, status)
                        VALUES (?, ?, ?, ?, ?, 'unpaid')
                        ON DUPLICATE KEY UPDATE
                            overdue_days = VALUES(overdue_days),
                            daily_rate = VALUES(daily_rate),
                            fine_amount = VALUES(fine_amount)
                    ");
                    $fStmt->execute([$sel_issue_id, $issue['user_id'], $overdue_days, $daily_rate, $fine_amount]);
                }

                $pdo->commit();
                set_flash('success', 'Book returned successfully! Inventory stock has been restored.');
                header('Location: ' . BASE_URL . '/admin/transactions.php');
                exit;
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = 'Return process failed: ' . $e->getMessage();
        }
    }
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 700px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" style="color: var(--accent); text-decoration: none; font-size: 0.9rem; font-weight: 600;">
            &larr; Back to Dashboard
        </a>
    </div>

    <div class="card" style="padding: 2.5rem; border-top: 4px solid var(--success);">
        <h1 style="font-size: 1.75rem; font-weight: 800; color: #ffffff; margin-bottom: 0.5rem;">Process Book Return</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem;">Mark returned items, restore inventory copy, and compute any overdue fine</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <ul style="margin-left: 1rem;">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/admin/return_book.php" method="POST" data-validate="true">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label" for="issue_id">Select Active Loan to Return *</label>
                <select id="issue_id" name="issue_id" class="form-control" required>
                    <option value="">-- Choose Borrowing Record --</option>
                    <?php foreach ($active_loans as $al): ?>
                        <option value="<?php echo $al['id']; ?>" <?php if ($issue_id === (int)$al['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($al['title']); ?> &bull; Student: <?php echo htmlspecialchars($al['student_name']); ?> (<?php echo htmlspecialchars($al['student_id']); ?>) &bull; Due: <?php echo htmlspecialchars($al['due_date']); ?>
                            <?php if ($al['days_late'] > 0): ?> [OVERDUE: <?php echo $al['days_late']; ?> days]<?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="return_date">Actual Return Date *</label>
                <input type="date" id="return_date" name="return_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 1.5rem; padding: 0.85rem;">
                &#10004; Record Return & Update Stock
            </button>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
