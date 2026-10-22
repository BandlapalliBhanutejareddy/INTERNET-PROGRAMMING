<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Manage Student Accounts';

$pdo = get_db_connection();
$search = trim($_GET['q'] ?? '');

$sql = "
    SELECT u.*,
           (SELECT COUNT(*) FROM book_issues bi WHERE bi.user_id = u.id AND bi.status IN ('issued', 'overdue')) AS active_loans,
           (SELECT COUNT(*) FROM book_issues bi WHERE bi.user_id = u.id AND bi.status = 'overdue') AS overdue_loans,
           (SELECT COALESCE(SUM(fine_amount), 0) FROM fines f WHERE f.user_id = u.id AND f.status = 'unpaid') AS pending_fines
    FROM users u
    WHERE u.role = 'student'
";
$params = [];

if (!empty($search)) {
    $sql .= " AND (u.name LIKE :q OR u.student_id LIKE :q OR u.email LIKE :q OR u.department LIKE :q)";
    $params['q'] = '%' . $search . '%';
}
$sql .= " ORDER BY u.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

include_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff;">Enrolled Students Registry</h1>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Audit student registration, active borrowing volume, and overdue balances</p>
    </div>
    <input type="text" id="tableFilterInput" class="form-control" placeholder="Quick filter students..." style="max-width: 260px;">
</div>

<div class="card" style="padding: 1.5rem;">
    <?php if (!empty($students)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Registration ID</th>
                        <th>Student Name</th>
                        <th>Email & Contact</th>
                        <th>Department</th>
                        <th>Active Loans</th>
                        <th>Pending Fines</th>
                        <th>Account Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $st): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($st['student_id']); ?></code></td>
                            <td><strong style="color: #ffffff;"><?php echo htmlspecialchars($st['name']); ?></strong></td>
                            <td>
                                <div><?php echo htmlspecialchars($st['email']); ?></div>
                                <small style="color: var(--text-muted);"><?php echo htmlspecialchars($st['phone'] ?? 'No phone'); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($st['department']); ?></td>
                            <td>
                                <?php if ($st['active_loans'] > 0): ?>
                                    <span class="badge badge-info"><?php echo $st['active_loans']; ?> Active (<?php echo $st['overdue_loans']; ?> Late)</span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">0 Loans</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($st['pending_fines'] > 0): ?>
                                    <strong style="color: #ef4444;">₹<?php echo number_format($st['pending_fines'], 2); ?></strong>
                                <?php else: ?>
                                    <span style="color: #10b981; font-weight: 600;">₹0.00</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($st['status'] === 'active'): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Suspended</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-muted); padding: 3rem;">No registered students found.</p>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
