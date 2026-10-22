<?php
/**
 * Experiment 12: Book Search & Filtering
 */
require_once __DIR__ . '/../../config/config.php';
$pdo = get_db_connection();

$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

if (!empty($q)) {
    $sql .= " AND (title LIKE :q OR author LIKE :q OR isbn LIKE :q)";
    $params['q'] = '%' . $q . '%';
}
if (!empty($category)) {
    $sql .= " AND category = :cat";
    $params['cat'] = $category;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 12 - Book Search</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #fff; padding: 2rem; }
        .box { max-width: 800px; margin: 0 auto; background: #1e293b; padding: 1.5rem; border-radius: 8px; border: 1px solid #334155; }
        input, select { padding: 0.6rem; background: #0f172a; border: 1px solid #334155; border-radius: 4px; color: #fff; }
        button { background: #2563eb; color: #fff; padding: 0.6rem 1.25rem; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #334155; padding: 0.65rem; text-align: left; }
        th { background: #0f172a; color: #38bdf8; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Book Catalog Search & Filter</h2>
        <form method="GET" style="display: flex; gap: 0.5rem;">
            <input type="text" name="q" placeholder="Keyword, Title, or ISBN..." value="<?php echo htmlspecialchars($q); ?>" style="flex: 2;">
            <select name="category" style="flex: 1;">
                <option value="">All Categories</option>
                <option value="Internet Programming" <?php if ($category === 'Internet Programming') echo 'selected'; ?>>Internet Programming</option>
                <option value="Computer Science Core" <?php if ($category === 'Computer Science Core') echo 'selected'; ?>>Computer Science Core</option>
                <option value="Database Systems" <?php if ($category === 'Database Systems') echo 'selected'; ?>>Database Systems</option>
            </select>
            <button type="submit">&#128269; Search</button>
        </form>

        <table>
            <thead><tr><th>Title</th><th>Author</th><th>Category</th><th>Available</th></tr></thead>
            <tbody>
                <?php if (!empty($books)): ?>
                    <?php foreach ($books as $b): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($b['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($b['author']); ?></td>
                            <td><?php echo htmlspecialchars($b['category']); ?></td>
                            <td><?php echo $b['available_copies']; ?> / <?php echo $b['total_copies']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; color: #94a3b8;">No matching books found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
