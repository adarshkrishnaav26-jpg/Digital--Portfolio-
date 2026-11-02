<?php
require 'db.php';

$token = trim($_GET['token'] ?? '');
$code = trim($_GET['code'] ?? $_POST['code'] ?? '');

$result = null;
$error = '';

if ($token !== '') {
    $tokenHash = hash('sha256', $token);

    $stmt = $db->prepare("
        SELECT
            achievements.title,
            achievements.status,
            achievements.issue_date,
            students.name,
            students.department,
            verification_tokens.token_hash,
            verification_tokens.created_at
        FROM verification_tokens
        JOIN achievements
            ON achievements.id = verification_tokens.achievement_id
        JOIN students
            ON students.id = achievements.student_id
        WHERE verification_tokens.token_hash = ?
    ");

    $stmt->execute([$tokenHash]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        $error = 'Invalid verification token.';
    }
} elseif ($code !== '') {
    $stmt = $db->prepare("
        SELECT credentials.*, students.name
        FROM credentials
        JOIN students ON students.id = credentials.student_id
        WHERE credential_code = ?
    ");

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

<header>
    <a class="brand" href="index.php">Digital Portfolio</a>
</header>

<main class="content">
<section class="panel">

<h1>Credential Verification</h1>

<form method="POST">
    <label>Credential Code</label>
    <input type="text" name="code"
           value="<?= htmlspecialchars($code) ?>" required>
    <button type="submit">Verify Now</button>
</form>

<?php if ($result): ?>

    <?php
    $status = $result['status'] ?? 'Invalid';
    $isValid = (strcasecmp($status, 'Verified') === 0 ||
                strcasecmp($status, 'Valid') === 0);
    ?>

    <h2><?= $isValid ? 'Verified Credential' : 'Credential Status' ?></h2>

    <p><strong>Status:</strong>
        <?= htmlspecialchars($status) ?>
    </p>

    <p><strong>Student:</strong>
        <?= htmlspecialchars($result['name']) ?>
    </p>

    <p><strong>Achievement:</strong>
        <?= htmlspecialchars($result['title']) ?>
    </p>

    <?php if (isset($result['department'])): ?>
    <p><strong>Department:</strong>
        <?= htmlspecialchars($result['department'] ?? '') ?>
    </p>
    <?php endif; ?>

    <?php if (isset($result['token_hash'])): ?>
    <p><strong>SHA-256 Hash:</strong>
        <?= htmlspecialchars($result['token_hash']) ?>
    </p>
    <?php endif; ?>

    <?php if (isset($result['issuer'])): ?>
    <p><strong>Issued By:</strong>
        <?= htmlspecialchars($result['issuer']) ?>
    </p>
    <?php endif; ?>

    <?php if (isset($result['issue_date'])): ?>
    <p><strong>Issue Date:</strong>
        <?= htmlspecialchars($result['issue_date'] ?? '') ?>
    </p>
    <?php endif; ?>

<?php elseif ($error): ?>

    <p><?= htmlspecialchars($error) ?></p>

<?php endif; ?>

<p><a href="index.php">Home</a></p>

</section>
</main>
</body>
</html>
