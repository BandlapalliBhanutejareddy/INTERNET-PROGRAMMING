<?php
$user = current_user();
?>
<nav class="navbar" id="mainNavbar">
    <div class="nav-container">
        <a href="<?php echo BASE_URL; ?>/index.php" class="brand-logo">
            <span class="logo-icon">&#128218;</span>
            <span class="brand-text">
                <span class="brand-title"><?php echo htmlspecialchars(APP_NAME); ?></span>
                <span class="brand-subtitle">CSA4315 Academic Portal</span>
            </span>
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="nav-menu" id="navMenu">
            <ul class="nav-links">
                <li><a href="<?php echo BASE_URL; ?>/index.php" class="nav-link">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>/books/catalog.php" class="nav-link">Book Catalog</a></li>
                <li><a href="<?php echo BASE_URL; ?>/student/materials.php" class="nav-link">Study Materials</a></li>
                <li><a href="<?php echo BASE_URL; ?>/LAB/README.md" class="nav-link badge-link">IP Lab Manual</a></li>
            </ul>

            <div class="nav-actions">
                <?php if ($user): ?>
                    <?php if ($user['role'] === 'admin'): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-admin dropdown-btn">
                                &#9881; Admin Portal &#9662;
                            </button>
                            <div class="dropdown-content">
                                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php">&#128202; Admin Dashboard</a>
                                <a href="<?php echo BASE_URL; ?>/admin/manage_books.php">&#128214; Manage Books</a>
                                <a href="<?php echo BASE_URL; ?>/admin/issue_book.php">&#128228; Issue Book</a>
                                <a href="<?php echo BASE_URL; ?>/admin/return_book.php">&#128229; Return Book</a>
                                <a href="<?php echo BASE_URL; ?>/admin/transactions.php">&#128392; All Borrowings</a>
                                <a href="<?php echo BASE_URL; ?>/admin/fines.php">&#128176; Fine Records</a>
                                <a href="<?php echo BASE_URL; ?>/admin/materials.php">&#128193; Manage Materials</a>
                                <a href="<?php echo BASE_URL; ?>/admin/students.php">&#128101; Student Accounts</a>
                                <a href="<?php echo BASE_URL; ?>/admin/settings.php">&#9881; Portal Settings</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-student dropdown-btn">
                                &#127891; Student Portal &#9662;
                            </button>
                            <div class="dropdown-content">
                                <a href="<?php echo BASE_URL; ?>/student/dashboard.php">&#128200; My Dashboard</a>
                                <a href="<?php echo BASE_URL; ?>/student/my_loans.php">&#128214; My Borrowed Books</a>
                                <a href="<?php echo BASE_URL; ?>/student/my_fines.php">&#128176; My Overdue Fines</a>
                                <a href="<?php echo BASE_URL; ?>/student/materials.php">&#128193; Study Notes</a>
                                <a href="<?php echo BASE_URL; ?>/student/profile.php">&#128100; My Profile</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <span class="user-greeting">
                        <span class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                        <span class="user-name"><?php echo htmlspecialchars($user['name']); ?></span>
                    </span>

                    <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="btn btn-sm btn-danger">&#128682; Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/auth/login.php" class="btn btn-outline">Sign In</a>
                    <a href="<?php echo BASE_URL; ?>/auth/register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
