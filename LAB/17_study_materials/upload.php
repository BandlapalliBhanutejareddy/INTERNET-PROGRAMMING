<?php
/**
 * Experiment 17: Secure File Uploads
 */
require_once __DIR__ . '/../../config/config.php';
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["note"])) {
    $file = $_FILES["note"];
    $allowed = ["pdf", "docx", "txt"];
    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        $msg = "<span style='color: #ef4444;'>Error: Invalid file type. Only PDF, DOCX, TXT allowed.</span>";
    } elseif ($file["size"] > 5 * 1024 * 1024) {
        $msg = "<span style='color: #ef4444;'>Error: File size exceeds 5MB limit.</span>";
    } else {
        $dest = __DIR__ . "/../../uploads/materials/" . uniqid("exp17_") . "." . $ext;
        if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
        if (move_uploaded_file($file["tmp_name"], $dest)) {
            $msg = "<span style='color: #10b981;'>&#10004; File uploaded securely to storage!</span>";
        } else {
            $msg = "<span style='color: #ef4444;'>Upload failed.</span>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 17 - File Uploads</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #fff; padding: 2rem; }
        .card { max-width: 500px; margin: 0 auto; background: #1e293b; padding: 1.5rem; border-radius: 8px; border: 1px solid #334155; }
        input { margin-bottom: 1rem; color: #fff; }
        button { background: #2563eb; color: #fff; padding: 0.6rem 1.25rem; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Secure Study Material File Upload</h2>
        <?php if ($msg): ?><p><?php echo $msg; ?></p><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Select Lecture PDF / DOCX (Max 5MB):</label><br><br>
            <input type="file" name="note" required><br>
            <button type="submit">&#128190; Upload Document</button>
        </form>
    </div>
</body>
</html>
