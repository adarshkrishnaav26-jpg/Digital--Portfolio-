<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

require 'db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    exit("Invalid achievement ID.");
}

$stmt = $db->prepare("
    SELECT v.token_hash, a.status
    FROM verification_tokens v
    JOIN achievements a ON a.id = v.achievement_id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data || $data['status'] !== 'Verified') {
    exit("Verified credential not found.");
}

$verificationUrl = "http://" . $_SERVER['HTTP_HOST']
    . "/verify.php?token=" . $data['token_hash'];

$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data="
    . urlencode($verificationUrl);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Achievement QR Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="content">
    <div class="panel">
        <h1>Achievement QR Code</h1>
        <p>Scan to verify this achievement.</p>
        <img src="<?= htmlspecialchars($qrUrl) ?>"
             alt="QR Code" width="250" height="250">
        <p>
            <a href="<?= htmlspecialchars($verificationUrl) ?>">
                Open Verification Page
            </a>
        </p>
    </div>
</main>
</body>
</html>
