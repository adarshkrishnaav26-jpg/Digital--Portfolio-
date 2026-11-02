<?php
$studentId = (int)$_SESSION['student_id'];

$stmt = $db->prepare("SELECT COUNT(*) FROM achievements WHERE student_id=?");
$stmt->execute([$studentId]);
$total = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM achievements WHERE student_id=? AND status='Verified'");
$stmt->execute([$studentId]);
$verified = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM achievements WHERE student_id=? AND status='Pending'");
$stmt->execute([$studentId]);
$pending = $stmt->fetchColumn();
?>

<section class="panel">
    <h2>My Portfolio Statistics</h2>
    <div class="stats-container">
        <div class="achievement">
            <h3>Total Achievements</h3>
            <p><?= (int)$total ?></p>
        </div>
        <div class="achievement">
            <h3>Verified Badges</h3>
            <p><?= (int)$verified ?></p>
        </div>
        <div class="achievement">
            <h3>Pending Claims</h3>
            <p><?= (int)$pending ?></p>
        </div>
    </div>
<?php include __DIR__ . "/profile_views_stat.php"; ?>
</section>
