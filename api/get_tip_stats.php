<?php
header('Content-Type: application/json');

require_once '../php/Database.php';
require_once '../php/Tip.php';

$db = new Database();
$conn = $db->connect();

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$tipId = $_GET['tip_id'] ?? '';

if (empty($tipId)) {
    echo json_encode(["status" => "error", "message" => "Tip ID is required"]);
    exit;
}

$tipModel = new Tip($conn);
$stats = $tipModel->getStats($tipId);

echo json_encode([
    "status" => "success",
    "likes" => $stats['likes'],
    "dislikes" => $stats['dislikes'],
    "comments" => $stats['comments']
]);

$conn->close();
?>
