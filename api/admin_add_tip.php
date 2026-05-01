<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "mark24", "motherguidedb");

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => $conn->connect_error
    ]);
    exit;
}

// Get form data
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$pregnancy_week = $_POST['pregnancy_week'] ?? '';

// Validate
if (!$title || !$content || !$pregnancy_week) {
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required"
    ]);
    exit;
}

// Generate ID (5 chars)
$id = substr(uniqid(), -5);

// Insert
$stmt = $conn->prepare("
    INSERT INTO tips (id, title, content, pregnancy_week)
    VALUES (?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("sssi", $id, $title, $content, $pregnancy_week);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "id" => $id
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();