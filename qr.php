<?php
require 'db.php';

$token = $_GET['token'] ?? '';

if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
    http_response_code(400);
    exit("Invalid verification token.");
}

$hash = hash('sha256', $token);

$stmt = $db->prepare("
    SELECT a.status
    FROM verification_tokens v
    JOIN achievements a ON a.id = v.achievement_id
    WHERE v.token_hash = ?
");
$stmt->execute([$hash]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result || $result['status'] !== 'Verified') {
    http_response_code(404);
    exit("Verified credential not found.");
}

$verificationUrl = "http://" . $_SERVER['HTTP_HOST']
    . "/verify.php?token=" . urlencode($token);

$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data="
    . urlencode($verificationUrl);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Credential QR Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="content">
    <div class="panel">
        <h1>Credential QR Code</h1>
        <p>Scan this QR code to verify the credential.</p>
        <img src="<?= htmlspecialchars($qrUrl) ?>"
             alt="Credential QR Code"
             width="250" height="250">
        <p>
            <a href="<?= htmlspecialchars($verificationUrl) ?>">
                Open Verification Page
            </a>
        </p>
    </div>
</main>
</body>
</html>
