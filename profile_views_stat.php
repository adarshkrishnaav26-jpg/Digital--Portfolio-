<?php
$viewQuery = $db->prepare(
    "SELECT COUNT(*) FROM profile_views WHERE student_id=?"
);
$viewQuery->execute([$_SESSION['student_id']]);
$profileViews = $viewQuery->fetchColumn();
?>

<div class="achievement">
    <h3>Profile Views</h3>
    <p><?= (int)$profileViews ?></p>
</div>
