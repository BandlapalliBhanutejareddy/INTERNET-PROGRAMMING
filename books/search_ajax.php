<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');

if (empty($q)) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("
        SELECT id, title, author, isbn, category, available_copies, total_copies
        FROM books
        WHERE title LIKE :q OR author LIKE :q OR isbn LIKE :q OR category LIKE :q
        ORDER BY title ASC
        LIMIT 10
    ");
    $stmt->execute(['q' => '%' . $q . '%']);
    $results = $stmt->fetchAll();
    echo json_encode($results);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
