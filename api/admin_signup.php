<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../php/Database.php';
require_once __DIR__ . '/../php/Admin.php';

date_default_timezone_set('Africa/Kampala');

// SECURITY: Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// VALIDATION: Collect and sanitize input
$full_name = isset($_POST['full-name']) ? trim($_POST['full-name']) : '';
$email     = isset($_POST['email'])     ? trim($_POST['email'])     : '';
$password  = isset($_POST['password'])  ? $_POST['password']        : '';

if (empty($full_name) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters']);
    exit;
}

$db   = new Database();
$conn = $db->connect();

$admin        = new Admin($conn);
$admin->name  = $full_name;
$admin->email = $email;
// SECURITY: Hash password with bcrypt before storing
$admin->password      = password_hash($password, PASSWORD_DEFAULT);
$admin->sign_up_day   = date('l');
$admin->sign_up_date  = date('Y-m-d');

if ($admin->emailExists()) {
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
    exit;
}

$admin->id = $admin->generateId();

if ($admin->create()) {
    http_response_code(201);
    echo json_encode(['status' => 'success', 'message' => 'Admin account created successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Registration failed. Please try again.']);
}
?>
