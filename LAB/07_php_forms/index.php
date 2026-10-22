<?php
/**
 * Experiment 07: PHP Form Handling & Data Sanitization
 */
$errors = [];
$submitted_data = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitization using htmlspecialchars & trim
    $student_id = htmlspecialchars(trim($_POST["student_id"] ?? ""));
    $book_title = htmlspecialchars(trim($_POST["book_title"] ?? ""));
    $email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
    $copies = filter_var($_POST["copies"] ?? 1, FILTER_VALIDATE_INT);

    // Server-side Validation
    if (empty($student_id)) {
        $errors[] = "Student ID is required.";
    } elseif (!preg_match("/^[0-9]{9}$/", $student_id)) {
        $errors[] = "Student ID must be exactly 9 digits.";
    }

    if (empty($book_title)) {
        $errors[] = "Book Title cannot be blank.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid college email address is required.";
    }

    if ($copies === false || $copies < 1 || $copies > 3) {
        $errors[] = "Requested copies must be an integer between 1 and 3.";
    }

    if (empty($errors)) {
        $submitted_data = [
            "student_id" => $student_id,
            "book_title" => $book_title,
            "email" => $email,
            "copies" => $copies,
            "timestamp" => date("d M Y, h:i A")
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 07 - PHP Forms & Sanitization</title>
    <style>
        body { font-family: -apple-system, sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; }
        .card { max-width: 550px; margin: 0 auto; background: #1e293b; padding: 2rem; border-radius: 8px; border: 1px solid #334155; }
        h2 { color: #38bdf8; margin-top: 0; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.35rem; font-weight: 600; font-size: 0.9rem; }
        input { width: 100%; padding: 0.65rem; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #fff; box-sizing: border-box; }
        button { background: #2563eb; color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 1rem; }
        .alert-error { background: rgba(239, 68, 68, 0.2); border-left: 4px solid #ef4444; padding: 0.75rem 1rem; border-radius: 4px; margin-bottom: 1rem; color: #fca5a5; }
        .alert-success { background: rgba(16, 185, 129, 0.2); border-left: 4px solid #10b981; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; color: #a7f3d0; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Book Reservation Request (PHP POST)</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <strong>Validation Errors:</strong>
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?php echo $e; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($submitted_data): ?>
            <div class="alert-success">
                <strong>&#10004; Reservation Recorded!</strong><br>
                Student: <?php echo $submitted_data["student_id"]; ?><br>
                Book: <?php echo $submitted_data["book_title"]; ?><br>
                Email: <?php echo $submitted_data["email"]; ?><br>
                Copies: <?php echo $submitted_data["copies"]; ?><br>
                Date: <?php echo $submitted_data["timestamp"]; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="form-group">
                <label>Student Registration ID *</label>
                <input type="text" name="student_id" placeholder="9-digit ID">
            </div>

            <div class="form-group">
                <label>Book Title *</label>
                <input type="text" name="book_title" placeholder="e.g. Internet & World Wide Web">
            </div>

            <div class="form-group">
                <label>Student Email Address *</label>
                <input type="email" name="email" placeholder="student@college.edu">
            </div>

            <div class="form-group">
                <label>Number of Copies (1 - 3)</label>
                <input type="number" name="copies" value="1" min="1" max="3">
            </div>

            <button type="submit">Submit Reservation</button>
        </form>
    </div>
</body>
</html>
