<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

function isLoggedIn(): bool { return isset($_SESSION['user_id']); }
function isAdmin(): bool { return ($_SESSION['user_type'] ?? '') === 'admin'; }

function requireLogin(?string $redirect = null): void {
    if (isLoggedIn()) return;
    $r = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: ' . url('login.php') . $r);
    exit();
}

function requireAdmin(): void {
    if (!isAdmin()) { header('Location: ' . url('login.php')); exit(); }
}

function registerUser(string $u, string $e, string $p): array {
    global $conn;
    $u = trim($u); $e = trim($e);

    if (strlen($u) < 3) return ['ok'=>false,'msg'=>'Username must be at least 3 characters'];
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $u)) return ['ok'=>false,'msg'=>'Username can only contain letters, numbers and underscore'];
    if (!filter_var($e, FILTER_VALIDATE_EMAIL)) return ['ok'=>false,'msg'=>'Invalid email format'];
    if (strlen($p) < 6) return ['ok'=>false,'msg'=>'Password must be at least 6 characters'];

    $chk = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $chk->bind_param('ss', $e, $u);
    $chk->execute();
    if ($chk->get_result()->num_rows > 0) return ['ok'=>false,'msg'=>'Email or username already exists'];

    $hash = password_hash($p, PASSWORD_DEFAULT);
    $s = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $s->bind_param('sss', $u, $e, $hash);
    if (!$s->execute()) return ['ok'=>false,'msg'=>'Registration failed. Please try again.'];

    return ['ok'=>true, 'user_id'=>$conn->insert_id];
}

function loginUser(string $email, string $password): array {
    global $conn;
    $email = trim($email);
    if (!$email || !$password) return ['ok'=>false,'msg'=>'Please fill in all fields'];

    $s = $conn->prepare("SELECT id, username, email, password, user_type FROM users WHERE email = ?");
    $s->bind_param('s', $email);
    $s->execute();
    $r = $s->get_result();
    if ($r->num_rows !== 1) return ['ok'=>false,'msg'=>'Invalid email or password'];
    $u = $r->fetch_assoc();
    if (!password_verify($password, $u['password'])) return ['ok'=>false,'msg'=>'Invalid email or password'];

    $_SESSION['user_id']   = (int)$u['id'];
    $_SESSION['username']  = $u['username'];
    $_SESSION['email']     = $u['email'];
    $_SESSION['user_type'] = $u['user_type'];

    return ['ok'=>true, 'user_type'=>$u['user_type'], 'username'=>$u['username']];
}