<?php
session_start();
require 'db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $issuer = trim($_POST['issuer'] ?? '');
    $date = $_POST['issue_date'] ?? '';

    if ($title !== '' && $issuer !== '') {
        $code = 'CRD-' . strtoupper(bin2hex(random_bytes(5)));

        $stmt = $db->prepare(
            "INSERT INTO credentials
            (student_id, title, issuer, issue_date, credential_code)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $_SESSION['student_id'],
            $title,
            $issuer,
            $date,
            $code
        ]);

        $message = "Credential added! Verification code: " . $code;
    } else {
        $message = "Please enter all required details.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Credential</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Add New Credential</h1>

    <p><?= htmlspecialchars($message) ?></p>

    <form method="POST">
        <label>Certificate / Credential Name</label>
        <input type="text" name="title" required>

        <label>Issued By</label>
        <input type="text" name="issuer" required>

        <label>Issue Date</label>
        <input type="date" name="issue_date">

        <button type="submit">Save Credential</button>
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</div>
</body>
</html>
