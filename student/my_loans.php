<?php
require_once __DIR__ . '/../config/config.php';
require_student();
$page_title = 'My Borrowing History';

$user = current_user();
$userId = $user['id'];
$loans = [];

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("
        SELECT bi.*, b.title, b.author, b.isbn, b.category,
               DATEDIFF(CURDATE(), bi.due_date) AS days_overdue
        FROM book_issues bi
        JOIN books b ON bi.book_id = b.id
        WHERE bi.user_id = ?
        ORDER BY bi.id DESC
    ");
    $stmt->execute([$userId]);
    $loans = $stmt->fetchAll();
} catch (Exception $e) {
    set_flash('error', 'Error fetching borrowing history: ' . $e->getMessage());
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff;">My Borrowing History</h1>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Complete record of your issued, returned, and overdue library books</p>
    </div>
    <input type="text" id="tableFilterInput" class="form-control" placeholder="Search my loans..." style="max-width: 280px;">
</div>

<div class="card" style="padding: 1.5rem;">
    <?php if (!empty($loans)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Book Title & Details</th>
                        <th>Category</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $idx => $loan): ?>
                        <tr>
                            <td><?php echo $idx + 1; ?></td>
                            <td>
                                <strong style="color: #ffffff;"><?php echo htmlspecialchars($loan['title']); ?></strong>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">
                                    By <?php echo htmlspecialchars($loan['author']); ?> &bull; <code><?php echo htmlspecialchars($loan['isbn']); ?></code>
                                </div>
                            </td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($loan['category']); ?></span></td>
                            <td><?php echo htmlspecialchars($loan['issue_date']); ?></td>
                            <td>
                                <span style="<?php echo ($loan['status'] === 'overdue') ? 'color: #ef4444; font-weight: 700;' : ''; ?>">
                                    <?php echo htmlspecialchars($loan['due_date']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo $loan['return_date'] ? htmlspecialchars($loan['return_date']) : '<span style="color: var(--text-muted); font-style: italic;">Not yet returned</span>'; ?>
                            </td>
                            <td>
                                <?php if ($loan['status'] === 'issued'): ?>
                                    <span class="badge badge-info">&#128214; Active Loan</span>
                                <?php elseif ($loan['status'] === 'overdue'): ?>
                                    <span class="badge badge-danger">&#9888; <?php echo max(1, (int)$loan['days_overdue']); ?> Days Overdue</span>
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
        <p style="text-align: center; color: var(--text-muted); padding: 3rem;">You have not borrowed any books from the library yet.</p>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
