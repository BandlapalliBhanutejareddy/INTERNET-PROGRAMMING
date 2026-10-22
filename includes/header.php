<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}
sync_overdue_fines();
$page_title = $page_title ?? 'SIMATS Digital Library';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title . ' | ' . APP_NAME); ?></title>
    <meta name="description" content="Comprehensive Academic Library Management System for Internet Programming Lab - CSA4315">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="site-wrapper">
        <?php include_once __DIR__ . '/navbar.php'; ?>
        
        <?php $flash = get_flash(); if ($flash): ?>
            <div class="flash-container">
                <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?> alert-dismissible" id="globalAlert">
                    <span class="alert-icon">
                        <?php if ($flash['type'] === 'success'): ?>&#10004;<?php elseif ($flash['type'] === 'error'): ?>&#9888;<?php else: ?>&#8505;<?php endif; ?>
                    </span>
                    <span class="alert-message"><?php echo htmlspecialchars($flash['message']); ?></span>
                    <button type="button" class="close-btn" onclick="document.getElementById('globalAlert').remove()">&times;</button>
                </div>
            </div>
        <?php endif; ?>
        
        <main class="main-content">
