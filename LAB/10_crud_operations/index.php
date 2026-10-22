<?php
/**
 * Experiment 10: Complete CRUD Operations
 */
require_once __DIR__ . '/../../config/config.php';
$pdo = get_db_connection();

// Read
$stmt = $pdo->query("SELECT * FROM books ORDER BY id DESC LIMIT 10");
$books = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 10 - CRUD Operations</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #fff; padding: 2rem; }
        .box { max-width: 900px; margin: 0 auto; background: #1e293b; padding: 1.5rem; border-radius: 8px; border: 1px solid #334155; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #334155; padding: 0.75rem; text-align: left; }
        th { background: #0f172a; color: #38bdf8; }
        .btn { background: #2563eb; color: #fff; padding: 0.35rem 0.75rem; text-decoration: none; border-radius: 4px; font-size: 0.8rem; }
        .btn-danger { background: #ef4444; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Book Catalog CRUD Manager</h2>
        <a href="../../admin/add_book.php" class="btn" style="padding: 0.5rem 1rem;">&#43; Create New Book</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Title</th><th>Author</th><th>ISBN</th><th>Copies</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $b): ?>
                    <tr>
                        <td>#<?php echo $b['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($b['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($b['author']); ?></td>
                        <td><code><?php echo htmlspecialchars($b['isbn']); ?></code></td>
                        <td><?php echo $b['available_copies']; ?> / <?php echo $b['total_copies']; ?></td>
                        <td>
                            <a href="../../admin/edit_book.php?id=<?php echo $b['id']; ?>" class="btn">Update</a>
                            <a href="../../admin/delete_book.php?id=<?php echo $b['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
