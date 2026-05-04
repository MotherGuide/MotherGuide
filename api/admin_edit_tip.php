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

// SECURITY: Require active admin session
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Admin access required']);
    exit;
}

// REMOVE in production: debug flags should never be on in a live API endpoint
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$db   = new Database();
$conn = $db->connect();

// VALIDATION: Collect and sanitize input
$tip_id         = isset($_POST['tip_id'])         ? trim($_POST['tip_id'])         : '';
$title          = isset($_POST['title'])          ? trim($_POST['title'])          : '';
$content        = isset($_POST['content'])        ? trim($_POST['content'])        : '';
$pregnancy_week = isset($_POST['pregnancy_week']) ? (int)$_POST['pregnancy_week'] : 0;

if (empty($tip_id) || empty($title) || empty($content) || $pregnancy_week === 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// VALIDATION: title length
if (strlen($title) < 3 || strlen($title) > 255) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Title must be between 3 and 255 characters']);
    exit;
}

// VALIDATION: content minimum length
if (strlen($content) < 10) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Content must be at least 10 characters']);
    exit;
}

// VALIDATION: week range
if ($pregnancy_week < 1 || $pregnancy_week > 40) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Pregnancy week must be between 1 and 40']);
    exit;
}

// FIX: bind_param was "ssis" but pregnancy_week is cast to int, so correct binding is "ssis"
// tip_id is a string ID like "T0001", so the last param is "s" — correct as-is
$stmt = $conn->prepare("UPDATE tips SET title = ?, content = ?, pregnancy_week = ? WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ssis", $title, $content, $pregnancy_week, $tip_id);

if ($stmt->execute()) {
    // Check if any row was actually found/updated
    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Tip not found']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Tip updated successfully']);
    }
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
