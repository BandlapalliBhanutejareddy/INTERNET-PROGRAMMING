<?php
/**
 * Experiment 06: PHP Fundamentals
 * Demonstrating variables, data types, arrays, functions, and loops.
 */

// 1. Scalar Variables & Constants
define("INSTITUTE", "SIMATS School of Engineering");
$course_code = "CSA4315";
$course_title = "Internet Programming";
$daily_fine_rate = 5.00; // Float
$max_loan_days = 14;     // Integer
$is_portal_active = true;// Boolean

// 2. Associative and Multidimensional Arrays
$books_catalog = [
    [
        "id" => 101,
        "title" => "Internet & World Wide Web How to Program",
        "author" => "Paul Deitel, Harvey Deitel",
        "copies" => 5,
        "issued_days_ago" => 20
    ],
    [
        "id" => 102,
        "title" => "Web Technologies: Computer Science Perspective",
        "author" => "Jeffrey C. Jackson",
        "copies" => 3,
        "issued_days_ago" => 10
    ],
    [
        "id" => 103,
        "title" => "Database System Concepts",
        "author" => "Abraham Silberschatz",
        "copies" => 6,
        "issued_days_ago" => 17
    ]
];

// 3. User-defined Function: Calculate Overdue Fine
function calculate_fine($days_borrowed, $max_allowed, $rate_per_day) {
    if ($days_borrowed <= $max_allowed) {
        return 0.00;
    }
    $overdue_days = $days_borrowed - $max_allowed;
    return $overdue_days * $rate_per_day;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 06 - PHP Basics</title>
    <style>
        body { font-family: monospace; background: #0f172a; color: #f8fafc; padding: 2rem; }
        .box { background: #1e293b; border: 1px solid #334155; padding: 1.5rem; border-radius: 8px; max-width: 700px; margin: 0 auto; }
        h2 { color: #38bdf8; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #334155; padding: 0.5rem; text-align: left; }
        th { background: #2563eb; color: #fff; }
    </style>
</head>
<body>
    <div class="box">
        <h2><?php echo INSTITUTE; ?> &mdash; <?php echo $course_code; ?></h2>
        <p><strong>Course:</strong> <?php echo $course_title; ?></p>
        <p><strong>Daily Fine Rate:</strong> ₹<?php echo number_format($daily_fine_rate, 2); ?> | <strong>Max Loan:</strong> <?php echo $max_loan_days; ?> days</p>

        <h3>Book Loan Fine Calculations (Processed via PHP):</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Days Kept</th>
                    <th>Overdue Fine</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books_catalog as $book): ?>
                    <?php $fine = calculate_fine($book["issued_days_ago"], $max_loan_days, $daily_fine_rate); ?>
                    <tr>
                        <td>#<?php echo $book["id"]; ?></td>
                        <td><?php echo htmlspecialchars($book["title"]); ?></td>
                        <td><?php echo $book["issued_days_ago"]; ?> days</td>
                        <td style="<?php echo ($fine > 0) ? 'color: #ef4444; font-weight: bold;' : 'color: #10b981;'; ?>">
                            ₹<?php echo number_format($fine, 2); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
