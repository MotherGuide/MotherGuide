<?php
require_once("../php/Database.php");
require_once("../php/User.php");

// SECURITY: Validate HTTP method - only POST requests are allowed for authentication
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

// DATA VALIDATION: Sanitize email input and validate presence of credentials
$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
$password = isset($_POST["password"]) ? $_POST["password"] : '';

// SECURITY: Provide consistent error messages to prevent user enumeration
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Email and password are required"]);
    exit;
}

$user = new User($conn);
$response = $user->login($email, $password);

// RESPONSE: Set appropriate content-type header and return JSON response
header("Content-Type: application/json; charset=utf-8");
echo json_encode($response);
?>