<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

require 'db.php';

$students = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total = $db->query("SELECT COUNT(*) FROM achievements")->fetchColumn();
$pending = $db->query("SELECT COUNT(*) FROM achievements WHERE status='Pending'")->fetchColumn();
$verified = $db->query("SELECT COUNT(*) FROM achievements WHERE status='Verified'")->fetchColumn();

$stmt = $db->query("
    SELECT achievements.*, students.name
    FROM achievements
    JOIN students ON students.id = achievements.student_id
    ORDER BY achievements.id DESC
");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <a class="brand" href="index.php">Admin Dashboard</a>
    <nav>
        <a href="index.php">Home</a>
        <a href="admin_logout.php">Logout</a>
    </nav>
</header>

<main class="content">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?>!</h1>

    <h2>Admin Statistics</h2>

    <section class="stats">
        <div class="panel">
            <h3>Total Students</h3>
            <p><?= (int)$students ?></p>
        </div>

        <div class="panel">
            <h3>Total Achievements</h3>
            <p><?= (int)$total ?></p>
        </div>

        <div class="panel">
            <h3>Pending</h3>
            <p><?= (int)$pending ?></p>
        </div>

        <div class="panel">
            <h3>Verified</h3>
            <p><?= (int)$verified ?></p>
        </div>
    </section>

    <h2>Achievement Verification Queue</h2>

    <?php if (count($items) > 0): ?>
        <?php foreach ($items as $item): ?>
            <div class="panel">
                <h3><?= htmlspecialchars($item['title']) ?></h3>
                <p>Student: <?= htmlspecialchars($item['name']) ?></p>
                <p>Category: <?= htmlspecialchars($item['category'] ?? 'N/A') ?></p>
                <p>Status: <?= htmlspecialchars($item['status']) ?></p>
                <p>Issuer: <?= htmlspecialchars($item['issuer_name'] ?? 'N/A') ?></p>
                <p>Evidence: <?= htmlspecialchars($item['evidence_url'] ?? 'N/A') ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No achievements submitted yet.</p>
    <?php endif; ?>
</main>

</body>
</html>
