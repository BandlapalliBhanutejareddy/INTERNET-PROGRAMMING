<?php
require_once __DIR__ . '/../config/config.php';
require_student();
$page_title = 'Student Dashboard';

$user = current_user();
$userId = $user['id'];

$active_loans = 0;
$overdue_loans = 0;
$pending_fines = 0.00;
$recent_issues = [];

try {
    $pdo = get_db_connection();

    // Active loans count
    $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM book_issues WHERE user_id = ? AND status IN ('issued', 'overdue')");
    $stmt1->execute([$userId]);
    $active_loans = (int)$stmt1->fetchColumn();

    // Overdue count
    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM book_issues WHERE user_id = ? AND status = 'overdue'");
    $stmt2->execute([$userId]);
    $overdue_loans = (int)$stmt2->fetchColumn();

    // Pending fines
    $stmt3 = $pdo->prepare("SELECT COALESCE(SUM(fine_amount), 0) FROM fines WHERE user_id = ? AND status = 'unpaid'");
    $stmt3->execute([$userId]);
    $pending_fines = (float)$stmt3->fetchColumn();

    // Recent 5 issues
    $stmt4 = $pdo->prepare("
        SELECT bi.*, b.title, b.author, b.isbn
        FROM book_issues bi
        JOIN books b ON bi.book_id = b.id
        WHERE bi.user_id = ?
        ORDER BY bi.id DESC
        LIMIT 5
    ");
    $stmt4->execute([$userId]);
    $recent_issues = $stmt4->fetchAll();
} catch (Exception $e) {
    set_flash('error', 'Error loading dashboard: ' . $e->getMessage());
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff;">Student Portal Dashboard</h1>
            <p style="color: var(--text-muted); font-size: 0.92rem;">
                Welcome, <strong><?php echo htmlspecialchars($user['name']); ?></strong> (Reg. No: <code><?php echo htmlspecialchars($user['student_id']); ?></code>)
            </p>
        </div>
        <a href="<?php echo BASE_URL; ?>/books/catalog.php" class="btn btn-primary btn-sm">
            &#128269; Browse Book Catalog
        </a>
    </div>
</div>

<!-- Metrics Cards -->
<div class="grid grid-cols-3" style="margin-bottom: 2.5rem;">
    <div class="stat-card">
        <div class="stat-icon">&#128214;</div>
        <div class="stat-info">
            <h3><?php echo $active_loans; ?></h3>
            <p>Active Borrowed Books</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #ef4444; background: rgba(239, 68, 68, 0.15);">&#9888;</div>
        <div class="stat-info">
            <h3 style="color: #f87171;"><?php echo $overdue_loans; ?></h3>
            <p>Overdue Books</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #f59e0b; background: rgba(245, 158, 11, 0.15);">&#128176;</div>
        <div class="stat-info">
            <h3 style="color: #fbbf24;">₹<?php echo number_format($pending_fines, 2); ?></h3>
            <p>Pending Overdue Fines</p>
        </div>
    </div>
</div>

<!-- Recent Borrowings Section -->
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #ffffff;">My Recent Book Issues</h2>
        <a href="<?php echo BASE_URL; ?>/student/my_loans.php" class="btn btn-outline btn-sm">View Full History &rarr;</a>
    </div>

    <?php if (!empty($recent_issues)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_issues as $issue): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($issue['title']); ?></strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">ISBN: <?php echo htmlspecialchars($issue['isbn']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($issue['author']); ?></td>
                            <td><?php echo htmlspecialchars($issue['issue_date']); ?></td>
                            <td>
                                <span style="font-weight: 600; <?php echo ($issue['status'] === 'overdue') ? 'color: #ef4444;' : ''; ?>">
                                    <?php echo htmlspecialchars($issue['due_date']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($issue['status'] === 'issued'): ?>
                                    <span class="badge badge-info">&#128214; Active Loan</span>
                                <?php elseif ($issue['status'] === 'overdue'): ?>
                                    <span class="badge badge-danger">&#9888; Overdue</span>
                                <?php else: ?>
                                    <span class="badge badge-success">&#10004; Returned</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No recent borrowings recorded. Visit the library to issue books.</p>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
