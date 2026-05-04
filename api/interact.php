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

// SECURITY: Require active user session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Login required']);
    exit;
}

// VALIDATION: Collect and validate input
$tip_id = isset($_POST['tip_id']) ? trim($_POST['tip_id']) : '';
$type   = isset($_POST['type'])   ? trim($_POST['type'])   : '';

if (empty($tip_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'tip_id is required']);
    exit;
}

// VALIDATION: Whitelist allowed interaction types to prevent arbitrary values in DB
if (!in_array($type, ['like', 'dislike'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'type must be "like" or "dislike"']);
    exit;
}

$db      = (new Database())->connect();
$user_id = $_SESSION['user_id'];

// Check if the user has already interacted with this tip
$check = $db->prepare('SELECT id, type FROM likes WHERE user_id = ? AND tip_id = ?');
$check->bind_param('ss', $user_id, $tip_id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
$check->close();

if ($existing) {
    if ($existing['type'] === $type) {
        // Same button clicked again: toggle it off (remove the like/dislike)
        $stmt = $db->prepare('DELETE FROM likes WHERE id = ?');
        // FIX: existing['id'] is the likes table PK. If it is an auto-increment
        // integer use "i"; if it is a string ID use "s". Defaulting to "s" to
        // match the rest of the codebase's string IDs — adjust if your likes
        // table uses an integer PK.
        $stmt->bind_param('s', $existing['id']);
    } else {
        // Different button: switch from like → dislike or vice-versa
        $stmt = $db->prepare('UPDATE likes SET type = ? WHERE id = ?');
        $stmt->bind_param('ss', $type, $existing['id']);
    }
} else {
    // First interaction: insert new record
    $stmt = $db->prepare('INSERT INTO likes (user_id, tip_id, type) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $user_id, $tip_id, $type);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Action failed']);
}

$stmt->close();
?>
