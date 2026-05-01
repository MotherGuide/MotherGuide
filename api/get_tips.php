<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "mark24", "motherguidedb");

if ($conn->connect_error) {
    echo json_encode(["status" => "error"]);
    exit;
}

$result = $conn->query("SELECT * FROM tips ORDER BY id DESC");

$tips = [];

while ($row = $result->fetch_assoc()) {
    $tips[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $tips
]);

$conn->close();