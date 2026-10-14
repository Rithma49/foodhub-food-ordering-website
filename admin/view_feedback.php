<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message = '';
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $d = $conn->prepare("DELETE FROM feedback WHERE id=?");
    $d->bind_param('i', $id);
    if ($d->execute()) $message = 'Feedback deleted.';
}

$fb = $conn->query("SELECT f.*, u.username, DATE_FORMAT(f.created_at,'%M %d, %Y') AS d FROM feedback f JOIN users u ON f.user_id=u.id ORDER BY f.created_at DESC");
$avg = $conn->query("SELECT COALESCE(AVG(rating),0) a FROM feedback")->fetch_assoc()['a'];
$total = $conn->query("SELECT COUNT(*) c FROM feedback")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Feedback · Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head><body>
<div class="admin-wrap">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <h1>Customer Feedback</h1>
        <p class="lead">Review and manage customer reviews</p>
        <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

        <div class="stats">
            <div class="stat amber"><span class="ic"><i class="fa-solid fa-star"></i></span><h3><?= round($avg, 1) ?>/5</h3><p>Average Rating</p></div>
            <div class="stat blue"><span class="ic"><i class="fa-solid fa-comments"></i></span><h3><?= $total ?></h3><p>Total Feedback</p></div>
        </div>

        <div class="admin-card">
            <h3><i class="fa-solid fa-comments"></i> All Feedback</h3>
            <?php if ($fb->num_rows): while ($f = $fb->fetch_assoc()): ?>
                <div class="fb-card mb-2">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap">
                        <div class="fb-head">
                            <div class="avatar"><?= avatarInitial($f['username']) ?></div>
                            <div>
                                <div class="fb-name"><?= e($f['username']) ?></div>
                                <?= renderStars((int)$f['rating']) ?>
                            </div>
                        </div>
                        <div style="text-align:right">
                            <div class="fb-date"><?= e($f['d']) ?></div>
                            <a href="?delete_id=<?= $f['id'] ?>" onclick="return confirm('Delete?')" style="color:var(--danger);font-size:.82rem;margin-top:6px;display:inline-block"><i class="fa-solid fa-trash"></i> Delete</a>
                        </div>
                    </div>
                    <p class="fb-comment mt-2"><?= nl2br(e($f['comment'])) ?></p>
                </div>
            <?php endwhile; else: ?>
                <div class="empty"><i class="fa-solid fa-comments"></i><p>No feedback yet.</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body></html>