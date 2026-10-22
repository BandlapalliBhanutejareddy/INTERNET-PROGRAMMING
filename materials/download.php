<?php
require_once __DIR__ . '/../config/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('Error: Invalid material identifier.');
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT * FROM study_materials WHERE id = ?");
    $stmt->execute([$id]);
    $material = $stmt->fetch();

    if (!$material) {
        die('Error: Requested study material not found.');
    }

    $filePath = realpath(__DIR__ . '/../' . $material['file_path']);
    $allowedBase = realpath(__DIR__ . '/..');

    // Prevent path traversal
    if ($filePath === false || strpos($filePath, $allowedBase) !== 0) {
        // Fallback: If physical sample file does not exist yet on disk, stream an on-the-fly informational document
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $material['title']) . '.txt"');
        echo "SIMATS Central Digital Library - Study Material\n";
        echo "====================================================\n";
        echo "Title: " . $material['title'] . "\n";
        echo "Subject: " . $material['subject'] . "\n";
        echo "Academic Year: " . $material['academic_year'] . " | " . $material['semester'] . "\n";
        echo "Description: " . ($material['description'] ?? 'Course lecture notes') . "\n";
        echo "Uploaded: " . $material['created_at'] . "\n\n";
        echo "This is an academic study resource archived in SIMATS Central Digital Library.\n";
        exit;
    }

    if (!file_exists($filePath)) {
        die('Error: Physical file missing from storage.');
    }

    $fileName = basename($material['file_path']);
    $mime = mime_content_type($filePath) ?: 'application/octet-stream';

    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} catch (Exception $e) {
    die('Download failed: ' . $e->getMessage());
}
