<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $data['action'] ?? '';

try {
    if ($action === 'login') {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $res = loginUser($email, $password);
        if (!$res['ok']) {
            echo json_encode(['success'=>false,'message'=>$res['msg']]);
            exit();
        }
        $redirect = ($res['user_type'] === 'admin') ? url('admin/index.php') : null;
        echo json_encode([
            'success' => true,
            'message' => 'Welcome back, ' . $res['username'] . '!',
            'user_type' => $res['user_type'],
            'redirect' => $redirect
        ]);
        exit();
    }

    if ($action === 'signup') {
        $username = $data['username'] ?? '';
        $email    = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $confirm  = $data['confirm_password'] ?? '';

        if ($password !== $confirm) {
            echo json_encode(['success'=>false,'message'=>'Passwords do not match']);
            exit();
        }

        $res = registerUser($username, $email, $password);
        if (!$res['ok']) {
            echo json_encode(['success'=>false,'message'=>$res['msg']]);
            exit();
        }

        $login = loginUser($email, $password);
        if (!$login['ok']) {
            echo json_encode(['success'=>true,'message'=>'Account created. Please log in.']);
            exit();
        }

        echo json_encode([
            'success' => true,
            'message' => 'Welcome to Food Hub, ' . $login['username'] . '!',
            'user_type' => 'customer',
            'redirect' => null
        ]);
        exit();
    }

    echo json_encode(['success'=>false,'message'=>'Unknown action']);
} catch (Throwable $e) {
    echo json_encode(['success'=>false,'message'=>'Server error. Please try again.']);
}