<?php

require_once("./php/Database.php");
require_once("./php/User.php");

date_default_timezone_set("Africa/Kampala");

$current_date = date("Y-m-d");
$current_day  = date("l");

$db = new Database();
$conn = $db->connect();

$response = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user = new User($conn);

    $user->name = $_POST["full-name"];
    $user->email = $_POST["email"];
    $user->password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $user->sign_up_day = $current_day;
    $user->sign_up_date = $current_date;
    $user->pregnancy_week = $_POST["pregnancy-week"];

    if ($user->emailExists()) {
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
        exit;
    }

    $user->id = $user->generateId();

    if ($user->create()) {
        $response["status"] = "success";
        $response["message"] = "Registration successful!";
    } else {
        $response["status"] = "error";
        $response["message"] = "Something went wrong.";
    }
}

header('Content-Type: application/json');
echo json_encode($response);

?>