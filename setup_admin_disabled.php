<?php
require 'db.php';

$message = '';

$count = $db->query("SELECT COUNT(*) FROM admins")->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $count == 0) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || strlen($password) < 8) {
        $message = "Enter a username and password of at least 8 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare(
            "INSERT INTO admins (username, password) VALUES (?, ?)"
        );
        $stmt->execute([$username, $hash]);

        $message = "Admin account created successfully!";
        $count = 1;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Setup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="content">
<section class="panel">
<h1>Admin Account Setup</h1>

<p><?= htmlspecialchars($message) ?></p>

<?php if ($count == 0): ?>
<form method="POST">
    <label>Admin Username</label>
    <input type="text" name="username" required>

    <label>Admin Password</label>
    <input type="password" name="password" minlength="8" required>

    <button type="submit">Create Admin</button>
</form>
<?php else: ?>
<p>An admin account already exists. Setup is closed.</p>
<?php endif; ?>

</section>
</main>
</body>
</html>
