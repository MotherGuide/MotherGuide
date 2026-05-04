<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../php/Database.php';
require_once __DIR__ . '/../php/User.php';

date_default_timezone_set('Africa/Kampala');

// SECURITY: Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// VALIDATION: Collect and sanitize input
// NOTE: auth.html sends "first-name" + "last-name" separately.
//       auth-2.html sends a single "full-name".
//       We handle both cases here so either frontend works.
$full_name = '';
if (!empty($_POST['full-name'])) {
    $full_name = trim($_POST['full-name']);
} elseif (!empty($_POST['first-name'])) {
    $first = trim($_POST['first-name']);
    $last  = trim($_POST['last-name'] ?? '');
    $full_name = $last ? "$first $last" : $first;
}

$email          = isset($_POST['email'])           ? trim($_POST['email'])          : '';
$password       = isset($_POST['password'])        ? $_POST['password']             : '';
$pregnancy_week = isset($_POST['pregnancy-week'])  ? (int)$_POST['pregnancy-week'] : 0;

if (empty($full_name) || empty($email) || empty($password) || $pregnancy_week === 0) {
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

if ($pregnancy_week < 1 || $pregnancy_week > 40) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Pregnancy week must be between 1 and 40']);
    exit;
}

$db   = new Database();
$conn = $db->connect();

$user                 = new User($conn);
$user->name           = $full_name;
$user->email          = $email;
// SECURITY: Hash with bcrypt before storing
$user->password       = password_hash($password, PASSWORD_DEFAULT);
$user->sign_up_day    = date('l');
$user->sign_up_date   = date('Y-m-d');
$user->pregnancy_week = $pregnancy_week;

if ($user->emailExists()) {
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
    exit;
}

$user->id = $user->generateId();

if ($user->create()) {
    // FIX: After successful registration, log the user in immediately
    // so they land on tips.php with a valid session instead of being
    // redirected to login again.
    $user->login($email, $password);

    http_response_code(201);
    echo json_encode(['status' => 'success', 'message' => 'Registration successful!']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Registration failed. Please try again.']);
}
?>
