<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Manage Study Materials';

$pdo = get_db_connection();
$errors = [];

// Handle File Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    verify_csrf();

    $title = trim($_POST['title'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $academic_year = trim($_POST['academic_year'] ?? '2026-2027');
    $semester = trim($_POST['semester'] ?? 'Semester 5');
    $description = trim($_POST['description'] ?? '');
    $admin_id = current_user()['id'];

    if (empty($title)) $errors[] = 'Material title is required.';
    if (empty($subject)) $errors[] = 'Subject name is required.';

    // File validation
    if (!isset($_FILES['material_file']) || $_FILES['material_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Please select a valid file to upload.';
    } else {
        $file = $_FILES['material_file'];
        $allowed_exts = ['pdf', 'docx', 'doc', 'pptx', 'ppt', 'zip', 'txt'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_exts)) {
            $errors[] = 'Invalid file type. Allowed formats: ' . implode(', ', $allowed_exts);
        } elseif ($file['size'] > 15 * 1024 * 1024) { // 15MB limit
            $errors[] = 'File exceeds maximum upload limit of 15MB.';
        } else {
            $upload_dir = __DIR__ . '/../uploads/materials/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $safe_filename = uniqid('mat_', true) . '.' . $ext;
            $dest_path = $upload_dir . $safe_filename;
            $rel_path = 'uploads/materials/' . $safe_filename;

            if (move_uploaded_file($file['tmp_name'], $dest_path)) {
                $ins = $pdo->prepare("
                    INSERT INTO study_materials (title, subject, academic_year, semester, description, file_path, file_size, uploaded_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $ins->execute([$title, $subject, $academic_year, $semester, $description, $rel_path, $file['size'], $admin_id]);

                set_flash('success', 'Study material uploaded successfully!');
                header('Location: ' . BASE_URL . '/admin/materials.php');
                exit;
            } else {
                $errors[] = 'Failed to move uploaded file to destination directory.';
            }
        }
    }
}

// Handle File Delete
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT file_path FROM study_materials WHERE id = ?");
    $stmt->execute([$del_id]);
    $mat = $stmt->fetch();
    if ($mat) {
        $fullPath = __DIR__ . '/../' . $mat['file_path'];
        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);
        }
        $del = $pdo->prepare("DELETE FROM study_materials WHERE id = ?");
        $del->execute([$del_id]);
        set_flash('success', 'Study material removed successfully.');
    }
    header('Location: ' . BASE_URL . '/admin/materials.php');
    exit;
}

$materials = $pdo->query("SELECT sm.*, u.name AS uploader_name FROM study_materials sm LEFT JOIN users u ON sm.uploaded_by = u.id ORDER BY sm.id DESC")->fetchAll();

include_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff;">Manage Study Materials</h1>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Upload lecture notes, syllabi, question banks, and lab guides for students</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error" style="margin-bottom: 1.5rem;">
        <ul style="margin-left: 1rem;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Upload Box -->
<div class="card" style="margin-bottom: 2.5rem; padding: 2rem; border-top: 4px solid var(--accent);">
    <h3 style="font-size: 1.25rem; font-weight: 700; color: #ffffff; margin-bottom: 1.25rem;">&#128193; Upload New Course Material</h3>
    <form action="<?php echo BASE_URL; ?>/admin/materials.php" method="POST" enctype="multipart/form-data" data-validate="true">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="upload">

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="title">Document Title *</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="e.g. CSA4315 Unit 1 Complete Notes" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="subject">Course Subject *</label>
                <input type="text" id="subject" name="subject" class="form-control" placeholder="e.g. Internet Programming" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="academic_year">Academic Year</label>
                <select id="academic_year" name="academic_year" class="form-control">
                    <option value="2026-2027">2026-2027</option>
                    <option value="2025-2026">2025-2026</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="semester">Semester</label>
                <select id="semester" name="semester" class="form-control">
                    <option value="Semester 5">Semester 5</option>
                    <option value="Semester 6">Semester 6</option>
                    <option value="Semester 7">Semester 7</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="material_file">Upload Document File (PDF, DOCX, PPTX, ZIP - Max 15MB) *</label>
            <input type="file" id="material_file" name="material_file" class="form-control" required style="padding: 0.5rem;">
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Summary & Description</label>
            <textarea id="description" name="description" class="form-control" rows="2" placeholder="Brief outline of syllabus covered in this file..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary">&#128190; Upload and Publish Material</button>
    </form>
</div>

<!-- Uploaded Materials Table -->
<div class="card" style="padding: 1.5rem;">
    <h3 style="font-size: 1.25rem; font-weight: 700; color: #ffffff; margin-bottom: 1.25rem;">Published Academic Materials</h3>
    <?php if (!empty($materials)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title & Subject</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Size</th>
                        <th>Uploaded By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materials as $m): ?>
                        <tr>
                            <td>
                                <strong style="color: #ffffff;"><?php echo htmlspecialchars($m['title']); ?></strong>
                                <div style="font-size: 0.78rem; color: var(--accent);"><?php echo htmlspecialchars($m['subject']); ?></div>
                            </td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($m['academic_year']); ?></span></td>
                            <td><?php echo htmlspecialchars($m['semester']); ?></td>
                            <td><?php echo round(($m['file_size'] ?? 1024) / 1024, 1); ?> KB</td>
                            <td><?php echo htmlspecialchars($m['uploader_name'] ?? 'Admin'); ?></td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="<?php echo BASE_URL; ?>/materials/download.php?id=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline">
                                        Download
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/admin/materials.php?delete=<?php echo $m['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this study material?');">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No study materials published yet.</p>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
