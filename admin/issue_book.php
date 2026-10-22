<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Issue Book to Student';

$prefill_book_id = (int)($_GET['book_id'] ?? 0);
$errors = [];
$students = [];
$books = [];

$pdo = get_db_connection();
try {
    $sStmt = $pdo->query("SELECT id, student_id, name, department FROM users WHERE role = 'student' AND status = 'active' ORDER BY name ASC");
    $students = $sStmt->fetchAll();

    $bStmt = $pdo->query("SELECT id, title, author, isbn, available_copies FROM books WHERE available_copies > 0 ORDER BY title ASC");
    $books = $bStmt->fetchAll();
} catch (Exception $e) {
    $errors[] = 'Database error: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $user_id = (int)($_POST['user_id'] ?? 0);
    $book_id = (int)($_POST['book_id'] ?? 0);
    $issue_date = trim($_POST['issue_date'] ?? date('Y-m-d'));
    $borrow_days = (int)get_setting('max_borrow_days', 14);
    $due_date = date('Y-m-d', strtotime($issue_date . " + {$borrow_days} days"));
    $admin_id = current_user()['id'];

    if ($user_id <= 0) $errors[] = 'Please select a registered student.';
    if ($book_id <= 0) $errors[] = 'Please select an available book.';

    // Check student loan limit
    $max_allowed = (int)get_setting('max_books_per_student', 3);
    $chkStudent = $pdo->prepare("SELECT COUNT(*) FROM book_issues WHERE user_id = ? AND status IN ('issued', 'overdue')");
    $chkStudent->execute([$user_id]);
    $currLoans = (int)$chkStudent->fetchColumn();

    if ($currLoans >= $max_allowed) {
        $errors[] = "Student already has {$currLoans} active loan(s). Limit is {$max_allowed} books per student.";
    }

    // Check book availability with row lock
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $chkBook = $pdo->prepare("SELECT available_copies FROM books WHERE id = ? FOR UPDATE");
            $chkBook->execute([$book_id]);
            $avail = (int)$chkBook->fetchColumn();

            if ($avail <= 0) {
                $pdo->rollBack();
                $errors[] = 'No available copies left for this book.';
            } else {
                // Insert issue record
                $ins = $pdo->prepare("
                    INSERT INTO book_issues (user_id, book_id, issue_date, due_date, status, issued_by)
                    VALUES (?, ?, ?, ?, 'issued', ?)
                ");
                $ins->execute([$user_id, $book_id, $issue_date, $due_date, $admin_id]);

                // Decrement book available copies
                $upBook = $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = ?");
                $upBook->execute([$book_id]);

                $pdo->commit();
                set_flash('success', 'Book issued successfully! Due date set to: ' . $due_date);
                header('Location: ' . BASE_URL . '/admin/transactions.php');
                exit;
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = 'Issue transaction failed: ' . $e->getMessage();
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

    <div class="card" style="padding: 2.5rem; border-top: 4px solid var(--primary);">
        <h1 style="font-size: 1.75rem; font-weight: 800; color: #ffffff; margin-bottom: 0.5rem;">Issue Library Book</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem;">Assign an available volume to an active enrolled student</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <ul style="margin-left: 1rem;">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/admin/issue_book.php" method="POST" data-validate="true">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label" for="user_id">Select Registered Student *</label>
                <select id="user_id" name="user_id" class="form-control" required>
                    <option value="">-- Choose Student --</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?php echo $s['id']; ?>">
                            <?php echo htmlspecialchars($s['name']); ?> (Reg: <?php echo htmlspecialchars($s['student_id']); ?>) &bull; <?php echo htmlspecialchars($s['department']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="book_id">Select Available Book *</label>
                <select id="book_id" name="book_id" class="form-control" required>
                    <option value="">-- Choose Available Book --</option>
                    <?php foreach ($books as $b): ?>
                        <option value="<?php echo $b['id']; ?>" <?php if ($prefill_book_id === (int)$b['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($b['title']); ?> (Available: <?php echo $b['available_copies']; ?>) &bull; ISBN: <?php echo htmlspecialchars($b['isbn']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="issue_date">Issue Date *</label>
                    <input type="date" id="issue_date" name="issue_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Loan Duration Policy</label>
                    <input type="text" class="form-control" value="<?php echo (int)get_setting('max_borrow_days', 14); ?> Days Standard Loan" disabled style="background: rgba(255,255,255,0.05); color: var(--text-muted);">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem; padding: 0.85rem;">
                &#128228; Confirm and Issue Book
            </button>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
