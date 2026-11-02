<?php
require 'db.php';

$slug = $_GET['slug'] ?? '';

$stmt = $db->prepare("SELECT * FROM students WHERE public_slug=?");
$stmt->execute([$slug]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    http_response_code(404);
    exit("Portfolio not found.");
}

$view = $db->prepare("INSERT INTO profile_views (student_id) VALUES (?)");
$view->execute([$student['id']]);

$stmt = $db->prepare(
    "SELECT * FROM achievements WHERE student_id=? AND status='Verified' ORDER BY id DESC"
);
$stmt->execute([$student['id']]);
$achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($student['name']) ?> - Portfolio</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
<a class="brand" href="index.php">Digital Portfolio</a>
</header>

<main class="content">
<section class="panel">
<h1><?= htmlspecialchars($student['name']) ?></h1>
<p><?= htmlspecialchars($student['degree'] ?? '') ?></p>
<p><?= htmlspecialchars($student['department'] ?? '') ?></p>
<p><?= nl2br(htmlspecialchars($student['bio'] ?? '')) ?></p>
</section>

<section class="panel">
<h2>Verified Achievements</h2>
<?php if (!$achievements): ?>
<p>No verified achievements available.</p>
<?php else: ?>
<?php foreach ($achievements as $a): ?>
<article class="achievement">
<h3><?= htmlspecialchars($a['title']) ?></h3>
<p><?= nl2br(htmlspecialchars($a['description'] ?? '')) ?></p>
</article>
<?php endforeach; ?>
<?php endif; ?>
</section>
</main>
</body>
</html>
