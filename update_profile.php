<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

$studentId = (int) $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $degree = trim($_POST['degree'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $year = trim($_POST['graduation_year'] ?? '');

    if ($name === '') {
        exit('Student name is required.');
    }

    $slug = 'student-' . $studentId;

    $q = $db->prepare(
        'UPDATE students
         SET name=?, bio=?, degree=?, department=?,
             graduation_year=?, public_slug=?
         WHERE id=?'
    );

    $q->execute([
        $name,
        $bio,
        $degree,
        $department,
        $year !== '' ? (int)$year : null,
        $slug,
        $studentId
    ]);

    $_SESSION['student_name'] = $name;

    header('Location: dashboard.php?profile=updated');
    exit;
}

$q = $db->prepare('SELECT * FROM students WHERE id=?');
$q->execute([$studentId]);
$student = $q->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    exit('Student profile not found.');
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Student Profile</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<main class="content">
<section class="panel">
<h1>Edit Student Profile</h1>

<form method="post">
<label>Full Name</label>
<input type="text" name="name"
value="<?=htmlspecialchars($student['name'])?>" required>

<label>Bio</label>
<textarea name="bio" rows="4"><?=htmlspecialchars($student['bio'] ?? '')?></textarea>

<label>Degree</label>
<input type="text" name="degree"
value="<?=htmlspecialchars($student['degree'] ?? '')?>"
placeholder="B.Sc. Computer Science">

<label>Department</label>
<input type="text" name="department"
value="<?=htmlspecialchars($student['department'] ?? '')?>"
placeholder="Computer Science">

<label>Graduation Year</label>
<input type="number" name="graduation_year"
value="<?=htmlspecialchars((string)($student['graduation_year'] ?? ''))?>"
min="2000" max="2100">

<button class="btn" type="submit">Save Profile</button>
</form>

<p><a href="dashboard.php">Back to Dashboard</a></p>
</section>
</main>

</body>
</html>
