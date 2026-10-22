<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Add New Book';

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

    if (empty($title)) $errors[] = 'Book title is required.';
    if (empty($author)) $errors[] = 'Author name is required.';
    if (empty($isbn)) $errors[] = 'ISBN number is required.';
    if (empty($category)) $errors[] = 'Category is required.';

    if (empty($errors)) {
        try {
            $pdo = get_db_connection();
            $check = $pdo->prepare("SELECT id FROM books WHERE isbn = ?");
            $check->execute([$isbn]);
            if ($check->rowCount() > 0) {
                $errors[] = 'A book with this ISBN already exists in catalog.';
            } else {
                $ins = $pdo->prepare("
                    INSERT INTO books (title, author, isbn, category, publisher, publication_year, total_copies, available_copies, description)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $ins->execute([$title, $author, $isbn, $category, $publisher, $pub_year, $total_copies, $total_copies, $description]);

                set_flash('success', 'Book "' . $title . '" successfully added to catalog!');
                header('Location: ' . BASE_URL . '/admin/manage_books.php');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = 'Error saving book: ' . $e->getMessage();
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

    <div class="card" style="padding: 2.5rem; border-top: 4px solid var(--primary);">
        <h1 style="font-size: 1.75rem; font-weight: 800; color: #ffffff; margin-bottom: 0.5rem;">Add New Catalog Book</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem;">Register a new physical or digital book volume into the central library inventory</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <ul style="margin-left: 1rem;">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/admin/add_book.php" method="POST" data-validate="true">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label" for="title">Book Title *</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Internet & World Wide Web How to Program" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="author">Author(s) *</label>
                    <input type="text" id="author" name="author" class="form-control" placeholder="e.g. Paul Deitel, Harvey Deitel" value="<?php echo htmlspecialchars($_POST['author'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="isbn">ISBN Code *</label>
                    <input type="text" id="isbn" name="isbn" class="form-control" placeholder="e.g. 978-0132151009" value="<?php echo htmlspecialchars($_POST['isbn'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="category">Category / Subject *</label>
                    <input type="text" id="category" name="category" class="form-control" placeholder="e.g. Internet Programming" value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="publisher">Publisher</label>
                    <input type="text" id="publisher" name="publisher" class="form-control" placeholder="e.g. Pearson Education" value="<?php echo htmlspecialchars($_POST['publisher'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="publication_year">Publication Year</label>
                    <input type="number" id="publication_year" name="publication_year" class="form-control" value="<?php echo htmlspecialchars($_POST['publication_year'] ?? date('Y')); ?>" min="1900" max="<?php echo date('Y') + 1; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="total_copies">Total Physical Copies *</label>
                    <input type="number" id="total_copies" name="total_copies" class="form-control" value="<?php echo htmlspecialchars($_POST['total_copies'] ?? '5'); ?>" min="1" max="500" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Book Description & Key Topics</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Brief outline of syllabus topics, contents, and index..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">&#43; Save Book to Catalog</button>
                <a href="<?php echo BASE_URL; ?>/admin/manage_books.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
