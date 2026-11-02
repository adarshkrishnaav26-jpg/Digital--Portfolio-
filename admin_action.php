<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_dashboard.php");
    exit;
}

$id = filter_input(INPUT_POST, 'achievement_id', FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? '';

if (!$id || !in_array($action, ['approve', 'reject'], true)) {
    exit("Invalid request.");
}

$stmt = $db->prepare("SELECT id FROM achievements WHERE id = ?");
$stmt->execute([$id]);

if (!$stmt->fetch()) {
    exit("Achievement not found.");
}

if ($action === 'approve') {

    $token = bin2hex(random_bytes(32));
    $hash = hash('sha256', $token);

    $verificationUrl =
        "http://127.0.0.1:8080/verify.php?token=" . $token;

    $qrFile = "qrcodes/achievement_" . $id . ".svg";
    $qrPath = __DIR__ . "/" . $qrFile;

    $command = "python "
        . escapeshellarg(__DIR__ . "/generate_qr.py") . " "
        . escapeshellarg($verificationUrl) . " "
        . escapeshellarg($qrPath) . " 2>&1";

    exec($command, $output, $exitCode);

    if ($exitCode !== 0 || !file_exists($qrPath)) {
        exit("QR generation failed: " . htmlspecialchars(implode("\n", $output)));
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            UPDATE achievements
            SET status = 'Verified',
                verified_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        $stmt = $db->prepare("
            INSERT INTO verification_tokens
                (achievement_id, token_hash, qr_code_url)
            VALUES (?, ?, ?)
            ON CONFLICT(achievement_id)
            DO UPDATE SET
                token_hash = excluded.token_hash,
                qr_code_url = excluded.qr_code_url
        ");

        $stmt->execute([$id, $hash, $qrFile]);

        $db->commit();

        $_SESSION['flash_message'] =
            "Achievement approved and QR Code generated successfully.";

        $_SESSION['verification_url'] = $verificationUrl;

    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        if (file_exists($qrPath)) {
            unlink($qrPath);
        }

        exit("Approval failed. Please check the database.");
    }

} else {

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            UPDATE achievements
            SET status = 'Rejected',
                verified_at = NULL
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        $stmt = $db->prepare("
            DELETE FROM verification_tokens
            WHERE achievement_id = ?
        ");
        $stmt->execute([$id]);

        $db->commit();

        $_SESSION['flash_message'] = "Achievement rejected.";

    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        exit("Rejection failed.");
    }
}

header("Location: admin_dashboard.php");
exit;
