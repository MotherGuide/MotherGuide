<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../php/Database.php';
require_once '../php/User.php';

// SECURITY: Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// VALIDATION: Sanitize and validate input presence
$email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';
$password = isset($_POST['password']) ? $_POST['password']       : '';

// SECURITY: Generic message prevents user enumeration
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
    exit;
}

$db   = new Database();
$conn = $db->connect();

$user     = new User($conn);
$response = $user->login($email, $password);

echo json_encode($response);
?>
