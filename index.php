<?php
require_once __DIR__ . '/config/config.php';
$page_title = 'Welcome to Central Digital Library';

// Fetch quick statistics
$stats = [
    'total_books' => 0,
    'total_copies' => 0,
    'available_copies' => 0,
    'students_count' => 0,
    'materials_count' => 0
];
$featured_books = [];

try {
    $pdo = get_db_connection();
    $bStmt = $pdo->query("SELECT COUNT(*) AS total_titles, SUM(total_copies) AS total_copies, SUM(available_copies) AS available_copies FROM books");
    $bData = $bStmt->fetch();
    if ($bData) {
        $stats['total_books'] = (int)($bData['total_titles'] ?? 0);
        $stats['total_copies'] = (int)($bData['total_copies'] ?? 0);
        $stats['available_copies'] = (int)($bData['available_copies'] ?? 0);
    }

    $uStmt = $pdo->query("SELECT COUNT(*) AS cnt FROM users WHERE role = 'student'");
    $stats['students_count'] = (int)($uStmt->fetchColumn() ?? 0);

    $mStmt = $pdo->query("SELECT COUNT(*) AS cnt FROM study_materials");
    $stats['materials_count'] = (int)($mStmt->fetchColumn() ?? 0);

    $featStmt = $pdo->query("SELECT * FROM books ORDER BY id DESC LIMIT 6");
    $featured_books = $featStmt->fetchAll();
} catch (Exception $e) {
    // Database connection or table initialization handled gracefully
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="hero-section" style="text-align: center; padding: 3rem 1rem 4rem; background: radial-gradient(circle at 50% 20%, rgba(37, 99, 235, 0.15) 0%, rgba(15, 23, 42, 0) 70%);">
    <div style="display: inline-block; padding: 0.35rem 1rem; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 9999px; color: #38bdf8; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1.25rem;">
        &#9733; CSA4315 Internet Programming Lab &bull; Semester 5
    </div>
    <h1 style="font-size: 2.75rem; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 1rem; color: #ffffff;">
        Academic Knowledge & Digital Library
    </h1>
    <p style="font-size: 1.1rem; color: var(--text-secondary); max-width: 680px; margin: 0 auto 2rem; line-height: 1.6;">
        Explore engineering textbooks, research materials, course lecture notes, and track your active book loans in real time.
    </p>

    <!-- Search Form -->
    <form action="<?php echo BASE_URL; ?>/books/catalog.php" method="GET" style="max-width: 640px; margin: 0 auto; display: flex; gap: 0.5rem; background: #1e293b; padding: 0.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-lg);">
        <input type="text" name="q" placeholder="Search by title, author, category, or ISBN..." style="flex: 1; background: transparent; border: none; padding: 0.75rem 1rem; color: #ffffff; font-size: 0.95rem; outline: none;">
        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.75rem;">
            &#128269; Search
        </button>
    </form>
</div>

<!-- Quick Statistics Grid -->
<div class="grid grid-cols-4" style="margin-bottom: 3.5rem;">
    <div class="stat-card">
        <div class="stat-icon">&#128218;</div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['total_books']); ?></h3>
            <p>Catalog Titles</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.15);">&#9989;</div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['available_copies']); ?></h3>
            <p>Available Copies</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #f59e0b; background: rgba(245, 158, 11, 0.15);">&#127891;</div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['students_count']); ?></h3>
            <p>Active Students</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color: #a855f7; background: rgba(168, 85, 247, 0.15);">&#128193;</div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['materials_count']); ?></h3>
            <p>Study Materials</p>
        </div>
    </div>
</div>

<!-- Featured Books Showcase -->
<div style="margin-bottom: 3.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #ffffff;">Featured Catalog Books</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Core reference books for Computer Science & Engineering</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/books/catalog.php" class="btn btn-outline btn-sm">
            View All Catalog &rarr;
        </a>
    </div>

    <div class="grid grid-cols-3">
        <?php if (!empty($featured_books)): ?>
            <?php foreach ($featured_books as $book): ?>
                <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                            <span class="badge badge-info"><?php echo htmlspecialchars($book['category']); ?></span>
                            <?php if ($book['available_copies'] > 0): ?>
                                <span class="badge badge-success">&#10004; <?php echo $book['available_copies']; ?> Available</span>
                            <?php else: ?>
                                <span class="badge badge-danger">&#10006; Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.4rem; color: #ffffff;">
                            <?php echo htmlspecialchars($book['title']); ?>
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                            By <strong><?php echo htmlspecialchars($book['author']); ?></strong>
                        </p>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1rem;">
                            <?php echo htmlspecialchars(mb_strimwidth($book['description'] ?? 'No description provided.', 0, 110, '...')); ?>
                        </p>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1rem; margin-top: 1rem;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">ISBN: <?php echo htmlspecialchars($book['isbn']); ?></span>
                        <a href="<?php echo BASE_URL; ?>/books/view.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline">
                            Details &rarr;
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card" style="grid-column: span 3; text-align: center; padding: 3rem;">
                <p style="color: var(--text-muted);">Database tables empty or pending import. Run schema.sql and seed.sql to populate catalog.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Academic Modules & Portal Shortcuts -->
<div class="grid grid-cols-3" style="margin-bottom: 3rem;">
    <div class="card" style="border-left: 4px solid var(--primary);">
        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.75rem; color: #ffffff;">&#127891; Student Portal</h3>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.25rem;">
            Self-service portal for enrolled students to track active loan return due dates, check overdue fines, and browse lecture materials.
        </p>
        <a href="<?php echo BASE_URL; ?>/student/dashboard.php" class="btn btn-outline-student btn-sm">Enter Student Portal &rarr;</a>
    </div>

    <div class="card" style="border-left: 4px solid var(--warning);">
        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.75rem; color: #ffffff;">&#9881; Librarian Admin Portal</h3>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.25rem;">
            Authorized administrative dashboard for issuing books, processing returns, calculating daily overdue penalties, and inventory management.
        </p>
        <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="btn btn-outline-admin btn-sm">Librarian Access &rarr;</a>
    </div>

    <div class="card" style="border-left: 4px solid var(--accent);">
        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.75rem; color: #ffffff;">&#128193; IP Lab Programs Index</h3>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.25rem;">
            Access all 18 structured Internet Programming Lab experiments with full algorithms, source code, test inputs, and expected outputs.
        </p>
        <a href="<?php echo BASE_URL; ?>/LAB/README.md" class="btn btn-outline btn-sm">Explore 18 Lab Experiments &rarr;</a>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
