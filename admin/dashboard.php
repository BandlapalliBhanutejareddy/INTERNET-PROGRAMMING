<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Librarian Dashboard';

$stats = [
    'total_titles' => 0,
    'total_copies' => 0,
    'available_copies' => 0,
    'issued_books' => 0,
    'overdue_books' => 0,
    'registered_students' => 0,
    'collected_fines' => 0.00,
    'pending_fines' => 0.00
];
$recent_issues = [];

try {
    $pdo = get_db_connection();

    // Books metrics
    $bStmt = $pdo->query("SELECT COUNT(*) AS total_titles, SUM(total_copies) AS total_copies, SUM(available_copies) AS available_copies FROM books");
    $bData = $bStmt->fetch();
    $stats['total_titles'] = (int)($bData['total_titles'] ?? 0);
    $stats['total_copies'] = (int)($bData['total_copies'] ?? 0);
    $stats['available_copies'] = (int)($bData['available_copies'] ?? 0);

    // Issues & Overdues
    $iStmt = $pdo->query("SELECT COUNT(*) FROM book_issues WHERE status IN ('issued', 'overdue')");
    $stats['issued_books'] = (int)$iStmt->fetchColumn();

    $oStmt = $pdo->query("SELECT COUNT(*) FROM book_issues WHERE status = 'overdue'");
    $stats['overdue_books'] = (int)$oStmt->fetchColumn();

    // Students
    $sStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
    $stats['registered_students'] = (int)$sStmt->fetchColumn();

    // Fines
    $fColl = $pdo->query("SELECT COALESCE(SUM(fine_amount), 0) FROM fines WHERE status = 'paid'");
    $stats['collected_fines'] = (float)$fColl->fetchColumn();

    $fPend = $pdo->query("SELECT COALESCE(SUM(fine_amount), 0) FROM fines WHERE status = 'unpaid'");
    $stats['pending_fines'] = (float)$fPend->fetchColumn();

    // Recent 6 transactions
    $txStmt = $pdo->query("
        SELECT bi.*, b.title, b.isbn, u.name AS student_name, u.student_id
        FROM book_issues bi
        JOIN books b ON bi.book_id = b.id
        JOIN users u ON bi.user_id = u.id
        ORDER BY bi.id DESC
        LIMIT 6
    ");
    $recent_issues = $txStmt->fetchAll();
} catch (Exception $e) {
    set_flash('error', 'Dashboard error: ' . $e->getMessage());
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff;">Librarian Administration Control Center</h1>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Real-time circulation metrics, catalog management, and student activity overview</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?php echo BASE_URL; ?>/admin/issue_book.php" class="btn btn-primary">&#128228; Issue Book</a>
        <a href="<?php echo BASE_URL; ?>/admin/return_book.php" class="btn btn-outline">&#128229; Return Book</a>
        <a href="<?php echo BASE_URL; ?>/admin/add_book.php" class="btn btn-outline-admin">&#43; Add Book</a>
    </div>
</div>

<!-- Primary Stats Grid -->
<div class="grid grid-cols-4" style="margin-bottom: 2.5rem;">
    <div class="stat-card">
        <div class="stat-icon">&#128218;</div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['total_titles']); ?></h3>
            <p>Catalog Titles (<?php echo $stats['total_copies']; ?> copies)</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #38bdf8; background: rgba(56, 189, 248, 0.15);">&#128228;</div>
        <div class="stat-info">
            <h3 style="color: #38bdf8;"><?php echo $stats['issued_books']; ?></h3>
            <p>Active Borrowings</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #ef4444; background: rgba(239, 68, 68, 0.15);">&#9888;</div>
        <div class="stat-info">
            <h3 style="color: #f87171;"><?php echo $stats['overdue_books']; ?></h3>
            <p>Overdue Books</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.15);">&#127891;</div>
        <div class="stat-info">
            <h3><?php echo $stats['registered_students']; ?></h3>
            <p>Enrolled Students</p>
        </div>
    </div>
</div>

<!-- Financial Summary Row -->
<div class="grid grid-cols-2" style="margin-bottom: 2.5rem;">
    <div class="card" style="display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--success);">
        <div>
            <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Total Fines Collected</span>
            <h2 style="font-size: 2rem; font-weight: 800; color: #34d399; margin-top: 0.25rem;">₹<?php echo number_format($stats['collected_fines'], 2); ?></h2>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin/fines.php" class="btn btn-outline btn-sm">Manage Fines &rarr;</a>
    </div>

    <div class="card" style="display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--warning);">
        <div>
            <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Outstanding Pending Fines</span>
            <h2 style="font-size: 2rem; font-weight: 800; color: #fbbf24; margin-top: 0.25rem;">₹<?php echo number_format($stats['pending_fines'], 2); ?></h2>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin/fines.php?status=unpaid" class="btn btn-outline btn-sm">View Unpaid Fines &rarr;</a>
    </div>
</div>

<!-- Recent Transactions Table -->
<div class="card" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #ffffff;">Recent Circulation Activity</h2>
        <a href="<?php echo BASE_URL; ?>/admin/transactions.php" class="btn btn-outline btn-sm">All Transactions &rarr;</a>
    </div>

    <?php if (!empty($recent_issues)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Book Title</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_issues as $tx): ?>
                        <tr>
                            <td>
                                <strong style="color: #ffffff;"><?php echo htmlspecialchars($tx['student_name']); ?></strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">ID: <code><?php echo htmlspecialchars($tx['student_id']); ?></code></div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($tx['title']); ?></strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">ISBN: <?php echo htmlspecialchars($tx['isbn']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($tx['issue_date']); ?></td>
                            <td>
                                <span style="<?php echo ($tx['status'] === 'overdue') ? 'color: #ef4444; font-weight: 700;' : ''; ?>">
                                    <?php echo htmlspecialchars($tx['due_date']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($tx['status'] === 'issued'): ?>
                                    <span class="badge badge-info">&#128214; Active Loan</span>
                                <?php elseif ($tx['status'] === 'overdue'): ?>
                                    <span class="badge badge-danger">&#9888; Overdue</span>
                                <?php else: ?>
                                    <span class="badge badge-success">&#10004; Returned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($tx['status'] !== 'returned'): ?>
                                    <a href="<?php echo BASE_URL; ?>/admin/return_book.php?issue_id=<?php echo $tx['id']; ?>" class="btn btn-sm btn-outline">
                                        Record Return
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">&#10004; Completed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No recent circulation activity recorded.</p>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
