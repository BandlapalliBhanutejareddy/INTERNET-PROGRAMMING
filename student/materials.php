<?php
require_once __DIR__ . '/../config/config.php';
$page_title = 'Course Study Materials';

$year = trim($_GET['year'] ?? '');
$semester = trim($_GET['semester'] ?? '');
$search = trim($_GET['q'] ?? '');

$materials = [];

try {
    $pdo = get_db_connection();
    $sql = "SELECT sm.*, u.name AS uploader_name FROM study_materials sm LEFT JOIN users u ON sm.uploaded_by = u.id WHERE 1=1";
    $params = [];

    if (!empty($year)) {
        $sql .= " AND sm.academic_year = :year";
        $params['year'] = $year;
    }
    if (!empty($semester)) {
        $sql .= " AND sm.semester = :sem";
        $params['sem'] = $semester;
    }
    if (!empty($search)) {
        $sql .= " AND (sm.title LIKE :q OR sm.subject LIKE :q OR sm.description LIKE :q)";
        $params['q'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY sm.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $materials = $stmt->fetchAll();
} catch (Exception $e) {
    set_flash('error', 'Error fetching study materials: ' . $e->getMessage());
}

include_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff;">Study Materials Repository</h1>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Download lecture notes, lab manuals, and question banks by academic year & semester</p>
    </div>
    <?php if (is_admin()): ?>
        <a href="<?php echo BASE_URL; ?>/admin/materials.php" class="btn btn-primary btn-sm">&#43; Upload New Material</a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 2rem; padding: 1.25rem;">
    <form action="<?php echo BASE_URL; ?>/student/materials.php" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 220px;">
            <input type="text" name="q" class="form-control" placeholder="Search by topic, subject, or title..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <select name="year" class="form-control" onchange="this.form.submit()">
                <option value="">All Academic Years</option>
                <option value="2026-2027" <?php if ($year === '2026-2027') echo 'selected'; ?>>2026-2027</option>
                <option value="2025-2026" <?php if ($year === '2025-2026') echo 'selected'; ?>>2025-2026</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <select name="semester" class="form-control" onchange="this.form.submit()">
                <option value="">All Semesters</option>
                <option value="Semester 5" <?php if ($semester === 'Semester 5') echo 'selected'; ?>>Semester 5</option>
                <option value="Semester 6" <?php if ($semester === 'Semester 6') echo 'selected'; ?>>Semester 6</option>
                <option value="Semester 7" <?php if ($semester === 'Semester 7') echo 'selected'; ?>>Semester 7</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">&#128269; Filter</button>
            <?php if (!empty($search) || !empty($year) || !empty($semester)): ?>
                <a href="<?php echo BASE_URL; ?>/student/materials.php" class="btn btn-outline">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Materials Table -->
<div class="card" style="padding: 1.5rem;">
    <?php if (!empty($materials)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Material Title & Subject</th>
                        <th>Academic Year</th>
                        <th>Semester</th>
                        <th>File Size</th>
                        <th>Upload Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materials as $m): ?>
                        <tr>
                            <td>
                                <strong style="color: #ffffff;"><?php echo htmlspecialchars($m['title']); ?></strong>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">
                                    Subject: <span style="color: var(--accent);"><?php echo htmlspecialchars($m['subject']); ?></span>
                                </div>
                                <p style="font-size: 0.78rem; color: var(--text-secondary); margin-top: 0.3rem;">
                                    <?php echo htmlspecialchars($m['description'] ?? ''); ?>
                                </p>
                            </td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($m['academic_year']); ?></span></td>
                            <td><?php echo htmlspecialchars($m['semester']); ?></td>
                            <td><?php echo round(($m['file_size'] ?? 1024) / 1024, 1); ?> KB</td>
                            <td><?php echo date('d M Y', strtotime($m['created_at'])); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/materials/download.php?id=<?php echo $m['id']; ?>" class="btn btn-sm btn-primary">
                                    &#128190; Download
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-muted); padding: 3rem;">No study materials found for the selected filter criteria.</p>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
