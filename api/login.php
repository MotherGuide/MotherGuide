<?php
require_once("./php/Database.php");
require_once("./php/User.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

$user = new User($conn);
$response = $user->login($_POST["email"], $_POST["password"]);

header("Content-Type: application/json");
echo json_encode($response);
?>