<?php
require_once __DIR__ . '/../config/config.php';
require_admin();

$book_id = (int)($_GET['id'] ?? 0);
if ($book_id <= 0) {
    set_flash('error', 'Invalid book identifier.');
    header('Location: ' . BASE_URL . '/admin/manage_books.php');
    exit;
}

try {
    $pdo = get_db_connection();

    // Safeguard check: ensure no copies are currently checked out
    $chk = $pdo->prepare("SELECT COUNT(*) FROM book_issues WHERE book_id = ? AND status IN ('issued', 'overdue')");
    $chk->execute([$book_id]);
    $activeCount = (int)$chk->fetchColumn();

    if ($activeCount > 0) {
        set_flash('error', 'Cannot delete this book: ' . $activeCount . ' copy/copies are currently issued to students. Have all copies returned before deleting.');
    } else {
        $del = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $del->execute([$book_id]);
        set_flash('success', 'Book removed from library catalog successfully.');
    }
} catch (Exception $e) {
    set_flash('error', 'Error deleting book: ' . $e->getMessage());
}

header('Location: ' . BASE_URL . '/admin/manage_books.php');
exit;
