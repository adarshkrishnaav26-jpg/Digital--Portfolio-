<?php
require 'db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    exit('Please login first.');
}

$id = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare(
    "SELECT * FROM credentials WHERE id = ? AND student_id = ?"
);
$stmt->execute([$id, $_SESSION['student_id']]);
$credential = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$credential) {
    exit('Credential not found.');
}

$url = 'http://' . $_SERVER['HTTP_HOST'] .
       '/verify.php?code=' . urlencode($credential['credential_code']);

$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($url);
?>
<!DOCTYPE html>
<html>
<head>
<title>Credential QR Code</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<h2>Credential QR Code</h2>
<p><?= htmlspecialchars($credential['title']) ?></p>
<img src="<?= htmlspecialchars($qrUrl) ?>" width="250" height="250" alt="Credential QR Code">
<p>Verification Code: <?= htmlspecialchars($credential['credential_code']) ?></p>
<p>Verification Link: <a href="<?= htmlspecialchars($url) ?>">Verify Credential</a></p>
</body>
</html>
