<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
$pageTitle = 'Contact';
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if (!$name || !$email || !$subject || !$message) $error = 'Please fill in all fields!';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Invalid email format!';
    else { $success = 'Thank you for your message! We will get back to you soon.'; $_POST = []; }
}
include 'includes/header.php';
?>
<div class="container" style="padding-top:36px">
    <div class="sec-title">
        <span class="eyebrow">Say Hello</span>
        <h2>Contact Us</h2>
        <p>We'd love to hear from you</p>
    </div>

    <div class="contact-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
        <div class="panel">
            <div class="panel-title"><i class="fa-solid fa-paper-plane"></i> Send a Message</div>
            <?php if ($error): ?><div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= e($success) ?></div><?php endif; ?>

            <form method="POST">
                <div class="field"><label><i class="fa-solid fa-user"></i> Your Name</label><input type="text" name="name" class="input" value="<?= e($_POST['name'] ?? '') ?>" required></div>
                <div class="field"><label><i class="fa-solid fa-envelope"></i> Email</label><input type="email" name="email" class="input" value="<?= e($_POST['email'] ?? '') ?>" required></div>
                <div class="field"><label><i class="fa-solid fa-tag"></i> Subject</label><input type="text" name="subject" class="input" value="<?= e($_POST['subject'] ?? '') ?>" required></div>
                <div class="field"><label><i class="fa-solid fa-comment"></i> Message</label><textarea name="message" rows="5" class="input" required><?= e($_POST['message'] ?? '') ?></textarea></div>
                <button type="submit" class="btn btn-primary btn-block"><i class="fa-solid fa-paper-plane"></i> Send Message</button>
            </form>
        </div>

        <div class="panel">
            <div class="panel-title"><i class="fa-solid fa-address-book"></i> Contact Info</div>
            <?php
            $items = [
                ['fa-location-dot','Address','123 Restaurant Street, Colombo 03, Sri Lanka'],
                ['fa-phone','Phone','+94 11 234 5678 / +94 77 123 4567'],
                ['fa-envelope','Email','info@foodhub.com'],
                ['fa-clock','Hours','Mon–Fri · 10AM–10PM · Sat–Sun · 9AM–11PM'],
            ];
            foreach ($items as $it): ?>
                <div style="display:flex;gap:14px;margin-bottom:18px">
                    <div style="width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:var(--primary-soft);color:var(--primary);flex-shrink:0">
                        <i class="fa-solid <?= $it[0] ?>"></i>
                    </div>
                    <div><strong><?= $it[1] ?></strong><p class="muted" style="font-size:.88rem"><?= $it[2] ?></p></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<style>@media(max-width:800px){.contact-grid{grid-template-columns:1fr!important}}</style>
<?php include 'includes/footer.php'; ?>