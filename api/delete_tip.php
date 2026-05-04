<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../php/Database.php';

// SECURITY: Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// SECURITY: Require active admin session — anyone with the URL could delete tips otherwise
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Admin access required']);
    exit;
}

$db   = new Database();
$conn = $db->connect();

$id = isset($_POST['id']) ? trim($_POST['id']) : '';

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Tip ID is required']);
    exit;
}

$stmt = $conn->prepare('DELETE FROM tips WHERE id = ?');
$stmt->bind_param('s', $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Tip not found']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Tip deleted']);
    }
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
