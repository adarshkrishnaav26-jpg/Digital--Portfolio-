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
    die("Invalid request.");
}

$stmt = $db->prepare("SELECT id FROM achievements WHERE id = ?");
$stmt->execute([$id]);

if (!$stmt->fetch()) {
    die("Achievement not found.");
}

if ($action === 'approve') {
    $token = bin2hex(random_bytes(32));
    $hash = hash('sha256', $token);

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            UPDATE achievements
            SET status = 'Verified', verified_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        $stmt = $db->prepare("
            INSERT INTO verification_tokens
                (achievement_id, token_hash, qr_code_url)
            VALUES (?, ?, '')
            ON CONFLICT(achievement_id)
            DO UPDATE SET
                token_hash = excluded.token_hash,
                qr_code_url = ''
        ");
        $stmt->execute([$id, $hash]);

        $db->commit();

        $_SESSION['flash_message'] = "Achievement approved successfully.";
        $_SESSION['verification_url'] =
            "http://127.0.0.1:8080/verify.php?token=" . $token;

    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        die("Approval failed. Please check the database.");
    }

} else {
    $db->beginTransaction();

    try {
        $stmt = $db->prepare("
            UPDATE achievements
            SET status = 'Rejected', verified_at = NULL
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        $stmt = $db->prepare("
            DELETE FROM verification_tokens WHERE achievement_id = ?
        ");
        $stmt->execute([$id]);

        $db->commit();
        $_SESSION['flash_message'] = "Achievement rejected.";

    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        die("Rejection failed. Please check the database.");
    }
}

header("Location: admin_dashboard.php");
exit;
