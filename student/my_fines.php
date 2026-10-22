<?php
require_once __DIR__ . '/../config/config.php';
require_student();
$page_title = 'My Overdue Fines';

$user = current_user();
$userId = $user['id'];
$fines = [];
$total_pending = 0.00;

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("
        SELECT f.*, bi.issue_date, bi.due_date, bi.return_date, b.title, b.isbn
        FROM fines f
        JOIN book_issues bi ON f.issue_id = bi.id
        JOIN books b ON bi.book_id = b.id
        WHERE f.user_id = ?
        ORDER BY f.id DESC
    ");
    $stmt->execute([$userId]);
    $fines = $stmt->fetchAll();

    foreach ($fines as $f) {
        if ($f['status'] === 'unpaid') {
            $total_pending += (float)$f['fine_amount'];
        }
    }
} catch (Exception $e) {
    set_flash('error', 'Error loading fine records: ' . $e->getMessage());
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff;">My Library Fines</h1>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Review late-return fines and pending balance records</p>
    </div>
    <div style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.6rem 1.25rem; border-radius: var(--radius-md);">
        <span style="font-size: 0.8rem; color: #fbbf24; text-transform: uppercase; font-weight: 700;">Total Pending Balance:</span>
        <strong style="font-size: 1.25rem; color: #ffffff; margin-left: 0.5rem;">₹<?php echo number_format($total_pending, 2); ?></strong>
    </div>
</div>

<div class="card" style="padding: 1.5rem; margin-bottom: 2rem;">
    <?php if (!empty($fines)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fine ID</th>
                        <th>Overdue Book</th>
                        <th>Due Date</th>
                        <th>Days Late</th>
                        <th>Daily Rate</th>
                        <th>Fine Amount</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fines as $fine): ?>
                        <tr>
                            <td>#FN-<?php echo str_pad($fine['id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($fine['title']); ?></strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">ISBN: <?php echo htmlspecialchars($fine['isbn']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($fine['due_date']); ?></td>
                            <td><span style="color: #ef4444; font-weight: 700;"><?php echo $fine['overdue_days']; ?> days</span></td>
                            <td>₹<?php echo number_format($fine['daily_rate'], 2); ?> / day</td>
                            <td><strong style="color: #ffffff;">₹<?php echo number_format($fine['fine_amount'], 2); ?></strong></td>
                            <td>
                                <?php if ($fine['status'] === 'paid'): ?>
                                    <span class="badge badge-success">&#10004; Settled</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">&#9888; Unpaid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem;">
            <div style="font-size: 2.5rem; color: #10b981; margin-bottom: 0.5rem;">&#10004;</div>
            <h3 style="font-size: 1.2rem; font-weight: 700; color: #ffffff;">No Overdue Fines!</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">You have returned all your borrowed books on time. Great job!</p>
        </div>
    <?php endif; ?>
</div>

<div class="card" style="background: rgba(15, 23, 42, 0.6); border-left: 4px solid var(--info); font-size: 0.88rem;">
    <h4 style="color: var(--info); margin-bottom: 0.5rem;">&#8505; Library Fine Policy</h4>
    <p style="color: var(--text-secondary); line-height: 1.6;">
        Overdue penalties are calculated automatically at <strong>₹5.00 per day</strong> for each book kept past its due date. Books returned on or before the due date incur zero fines. To clear any outstanding dues, please visit the library circulation desk with your Student ID card.
    </p>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
