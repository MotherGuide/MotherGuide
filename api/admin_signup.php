<?php

require_once(__DIR__ . '/../php/Database.php');
require_once(__DIR__ . '/../php/Admin.php');

date_default_timezone_set("Africa/Kampala");

$current_date = date("Y-m-d");
$current_day  = date("l");

$db = new Database();
$conn = $db->connect();

$response = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $admin = new Admin($conn);

    $admin->name = $_POST["full-name"];
    $admin->email = $_POST["email"];
    $admin->password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $admin->sign_up_day = $current_day;
    $admin->sign_up_date = $current_date;

    if ($admin->emailExists()) {
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
        exit;
    }

    $admin->id = $admin->generateId();

    if ($admin->create()) {
        $response["status"] = "success";
        $response["message"] = "Admin registration successful!";
    } else {
        $response["status"] = "error";
        $response["message"] = "Something went wrong.";
    }
}

header('Content-Type: application/json');
echo json_encode($response);

?>
