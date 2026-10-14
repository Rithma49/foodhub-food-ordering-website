<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
$pageTitle = 'Feedback';
$error = ''; $success = '';

if (isset($_POST['submit_feedback'])) {
    if (!isLoggedIn()) $error = 'Please sign in to submit feedback!';
    else {
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        if (!$comment) $error = 'Please enter your feedback!';
        elseif ($rating < 1 || $rating > 5) $error = 'Please select a valid rating!';
        else {
            $s = $conn->prepare("INSERT INTO feedback (user_id, rating, comment) VALUES (?, ?, ?)");
            $s->bind_param('iis', $_SESSION['user_id'], $rating, $comment);
            if ($s->execute()) { $success = 'Thank you for your feedback!'; $_POST = []; }
            else $error = 'Error submitting. Try again.';
        }
    }
}
include 'includes/header.php';
?>
<div class="container" style="padding-top:36px">
    <div class="sec-title">
        <span class="eyebrow">Share Your Voice</span>
        <h2>Customer Feedback</h2>
        <p>Share your experience with us</p>
    </div>

    <div class="fb-layout" style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
        <div class="panel">
            <div class="panel-title"><i class="fa-solid fa-pen-to-square"></i> Share Your Feedback</div>
            <?php if ($error): ?><div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= e($success) ?></div><?php endif; ?>

            <?php if (!isLoggedIn()): ?>
                <div class="guest-notice" style="margin-bottom:18px">
                    <div class="guest-notice-icon"><i class="fa-solid fa-circle-info"></i></div>
                    <div class="guest-notice-body"><h4>Sign in required</h4><p>Please sign in to share your feedback.</p></div>
                    <div class="guest-notice-actions">
                        <button class="btn btn-primary btn-sm" data-open-auth="login"><i class="fa-solid fa-right-to-bracket"></i> Sign In</button>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="field">
                    <label><i class="fa-solid fa-star"></i> Rating</label>
                    <div class="star-picker">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label>
                                <input type="radio" name="rating" value="<?= $i ?>" <?= (isset($_POST['rating']) && $_POST['rating'] == $i) ? 'checked' : '' ?> <?= !isLoggedIn() ? 'disabled' : '' ?>>
                                <i class="fa-regular fa-star"></i>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="field">
                    <label for="comment"><i class="fa-solid fa-comment"></i> Your Feedback</label>
                    <textarea id="comment" name="comment" class="input" rows="5" <?= !isLoggedIn() ? 'disabled' : '' ?> required><?= e($_POST['comment'] ?? '') ?></textarea>
                </div>
                <?php if (isLoggedIn()): ?>
                    <button type="submit" name="submit_feedback" class="btn btn-primary btn-block"><i class="fa-solid fa-paper-plane"></i> Submit Feedback</button>
                <?php else: ?>
                    <button type="button" class="btn btn-primary btn-block" data-open-auth="login"><i class="fa-solid fa-right-to-bracket"></i> Sign In to Submit</button>
                <?php endif; ?>
            </form>
        </div>

        <div>
            <div class="panel-title"><i class="fa-solid fa-comments"></i> Recent Feedback</div>
            <?php
            $fb = $conn->query("SELECT f.*, u.username, DATE_FORMAT(f.created_at,'%M %d, %Y') AS d FROM feedback f JOIN users u ON f.user_id=u.id ORDER BY f.created_at DESC LIMIT 5");
            if ($fb && $fb->num_rows):
                while ($r = $fb->fetch_assoc()):
            ?>
                <div class="fb-card mb-2">
                    <div class="fb-head">
                        <div class="avatar"><?= avatarInitial($r['username']) ?></div>
                        <div style="flex:1">
                            <div class="fb-name"><?= e($r['username']) ?></div>
                            <?= renderStars((int)$r['rating']) ?>
                        </div>
                        <div class="fb-date"><?= e($r['d']) ?></div>
                    </div>
                    <p class="fb-comment">"<?= e($r['comment']) ?>"</p>
                </div>
            <?php endwhile; else: ?>
                <div class="panel empty"><i class="fa-solid fa-comments"></i><p>No feedback yet. Be the first!</p></div>
            <?php endif; ?>
            <div class="center mt-3">
                <a href="<?= url('feedback_all.php') ?>" class="btn btn-outline"><i class="fa-solid fa-list"></i> View All Feedback</a>
            </div>
        </div>
    </div>
</div>
<style>@media(max-width:900px){.fb-layout{grid-template-columns:1fr!important}}</style>
<?php include 'includes/footer.php'; ?>