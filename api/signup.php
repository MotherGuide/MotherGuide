<?php

require_once(__DIR__ . '/../php/Database.php');
require_once(__DIR__ . '/../php/User.php');

// CONFIGURATION: Set timezone for consistent date-time handling across the application
date_default_timezone_set("Africa/Kampala");

// SECURITY: Only process POST requests to prevent unauthorized access via GET or other methods
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$current_date = date("Y-m-d");
$current_day  = date("l");

$db = new Database();
$conn = $db->connect();

$response = [];

    // DATA COLLECTION: Retrieve and sanitize user input from registration form
    $full_name = isset($_POST["full-name"]) ? trim($_POST["full-name"]) : '';
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $password = isset($_POST["password"]) ? $_POST["password"] : '';
    $pregnancy_week = isset($_POST["pregnancy-week"]) ? (int)$_POST["pregnancy-week"] : 0;
    
    // VALIDATION: Ensure all required fields are provided
    if (empty($full_name) || empty($email) || empty($password) || $pregnancy_week === 0) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }
    
    // VALIDATION: Verify email format before database operations
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid email format"]);
        exit;
    }
    
    // VALIDATION: Ensure password meets minimum security requirements
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Password must be at least 6 characters"]);
        exit;
    }
    
    // VALIDATION: Verify pregnancy week is within valid range (1-40 weeks)
    if ($pregnancy_week < 1 || $pregnancy_week > 40) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Pregnancy week must be between 1 and 40"]);
        exit;
    }

    $user = new User($conn);

    $user->name = $full_name;
    $user->email = $email;
    // SECURITY: Use bcrypt hashing for secure password storage (PASSWORD_DEFAULT uses bcrypt)
    $user->password = password_hash($password, PASSWORD_DEFAULT);
    $user->sign_up_day = $current_day;
    $user->sign_up_date = $current_date;
    $user->pregnancy_week = $pregnancy_week;

    // VALIDATION: Check for duplicate email to prevent account duplication
    if ($user->emailExists()) {
        http_response_code(409);
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
        exit;
    }

    // GENERATE: Create unique user identifier
    $user->id = $user->generateId();

    // CREATE: Persist user record to database
    if ($user->create()) {
        http_response_code(201);
        $response["status"] = "success";
        $response["message"] = "Registration successful!";
    } else {
        http_response_code(500);
        $response["status"] = "error";
        $response["message"] = "Registration failed. Please try again.";
    }

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);

?>