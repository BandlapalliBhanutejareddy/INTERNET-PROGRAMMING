<?php
require_once __DIR__ . '/../config/config.php';

$book_id = (int)($_GET['id'] ?? 0);
if ($book_id <= 0) {
    set_flash('error', 'Invalid book identifier provided.');
    header('Location: ' . BASE_URL . '/books/catalog.php');
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();

    if (!$book) {
        set_flash('error', 'Book not found in library catalog.');
        header('Location: ' . BASE_URL . '/books/catalog.php');
        exit;
    }
} catch (Exception $e) {
    set_flash('error', 'Database error: ' . $e->getMessage());
    header('Location: ' . BASE_URL . '/books/catalog.php');
    exit;
}

$page_title = $book['title'];
include_once __DIR__ . '/../includes/header.php';
?>

<div style="margin-bottom: 1.5rem;">
    <a href="<?php echo BASE_URL; ?>/books/catalog.php" style="color: var(--accent); text-decoration: none; font-size: 0.9rem; font-weight: 600;">
        &larr; Back to Catalog
    </a>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto; padding: 2.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
        <div>
            <span class="badge badge-info" style="margin-bottom: 0.75rem;"><?php echo htmlspecialchars($book['category']); ?></span>
            <h1 style="font-size: 2rem; font-weight: 800; color: #ffffff; line-height: 1.2; margin-bottom: 0.5rem;">
                <?php echo htmlspecialchars($book['title']); ?>
            </h1>
            <p style="font-size: 1.1rem; color: var(--text-secondary);">
                By <strong style="color: #ffffff;"><?php echo htmlspecialchars($book['author']); ?></strong>
            </p>
        </div>
        <div>
            <?php if ($book['available_copies'] > 0): ?>
                <span class="badge badge-success" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                    &#10004; <?php echo $book['available_copies']; ?> of <?php echo $book['total_copies']; ?> Copies Available
                </span>
            <?php else: ?>
                <span class="badge badge-danger" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                    &#10006; All <?php echo $book['total_copies']; ?> Copies Currently Issued
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Metadata Details Table -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem; background: rgba(15, 23, 42, 0.6); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
        <div>
            <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">ISBN Number</span>
            <p style="font-size: 1rem; color: #ffffff; font-family: monospace; font-weight: 600;"><?php echo htmlspecialchars($book['isbn']); ?></p>
        </div>
        <div>
            <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Publisher</span>
            <p style="font-size: 1rem; color: #ffffff;"><?php echo htmlspecialchars($book['publisher'] ?? 'Not specified'); ?></p>
        </div>
        <div>
            <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Publication Year</span>
            <p style="font-size: 1rem; color: #ffffff;"><?php echo htmlspecialchars($book['publication_year'] ?? 'N/A'); ?></p>
        </div>
        <div>
            <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Physical Holdings</span>
            <p style="font-size: 1rem; color: #ffffff;"><?php echo $book['total_copies']; ?> Total Copies (<?php echo $book['available_copies']; ?> on shelf)</p>
        </div>
    </div>

    <!-- Description -->
    <div style="margin-bottom: 2.5rem;">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin-bottom: 0.75rem;">Book Synopsis & Academic Topics</h3>
        <p style="color: var(--text-secondary); line-height: 1.7; font-size: 0.95rem;">
            <?php echo nl2br(htmlspecialchars($book['description'] ?? 'No detailed synopsis recorded for this title.')); ?>
        </p>
    </div>

    <!-- Action Shortcuts -->
    <div style="display: flex; gap: 1rem; flex-wrap: wrap; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
        <?php if (is_admin()): ?>
            <a href="<?php echo BASE_URL; ?>/admin/issue_book.php?book_id=<?php echo $book['id']; ?>" class="btn btn-primary">
                &#128228; Issue This Book
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-outline">
                &#9998; Edit Book Metadata
            </a>
        <?php elseif (is_student()): ?>
            <?php if ($book['available_copies'] > 0): ?>
                <div style="padding: 0.75rem 1rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: var(--radius-md); color: #34d399; font-size: 0.9rem;">
                    &#128218; This book is currently available on the library shelf. Visit the circulation desk with your Student ID (<strong><?php echo htmlspecialchars($_SESSION['user']['student_id']); ?></strong>) to borrow.
                </div>
            <?php else: ?>
                <div style="padding: 0.75rem 1rem; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--radius-md); color: #f87171; font-size: 0.9rem;">
                    &#9888; All copies of this book are currently issued. Please check back when a copy is returned.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/auth/login.php" class="btn btn-primary">
                Sign In to View Loan Eligibility
            </a>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
