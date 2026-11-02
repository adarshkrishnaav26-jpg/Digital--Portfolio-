<?php
require 'db.php';

$code = trim($_GET['code'] ?? $_POST['code'] ?? '');
$result = null;
$error = '';

if ($code !== '') {
    $stmt = $db->prepare(
        "SELECT credentials.*, students.name
         FROM credentials
         JOIN students ON students.id = credentials.student_id
         WHERE credential_code = ?"
    );
    $stmt->execute([$code]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        $error = 'Credential not found. Invalid code.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Credential Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Credential Verification</h1>

    <form method="POST">
        <label>Credential Code</label>
        <input type="text" name="code"
               value="<?= htmlspecialchars($code) ?>" required>
        <button type="submit">Verify Now</button>
    </form>

    <?php if ($result): ?>
        <h2>Credential Found</h2>
        <p><strong>Status:</strong> <?= htmlspecialchars($result['status']) ?></p>
        <p><strong>Student:</strong> <?= htmlspecialchars($result['name']) ?></p>
        <p><strong>Credential:</strong> <?= htmlspecialchars($result['title']) ?></p>
        <p><strong>Issued By:</strong> <?= htmlspecialchars($result['issuer']) ?></p>
        <p><strong>Issue Date:</strong> <?= htmlspecialchars($result['issue_date'] ?? '') ?></p>
        <p><strong>Code:</strong> <?= htmlspecialchars($result['credential_code']) ?></p>
    <?php elseif ($error): ?>
        <p><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <p><a href="index.php">Home</a></p>
</div>
</body>
</html>
