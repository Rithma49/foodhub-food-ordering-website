<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
$pageTitle = 'All Feedback';
include 'includes/header.php';
?>
<div class="container" style="padding-top:36px">
    <div class="sec-title">
        <span class="eyebrow">All Reviews</span>
        <h2>Customer Feedback</h2>
        <p>What our customers think about us</p>
    </div>

    <div class="fb-grid">
    <?php
    $fb = $conn->query("SELECT f.*, u.username, DATE_FORMAT(f.created_at,'%M %d, %Y') AS d FROM feedback f JOIN users u ON f.user_id=u.id ORDER BY f.created_at DESC");
    if ($fb && $fb->num_rows):
        while ($r = $fb->fetch_assoc()):
    ?>
        <div class="fb-card reveal">
            <div class="fb-head">
                <div class="avatar"><?= avatarInitial($r['username']) ?></div>
                <div style="flex:1">
                    <div class="fb-name"><?= e($r['username']) ?></div>
                    <?= renderStars((int)$r['rating']) ?>
                </div>
            </div>
            <p class="fb-comment">"<?= e($r['comment']) ?>"</p>
            <div class="fb-date mt-2"><?= e($r['d']) ?></div>
        </div>
    <?php endwhile; else: ?>
        <div class="panel empty" style="grid-column:1/-1"><i class="fa-solid fa-comments"></i><p>No feedback yet.</p></div>
    <?php endif; ?>
    </div>

    <div class="center mt-4">
        <a href="<?= url('feedback.php') ?>" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>
</div>
<?php include 'includes/footer.php'; ?>