<?php
require_once __DIR__ . '/../config/config.php';
$page_title = 'Library Book Catalog';

$query = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

$categories = [];
$books = [];

try {
    $pdo = get_db_connection();
    $catStmt = $pdo->query("SELECT DISTINCT category FROM books ORDER BY category ASC");
    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

    $sql = "SELECT * FROM books WHERE 1=1";
    $params = [];

    if (!empty($query)) {
        $sql .= " AND (title LIKE :q OR author LIKE :q OR isbn LIKE :q OR description LIKE :q)";
        $params['q'] = '%' . $query . '%';
    }

    if (!empty($category)) {
        $sql .= " AND category = :cat";
        $params['cat'] = $category;
    }

    $sql .= " ORDER BY title ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();
} catch (Exception $e) {
    $error = $e->getMessage();
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800; color: #ffffff;">Library Book Catalog</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Browse and search all physical and digital volumes in SIMATS Library</p>
    </div>
    <?php if (is_admin()): ?>
        <a href="<?php echo BASE_URL; ?>/admin/add_book.php" class="btn btn-primary">
            &#43; Add New Book to Catalog
        </a>
    <?php endif; ?>
</div>

<!-- Search & Filter Controls -->
<div class="card" style="margin-bottom: 2.5rem; padding: 1.5rem;">
    <form action="<?php echo BASE_URL; ?>/books/catalog.php" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 260px;">
            <input type="text" name="q" class="form-control" placeholder="Search by title, author, keyword, or ISBN..." value="<?php echo htmlspecialchars($query); ?>" id="catalogSearchInput">
        </div>
        <div style="flex: 1; min-width: 200px;">
            <select name="category" class="form-control" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php if ($category === $cat) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">&#128269; Filter</button>
            <?php if (!empty($query) || !empty($category)): ?>
                <a href="<?php echo BASE_URL; ?>/books/catalog.php" class="btn btn-outline">Clear</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Catalog Results -->
<?php if (!empty($books)): ?>
    <div style="margin-bottom: 1rem; color: var(--text-muted); font-size: 0.88rem;">
        Showing <strong><?php echo count($books); ?></strong> book(s) in catalog
    </div>
    <div class="grid grid-cols-3">
        <?php foreach ($books as $b): ?>
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                        <span class="badge badge-info"><?php echo htmlspecialchars($b['category']); ?></span>
                        <?php if ($b['available_copies'] > 0): ?>
                            <span class="badge badge-success">&#10004; <?php echo $b['available_copies']; ?> of <?php echo $b['total_copies']; ?> Available</span>
                        <?php else: ?>
                            <span class="badge badge-danger">&#10006; 0 of <?php echo $b['total_copies']; ?> Available</span>
                        <?php endif; ?>
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin-bottom: 0.35rem;">
                        <?php echo htmlspecialchars($b['title']); ?>
                    </h3>
                    <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                        By <strong><?php echo htmlspecialchars($b['author']); ?></strong>
                    </p>
                    <p style="font-size: 0.84rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.25rem;">
                        <?php echo htmlspecialchars(mb_strimwidth($b['description'] ?? 'No synopsis recorded.', 0, 130, '...')); ?>
                    </p>
                </div>
                <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">
                        ISBN: <?php echo htmlspecialchars($b['isbn']); ?>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/books/view.php?id=<?php echo $b['id']; ?>" class="btn btn-outline btn-sm">
                        View Details &rarr;
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card" style="text-align: center; padding: 4rem 2rem;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">&#128218;</div>
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #ffffff;">No Books Match Your Search</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem;">Try adjusting your keyword or category filter to discover available library resources.</p>
        <a href="<?php echo BASE_URL; ?>/books/catalog.php" class="btn btn-outline">Reset Search Filters</a>
    </div>
<?php endif; ?>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
