<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Home';
$user = currentUser();
$cur = currentPage();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#E85D04">
<title><?= e($pageTitle) ?> · <?= APP_NAME ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>

<div class="topbar">
    <div class="topbar-inner">
        <span><i class="fa-solid fa-truck-fast"></i> Free delivery over Rs. 3,000</span>
        <span class="hide-sm"><i class="fa-solid fa-clock"></i> Open daily 10AM – 10PM</span>
        <a href="<?= url('contact.php') ?>"><i class="fa-solid fa-headset"></i> Support</a>
    </div>
</div>

<header class="nav" id="nav">
    <div class="nav-wrap">
        <a href="<?= url('index.php') ?>" class="brand">
            <span class="brand-mark"><i class="fa-solid fa-utensils"></i></span>
            <span class="brand-text">Food<em>Hub</em></span>
        </a>

        <ul class="nav-menu" id="navMenu">
            <li><a href="<?= url('index.php') ?>" class="<?= $cur==='index.php'?'on':'' ?>"><i class="fa-solid fa-house"></i> Home</a></li>
            <li><a href="<?= url('menu.php') ?>" class="<?= $cur==='menu.php'?'on':'' ?>"><i class="fa-solid fa-book-open"></i> Menu</a></li>
            <li><a href="<?= url('about.php') ?>" class="<?= $cur==='about.php'?'on':'' ?>"><i class="fa-solid fa-circle-info"></i> About</a></li>
            <li><a href="<?= url('contact.php') ?>" class="<?= $cur==='contact.php'?'on':'' ?>"><i class="fa-solid fa-envelope"></i> Contact</a></li>
            <li><a href="<?= url('feedback.php') ?>" class="<?= $cur==='feedback.php'?'on':'' ?>"><i class="fa-solid fa-star"></i> Reviews</a></li>
            <?php if ($user): ?>
                <li><a href="<?= url('order.php') ?>" class="<?= $cur==='order.php'?'on':'' ?>"><i class="fa-solid fa-receipt"></i> My Orders</a></li>
            <?php endif; ?>
        </ul>

        <div class="nav-actions">
            <button class="icon-btn" id="themeToggle" aria-label="Toggle theme">
                <i class="fa-solid fa-moon"></i>
            </button>

            <button class="icon-btn cart-btn" id="cartToggle" aria-label="Cart">
                <i class="fa-solid fa-bag-shopping"></i>
                <span class="cart-count">0</span>
            </button>

            <?php if ($user): ?>
                <div class="user-dd">
                    <button class="user-chip">
                        <span class="avatar sm"><?= avatarInitial($user['username']) ?></span>
                        <span class="hide-sm"><?= e($user['username']) ?></span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="dd-panel">
                        <?php if (currentIsAdmin()): ?>
                            <a href="<?= url('admin/index.php') ?>"><i class="fa-solid fa-gauge"></i> Admin Panel</a>
                        <?php endif; ?>
                        <a href="<?= url('order.php') ?>"><i class="fa-solid fa-receipt"></i> My Orders</a>
                        <a href="<?= url('feedback.php') ?>"><i class="fa-solid fa-star"></i> Reviews</a>
                        <a href="<?= url('logout.php') ?>" class="dd-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <button class="btn btn-primary" id="openAuth" aria-label="Sign in">
                    <i class="fa-solid fa-user"></i>
                    <span class="hide-sm">Sign In</span>
                </button>
            <?php endif; ?>

            <button class="menu-toggle" id="menuToggle" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<?php if (!$user): ?>
<div class="modal" id="authModal" role="dialog" aria-modal="true">
    <div class="modal-box auth-modal-box">
        <button class="icon-btn modal-close" id="authClose" aria-label="Close">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="auth-header">
            <div class="auth-logo">
                <span class="brand-mark"><i class="fa-solid fa-utensils"></i></span>
            </div>
            <h2 id="authTitle">Welcome to Food Hub</h2>
            <p id="authSubtitle">Sign in to your account or create a new one</p>
        </div>

        <div class="auth-tabs" role="tablist">
            <button class="auth-tab active" data-tab="login" role="tab">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>
            <button class="auth-tab" data-tab="signup" role="tab">
                <i class="fa-solid fa-user-plus"></i> Sign Up
            </button>
            <span class="auth-tab-indicator" id="authIndicator"></span>
        </div>

        <div class="auth-alert" id="authAlert"></div>

        <form class="auth-form active" id="loginForm" data-form="login" autocomplete="on">
            <div class="field">
                <label for="login-email"><i class="fa-solid fa-envelope"></i> Email Address</label>
                <input type="email" id="login-email" name="email" class="input" placeholder="you@example.com" required autocomplete="email">
            </div>
            <div class="field">
                <label for="login-password"><i class="fa-solid fa-lock"></i> Password</label>
                <div class="input-wrap">
                    <input type="password" id="login-password" name="password" class="input" placeholder="Enter your password" required autocomplete="current-password">
                    <button type="button" class="pw-toggle" data-target="login-password" aria-label="Show password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In
            </button>
            <p class="auth-switch">Don't have an account? <a href="#" data-switch="signup">Create one</a></p>
        </form>

        <form class="auth-form" id="signupForm" data-form="signup" autocomplete="on">
            <div class="field">
                <label for="signup-username"><i class="fa-solid fa-user"></i> Username</label>
                <input type="text" id="signup-username" name="username" class="input" placeholder="Choose a username" required minlength="3" autocomplete="username">
            </div>
            <div class="field">
                <label for="signup-email"><i class="fa-solid fa-envelope"></i> Email Address</label>
                <input type="email" id="signup-email" name="email" class="input" placeholder="you@example.com" required autocomplete="email">
            </div>
            <div class="field">
                <label for="signup-password"><i class="fa-solid fa-lock"></i> Password</label>
                <div class="input-wrap">
                    <input type="password" id="signup-password" name="password" class="input" placeholder="At least 6 characters" required minlength="6" autocomplete="new-password">
                    <button type="button" class="pw-toggle" data-target="signup-password" aria-label="Show password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="field">
                <label for="signup-confirm"><i class="fa-solid fa-lock"></i> Confirm Password</label>
                <div class="input-wrap">
                    <input type="password" id="signup-confirm" name="confirm_password" class="input" placeholder="Repeat your password" required minlength="6" autocomplete="new-password">
                    <button type="button" class="pw-toggle" data-target="signup-confirm" aria-label="Show password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fa-solid fa-user-plus"></i> Create Account
            </button>
            <p class="auth-switch">Already have an account? <a href="#" data-switch="login">Sign in</a></p>
        </form>

        <p class="auth-footer">
            <i class="fa-solid fa-shield-halved"></i>
            By continuing you agree to our Terms & Privacy Policy
        </p>
    </div>
</div>
<?php endif; ?>

<aside class="drawer" id="cartDrawer">
    <div class="drawer-head">
        <h3><i class="fa-solid fa-bag-shopping"></i> Your Cart</h3>
        <button class="icon-btn" id="cartClose"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="drawer-body" id="cartBody"></div>
    <div class="drawer-foot" id="cartFoot"></div>
</aside>
<div class="overlay" id="overlay"></div>

<button class="fab-top" id="fabTop" aria-label="Back to top">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<main>