<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

$q = $db->prepare(
    'SELECT * FROM achievements WHERE student_id=? ORDER BY id DESC'
);
$q->execute([$_SESSION['student_id']]);
$items = $q->fetchAll(PDO::FETCH_ASSOC);

$c = $db->prepare(
    'SELECT * FROM credentials WHERE student_id=? ORDER BY id DESC'
);
$c->execute([$_SESSION['student_id']]);
$credentials = $c->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
<a class="brand" href="index.php">🎓 Digital Portfolio</a>
<nav>
<a href="index.php">Home</a>
<a href="add_credential.php">Add Credential</a>
<a href="verify.php">Verify</a>
<a href="logout.php">Logout</a>
</nav>
</header>

<main class="content">
<h1>Welcome, <?=htmlspecialchars($_SESSION['student_name'])?>!</h1>
<p>Manage your portfolio and achievements here.</p>

<section class="panel">
<h2>Add an achievement</h2>
<form method="post" action="save_achievement.php">
<label>Achievement / certificate title</label>
<input name="title" maxlength="150" required>
<label>Description</label>
<textarea name="description" rows="3"></textarea>
<button class="btn">Save achievement</button>
</form>
</section>

<section class="panel">
<h2>My Credentials</h2>
<p><a class="btn" href="add_credential.php">+ Add Credential</a></p>

<?php if (!$credentials): ?>
<p>No credentials added yet.</p>
<?php else: ?>
<?php foreach ($credentials as $cr): ?>
<article class="achievement">
<h3><?=htmlspecialchars($cr['title'])?></h3>
<p>Issued by: <?=htmlspecialchars($cr['issuer'])?></p>
<p>Verification Code:
<p><a href="qr.php?id=<?= (int)$cr["id"] ?>">View QR Code</a></p>
<strong><?=htmlspecialchars($cr['credential_code'])?></strong></p>
<p>Status: <?=htmlspecialchars($cr['status'])?></p>
<a href="verify.php">Verify Credential</a>
</article>
<?php endforeach; ?>
<?php endif; ?>
</section>

<h2>Your achievements</h2>
<?php if (!$items): ?>
<p>No achievements added yet.</p>
<?php else: ?>
<?php foreach ($items as $a): ?>
<article class="achievement">
<h3><?=htmlspecialchars($a['title'])?></h3>
<p><?=nl2br(htmlspecialchars($a['description'] ?? ''))?></p>
<span class="status"><?=htmlspecialchars($a['status'])?></span>
</article>
<?php endforeach; ?>
<?php endif; ?>

</main>
</body>
</html>
