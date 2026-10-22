<?php
/**
 * SIMATS Central Digital Library Management System
 * Core Configuration and Global Bootstrap File
 */

// 1. Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// 2. Application Constants
define('APP_NAME', 'SIMATS Digital Library');
define('APP_VERSION', '1.0.0');
define('APP_TAGLINE', 'Internet Programming Lab Project - CSA4315');

// Base URL calculation
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

// Detect project root path dynamically
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
$current_dir = str_replace('\\', '/', dirname(__DIR__));
$sub_folder = str_replace($doc_root, '', $current_dir);
$base_url = rtrim($protocol . $host . $sub_folder, '/');
if (empty($base_url)) {
    $base_url = '/';
}
define('BASE_URL', $base_url);

// 3. Database Credentials (XAMPP Standard Defaults)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'library_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// 4. PDO Database Connection Singleton
function get_db_connection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Graceful diagnostic error page when database is offline
            render_db_error_page($e->getMessage());
            exit;
        }
    }
    return $pdo;
}

// 5. Friendly DB Diagnostic Error Page
function render_db_error_page($error_msg) {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Connection Notice - <?php echo htmlspecialchars(APP_NAME); ?></title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
            .card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 2.5rem; max-width: 650px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
            h1 { color: #38bdf8; margin-top: 0; font-size: 1.6rem; }
            .alert { background: #451a03; border-left: 4px solid #f97316; padding: 1rem; border-radius: 6px; margin: 1.5rem 0; font-family: monospace; font-size: 0.9rem; color: #fdba74; }
            ol { line-height: 1.8; color: #cbd5e1; }
            code { background: #0f172a; padding: 0.2rem 0.4rem; border-radius: 4px; color: #38bdf8; }
            .btn { display: inline-block; background: #2563eb; color: #fff; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 1rem; }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>Database Connection Setup Required</h1>
            <p>The application could not connect to MySQL database <code><?php echo htmlspecialchars(DB_NAME); ?></code>.</p>
            <div class="alert">Error: <?php echo htmlspecialchars($error_msg); ?></div>
            <h3>Quick Setup Steps in XAMPP:</h3>
            <ol>
                <li>Open the <strong>XAMPP Control Panel</strong> and click <strong>Start</strong> next to <strong>MySQL</strong> and <strong>Apache</strong>.</li>
                <li>Open your browser and visit: <code>http://localhost/phpmyadmin/</code></li>
                <li>Import <code>database/schema.sql</code> and then <code>database/seed.sql</code>.</li>
                <li>Reload this page.</li>
            </ol>
            <a href="javascript:location.reload()" class="btn">Retry Connection</a>
        </div>
    </body>
    </html>
    <?php
}

// 6. Security & Sanitization Helpers
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            die('Error 403: Invalid or missing CSRF token.');
        }
    }
}

// 7. Authentication State Helpers
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_admin() {
    return is_logged_in() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function is_student() {
    return is_logged_in() && ($_SESSION['user']['role'] ?? '') === 'student';
}

function require_login() {
    if (!is_logged_in()) {
        set_flash('error', 'Please login to access this page.');
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        set_flash('error', 'Access denied. Administrator privileges required.');
        header('Location: ' . BASE_URL . '/student/dashboard.php');
        exit;
    }
}

function require_student() {
    require_login();
    if (!is_student() && !is_admin()) {
        set_flash('error', 'Access denied. Student portal area.');
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }
}

// 8. Flash Messaging Helpers
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'error', 'warning', 'info'
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// 9. Dynamic System Settings Helper
function get_setting($key, $default = null) {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

// 10. Automated Overdue Fine Synchronizer
function sync_overdue_fines() {
    try {
        $pdo = get_db_connection();
        $rate = (float)get_setting('daily_fine_rate', 5.00);

        // Find active issues that are past due_date
        $sql = "SELECT id, user_id, due_date, DATEDIFF(CURDATE(), due_date) AS days_late
                FROM book_issues
                WHERE status IN ('issued', 'overdue')
                  AND return_date IS NULL
                  AND due_date < CURDATE()";
        $stmt = $pdo->query($sql);
        $overdues = $stmt->fetchAll();

        foreach ($overdues as $row) {
            $days = max(1, (int)$row['days_late']);
            $amount = $days * $rate;

            // Mark issue as overdue
            $upIssue = $pdo->prepare("UPDATE book_issues SET status = 'overdue' WHERE id = ?");
            $upIssue->execute([$row['id']]);

            // Insert or update fine entry
            $fStmt = $pdo->prepare("
                INSERT INTO fines (issue_id, user_id, overdue_days, daily_rate, fine_amount, status)
                VALUES (?, ?, ?, ?, ?, 'unpaid')
                ON DUPLICATE KEY UPDATE
                    overdue_days = VALUES(overdue_days),
                    daily_rate = VALUES(daily_rate),
                    fine_amount = IF(status = 'unpaid', VALUES(fine_amount), fine_amount)
            ");
            $fStmt->execute([$row['id'], $row['user_id'], $days, $rate, $amount]);
        }
    } catch (Exception $e) {
        // Silently tolerate if tables are not yet migrated
    }
}
