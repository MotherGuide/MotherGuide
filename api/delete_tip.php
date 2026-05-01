<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "mark24", "motherguidedb");

$id = $_POST['id'] ?? '';

$stmt = $conn->prepare("DELETE FROM tips WHERE id=?");
$stmt->bind_param("s", $id);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success"]);
} else {
    echo json_encode(["status"=>"error"]);
    
}