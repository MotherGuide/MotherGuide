<?php
require_once("../php/Database.php");
require_once("../php/Admin.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

$admin = new Admin($conn);
$response = $admin->login($_POST["email"], $_POST["password"]);

header("Content-Type: application/json");
echo json_encode($response);
?>
