<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Fine Management & Settlements';

$filter = trim($_GET['status'] ?? '');
$fines = [];
$pdo = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $fine_id = (int)($_POST['fine_id'] ?? 0);
    $method = trim($_POST['payment_method'] ?? 'Cash');
    $ref = trim($_POST['transaction_ref'] ?? 'CASH-' . time());
    $admin_id = current_user()['id'];

    if ($fine_id > 0) {
        try {
            $pdo->beginTransaction();

            $fStmt = $pdo->prepare("SELECT * FROM fines WHERE id = ? FOR UPDATE");
            $fStmt->execute([$fine_id]);
            $fine = $fStmt->fetch();

            if ($fine && $fine['status'] === 'unpaid') {
                // Mark fine as paid
                $up = $pdo->prepare("UPDATE fines SET status = 'paid', paid_at = NOW() WHERE id = ?");
                $up->execute([$fine_id]);

                // Record payment transaction
                $insPay = $pdo->prepare("
                    INSERT INTO fine_payments (fine_id, user_id, amount_paid, payment_method, transaction_reference, received_by)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $insPay->execute([$fine_id, $fine['user_id'], $fine['fine_amount'], $method, $ref, $admin_id]);

                $pdo->commit();
                set_flash('success', 'Fine #FN-' . str_pad($fine_id, 4, '0', STR_PAD_LEFT) . ' marked as settled!');
            } else {
                $pdo->rollBack();
                set_flash('warning', 'Fine was already marked as paid or not found.');
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            set_flash('error', 'Fine settlement failed: ' . $e->getMessage());
        }
    }
    header('Location: ' . BASE_URL . '/admin/fines.php');
    exit;
}

try {
    $sql = "
        SELECT f.*, u.name AS student_name, u.student_id, b.title AS book_title, b.isbn,
               bi.due_date, bi.return_date
        FROM fines f
        JOIN users u ON f.user_id = u.id
        JOIN book_issues bi ON f.issue_id = bi.id
        JOIN books b ON bi.book_id = b.id
        WHERE 1=1
    ";
    $params = [];
    if (!empty($filter)) {
        $sql .= " AND f.status = :status";
        $params['status'] = $filter;
    }
    $sql .= " ORDER BY f.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $fines = $stmt->fetchAll();
} catch (Exception $e) {
    set_flash('error', 'Error loading fines: ' . $e->getMessage());
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff;">Overdue Fines & Settlements</h1>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Audit overdue charges and record in-person cash or digital payments</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="<?php echo BASE_URL; ?>/admin/fines.php" class="btn btn-sm <?php echo empty($filter) ? 'btn-primary' : 'btn-outline'; ?>">All Fines</a>
        <a href="<?php echo BASE_URL; ?>/admin/fines.php?status=unpaid" class="btn btn-sm <?php echo ($filter === 'unpaid') ? 'btn-danger' : 'btn-outline'; ?>">Pending</a>
        <a href="<?php echo BASE_URL; ?>/admin/fines.php?status=paid" class="btn btn-sm <?php echo ($filter === 'paid') ? 'btn-success' : 'btn-outline'; ?>">Settled</a>
    </div>
</div>

<div class="card" style="padding: 1.5rem;">
    <?php if (!empty($fines)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fine ID</th>
                        <th>Student</th>
                        <th>Book Title</th>
                        <th>Due Date</th>
                        <th>Late Days</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fines as $fn): ?>
                        <tr>
                            <td>#FN-<?php echo str_pad($fn['id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <strong style="color: #ffffff;"><?php echo htmlspecialchars($fn['student_name']); ?></strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><code><?php echo htmlspecialchars($fn['student_id']); ?></code></div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($fn['book_title']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($fn['due_date']); ?></td>
                            <td><span style="color: #ef4444; font-weight: 700;"><?php echo $fn['overdue_days']; ?>d</span></td>
                            <td>₹<?php echo number_format($fn['daily_rate'], 2); ?></td>
                            <td><strong style="color: #ffffff; font-size: 1rem;">₹<?php echo number_format($fn['fine_amount'], 2); ?></strong></td>
                            <td>
                                <?php if ($fn['status'] === 'paid'): ?>
                                    <span class="badge badge-success">&#10004; Paid</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">&#9888; Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($fn['status'] === 'unpaid'): ?>
                                    <form action="<?php echo BASE_URL; ?>/admin/fines.php" method="POST" style="display: inline;" onsubmit="return confirm('Collect ₹<?php echo number_format($fn['fine_amount'], 2); ?> from <?php echo htmlspecialchars($fn['student_name']); ?> and mark fine as paid?');">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="fine_id" value="<?php echo $fn['id']; ?>">
                                        <input type="hidden" name="payment_method" value="Cash">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            &#10004; Collect Fine
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">&#10004; Settled</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-muted); padding: 3rem;">No fine records found for this status.</p>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
