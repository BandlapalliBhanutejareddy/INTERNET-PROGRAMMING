<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Circulation & Borrowing Records';

$filter_status = trim($_GET['status'] ?? '');
$search = trim($_GET['q'] ?? '');
$records = [];

try {
    $pdo = get_db_connection();
    $sql = "
        SELECT bi.*, b.title, b.isbn, u.name AS student_name, u.student_id, u.email,
               DATEDIFF(CURDATE(), bi.due_date) AS days_late
        FROM book_issues bi
        JOIN books b ON bi.book_id = b.id
        JOIN users u ON bi.user_id = u.id
        WHERE 1=1
    ";
    $params = [];

    if (!empty($filter_status)) {
        $sql .= " AND bi.status = :status";
        $params['status'] = $filter_status;
    }

    if (!empty($search)) {
        $sql .= " AND (b.title LIKE :q OR u.name LIKE :q OR u.student_id LIKE :q OR b.isbn LIKE :q)";
        $params['q'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY bi.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll();
} catch (Exception $e) {
    set_flash('error', 'Error fetching records: ' . $e->getMessage());
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff;">Circulation Records</h1>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Audit and manage all book loans, active checkouts, and overdue items</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?php echo BASE_URL; ?>/admin/issue_book.php" class="btn btn-primary btn-sm">&#128228; New Issue</a>
        <a href="<?php echo BASE_URL; ?>/admin/return_book.php" class="btn btn-outline btn-sm">&#128229; Return Book</a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 2rem; padding: 1.25rem;">
    <form action="<?php echo BASE_URL; ?>/admin/transactions.php" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 240px;">
            <input type="text" name="q" class="form-control" placeholder="Search by student name, ID, book title, or ISBN..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div style="flex: 1; min-width: 160px;">
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">All Loan Statuses</option>
                <option value="issued" <?php if ($filter_status === 'issued') echo 'selected'; ?>>Active Loans (Issued)</option>
                <option value="overdue" <?php if ($filter_status === 'overdue') echo 'selected'; ?>>Overdue</option>
                <option value="returned" <?php if ($filter_status === 'returned') echo 'selected'; ?>>Returned</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">&#128269; Filter</button>
            <?php if (!empty($search) || !empty($filter_status)): ?>
                <a href="<?php echo BASE_URL; ?>/admin/transactions.php" class="btn btn-outline">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Transactions Table -->
<div class="card" style="padding: 1.5rem;">
    <?php if (!empty($records)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Loan ID</th>
                        <th>Student Name & ID</th>
                        <th>Book Title</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td>#LN-<?php echo str_pad($r['id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <strong style="color: #ffffff;"><?php echo htmlspecialchars($r['student_name']); ?></strong>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">Reg: <code><?php echo htmlspecialchars($r['student_id']); ?></code></div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($r['title']); ?></strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">ISBN: <?php echo htmlspecialchars($r['isbn']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($r['issue_date']); ?></td>
                            <td>
                                <span style="<?php echo ($r['status'] === 'overdue') ? 'color: #ef4444; font-weight: 700;' : ''; ?>">
                                    <?php echo htmlspecialchars($r['due_date']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo $r['return_date'] ? htmlspecialchars($r['return_date']) : '<span style="color: var(--text-muted); font-style: italic;">Active</span>'; ?>
                            </td>
                            <td>
                                <?php if ($r['status'] === 'issued'): ?>
                                    <span class="badge badge-info">&#128214; Active</span>
                                <?php elseif ($r['status'] === 'overdue'): ?>
                                    <span class="badge badge-danger">&#9888; Late (<?php echo max(1, (int)$r['days_late']); ?>d)</span>
                                <?php else: ?>
                                    <span class="badge badge-success">&#10004; Returned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($r['status'] !== 'returned'): ?>
                                    <a href="<?php echo BASE_URL; ?>/admin/return_book.php?issue_id=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline">
                                        Return
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">&#10004; Closed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-muted); padding: 3rem;">No borrowing records found for this filter criteria.</p>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
