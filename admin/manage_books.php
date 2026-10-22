<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Manage Books Catalog';

$search = trim($_GET['q'] ?? '');
$books = [];

try {
    $pdo = get_db_connection();
    $sql = "SELECT * FROM books WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (title LIKE :q OR author LIKE :q OR isbn LIKE :q OR category LIKE :q)";
        $params['q'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();
} catch (Exception $e) {
    set_flash('error', 'Error fetching book list: ' . $e->getMessage());
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff;">Catalog Management</h1>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Add, modify, track physical holdings, or delete catalog titles</p>
    </div>
    <a href="<?php echo BASE_URL; ?>/admin/add_book.php" class="btn btn-primary">
        &#43; Add New Book
    </a>
</div>

<!-- Search Bar -->
<div class="card" style="margin-bottom: 2rem; padding: 1.25rem;">
    <form action="<?php echo BASE_URL; ?>/admin/manage_books.php" method="GET" style="display: flex; gap: 1rem;">
        <input type="text" name="q" class="form-control" placeholder="Search catalog by title, author, category, or ISBN..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">&#128269; Search</button>
        <?php if (!empty($search)): ?>
            <a href="<?php echo BASE_URL; ?>/admin/manage_books.php" class="btn btn-outline">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Books Table -->
<div class="card" style="padding: 1.5rem;">
    <?php if (!empty($books)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title & Author</th>
                        <th>ISBN</th>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $b): ?>
                        <tr>
                            <td>#BK-<?php echo str_pad($b['id'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <strong style="color: #ffffff;"><?php echo htmlspecialchars($b['title']); ?></strong>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">By <?php echo htmlspecialchars($b['author']); ?></div>
                            </td>
                            <td><code><?php echo htmlspecialchars($b['isbn']); ?></code></td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($b['category']); ?></span></td>
                            <td><?php echo $b['total_copies']; ?></td>
                            <td>
                                <?php if ($b['available_copies'] > 0): ?>
                                    <span class="badge badge-success"><?php echo $b['available_copies']; ?> Free</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">0 Left</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <?php if ($b['available_copies'] > 0): ?>
                                        <a href="<?php echo BASE_URL; ?>/admin/issue_book.php?book_id=<?php echo $b['id']; ?>" class="btn btn-sm btn-primary" title="Issue Book">
                                            Issue
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo BASE_URL; ?>/admin/edit_book.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline" title="Edit Metadata">
                                        Edit
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/admin/delete_book.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this book? This action cannot be undone.');" title="Delete Book">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-muted); padding: 3rem;">No books match your criteria in the catalog.</p>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
