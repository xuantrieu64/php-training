<?php
header('Content-Type: application/json');

require_once 'models/UserModel.php';
$userModel = new UserModel();

// Lấy dữ liệu JSON từ client
$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

// Kiểm tra login bằng UserModel
$user = $userModel->auth($username, $password);

if ($user) {
    // Tạo token giả (hoặc JWT trong thực tế)
    $token = base64_encode($username . ':' . time());
    echo json_encode([
        'success' => true,
        'username' => $user[0]['username'], // hoặc name tùy model
        'token' => $token
    ]);
} else {
    echo json_encode(['success' => false]);
}