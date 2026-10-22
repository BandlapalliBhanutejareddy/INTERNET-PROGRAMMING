<?php
require_once __DIR__ . '/../config/config.php';
require_admin();

$book_id = (int)($_GET['id'] ?? 0);
if ($book_id <= 0) {
    set_flash('error', 'Invalid book specified.');
    header('Location: ' . BASE_URL . '/admin/manage_books.php');
    exit;
}

$pdo = get_db_connection();
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if (!$book) {
    set_flash('error', 'Book not found.');
    header('Location: ' . BASE_URL . '/admin/manage_books.php');
    exit;
}

$page_title = 'Edit Book: ' . $book['title'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $publisher = trim($_POST['publisher'] ?? '');
    $pub_year = (int)($_POST['publication_year'] ?? date('Y'));
    $total_copies = max(1, (int)($_POST['total_copies'] ?? 1));
    $description = trim($_POST['description'] ?? '');

    if (empty($title)) $errors[] = 'Title is required.';
    if (empty($author)) $errors[] = 'Author is required.';
    if (empty($isbn)) $errors[] = 'ISBN is required.';

    // Check available copies consistency
    $issued_count = $book['total_copies'] - $book['available_copies'];
    if ($total_copies < $issued_count) {
        $errors[] = 'Total copies cannot be less than currently issued copies (' . $issued_count . ').';
    }

    if (empty($errors)) {
        try {
            $new_available = $total_copies - $issued_count;
            $up = $pdo->prepare("
                UPDATE books SET
                    title = ?, author = ?, isbn = ?, category = ?,
                    publisher = ?, publication_year = ?, total_copies = ?,
                    available_copies = ?, description = ?
                WHERE id = ?
            ");
            $up->execute([$title, $author, $isbn, $category, $publisher, $pub_year, $total_copies, $new_available, $description, $book_id]);

            set_flash('success', 'Book metadata updated successfully!');
            header('Location: ' . BASE_URL . '/admin/manage_books.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Update error: ' . $e->getMessage();
        }
    }
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="<?php echo BASE_URL; ?>/admin/manage_books.php" style="color: var(--accent); text-decoration: none; font-size: 0.9rem; font-weight: 600;">
            &larr; Back to Books Catalog
        </a>
    </div>

    <div class="card" style="padding: 2.5rem; border-top: 4px solid var(--accent);">
        <h1 style="font-size: 1.75rem; font-weight: 800; color: #ffffff; margin-bottom: 0.5rem;">Edit Book Metadata</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem;">Update information for #BK-<?php echo str_pad($book['id'], 3, '0', STR_PAD_LEFT); ?></p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <ul style="margin-left: 1rem;">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/admin/edit_book.php?id=<?php echo $book_id; ?>" method="POST" data-validate="true">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label" for="title">Book Title *</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="author">Author(s) *</label>
                    <input type="text" id="author" name="author" class="form-control" value="<?php echo htmlspecialchars($book['author']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="isbn">ISBN Code *</label>
                    <input type="text" id="isbn" name="isbn" class="form-control" value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="category">Category *</label>
                    <input type="text" id="category" name="category" class="form-control" value="<?php echo htmlspecialchars($book['category']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="publisher">Publisher</label>
                    <input type="text" id="publisher" name="publisher" class="form-control" value="<?php echo htmlspecialchars($book['publisher'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="publication_year">Publication Year</label>
                    <input type="number" id="publication_year" name="publication_year" class="form-control" value="<?php echo htmlspecialchars($book['publication_year'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="total_copies">Total Copies</label>
                    <input type="number" id="total_copies" name="total_copies" class="form-control" value="<?php echo htmlspecialchars($book['total_copies']); ?>" min="1" required>
                    <small style="color: var(--text-muted); font-size: 0.78rem;">Currently issued: <?php echo ($book['total_copies'] - $book['available_copies']); ?></small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Book Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($book['description'] ?? ''); ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
                <a href="<?php echo BASE_URL; ?>/admin/manage_books.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
