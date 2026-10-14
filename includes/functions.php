<?php
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function url(string $path = ''): string { return BASE_URL . ltrim($path, '/'); }

function asset(string $path): string {
    $full = __DIR__ . '/../' . ltrim($path, '/');
    $v = file_exists($full) ? filemtime($full) : time();
    return url($path) . '?v=' . $v;
}

function money($n): string { return 'Rs. ' . number_format((float)$n, 2); }

function currentUser(): ?array {
    if (!isset($_SESSION['user_id'])) return null;
    return [
        'id' => (int)$_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'type' => $_SESSION['user_type'] ?? 'customer',
    ];
}

function currentIsAdmin(): bool { return (currentUser()['type'] ?? '') === 'admin'; }

function currentPage(): string { return basename($_SERVER['PHP_SELF']); }

function renderStars(int $rating): string {
    $h = '<span class="stars">';
    for ($i = 1; $i <= 5; $i++) {
        $h .= $i <= $rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
    }
    return $h . '</span>';
}

function avatarInitial(string $name): string {
    return strtoupper(mb_substr(trim($name), 0, 1));
}

function statusIcon(string $s): string {
    return [
        'pending'    => 'fa-clock',
        'processing' => 'fa-spinner',
        'completed'  => 'fa-circle-check',
        'cancelled'  => 'fa-circle-xmark',
    ][$s] ?? 'fa-circle';
}