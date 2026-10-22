<?php
/**
 * Experiment 14: ACID Issue & Return System
 */
require_once __DIR__ . '/../../config/config.php';
$pdo = get_db_connection();
$status_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    $user_id = 2; // Student: Bhanuteja
    $book_id = (int)($_POST["book_id"] ?? 1);

    if ($action === "issue") {
        try {
            $pdo->beginTransaction();
            // 1. Lock and check book
            $chk = $pdo->prepare("SELECT available_copies FROM books WHERE id = ? FOR UPDATE");
            $chk->execute([$book_id]);
            $copies = (int)$chk->fetchColumn();

            if ($copies <= 0) {
                $pdo->rollBack();
                $status_msg = "<span style='color: #ef4444;'>No copies available. Transaction rolled back!</span>";
            } else {
                // 2. Insert issue record
                $ins = $pdo->prepare("INSERT INTO book_issues (user_id, book_id, issue_date, due_date, status) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'issued')");
                $ins->execute([$user_id, $book_id]);

                // 3. Decrement copies
                $up = $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = ?");
                $up->execute([$book_id]);

                $pdo->commit();
                $status_msg = "<span style='color: #10b981;'>&#10004; Transaction COMMITTED: Book successfully issued!</span>";
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $status_msg = "<span style='color: #ef4444;'>Error: " . $e->getMessage() . "</span>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 14 - ACID Issue & Return</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #fff; padding: 2rem; }
        .card { max-width: 600px; margin: 0 auto; background: #1e293b; padding: 1.5rem; border-radius: 8px; border: 1px solid #334155; }
        button { background: #2563eb; color: #fff; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>ACID-Compliant Book Circulation Transaction</h2>
        <p>Demonstration of <code>beginTransaction()</code>, row-level locking with <code>FOR UPDATE</code>, and atomic rollback on inventory shortage.</p>
        <?php if ($status_msg): ?><p><?php echo $status_msg; ?></p><?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="issue">
            <input type="hidden" name="book_id" value="1">
            <button type="submit">&#128228; Execute Atomic Issue Transaction (Book #1)</button>
        </form>
    </div>
</body>
</html>
