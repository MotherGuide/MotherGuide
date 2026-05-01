<?php
session_start();
require_once '../php/Database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Login required']);
    exit;
}

$db = (new Database())->connect();
$user_id = $_SESSION['user_id'];
$tip_id = $_POST['tip_id'];
$type = $_POST['type']; // 'like' or 'dislike'

// Check if already exists
$check = $db->prepare("SELECT id, type FROM likes WHERE user_id = ? AND tip_id = ?");
$check->bind_param("si", $user_id, $tip_id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();

if ($existing) {
    if ($existing['type'] === $type) {
        // Toggle off if clicking the same button
        $stmt = $db->prepare("DELETE FROM likes WHERE id = ?");
        $stmt->bind_param("i", $existing['id']);
    } else {
        // Change from like to dislike or vice-versa
        $stmt = $db->prepare("UPDATE likes SET type = ? WHERE id = ?");
        $stmt->bind_param("si", $type, $existing['id']);
    }
} else {
    $stmt = $db->prepare("INSERT INTO likes (user_id, tip_id, type) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $user_id, $tip_id, $type);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Action failed']);
}
?>