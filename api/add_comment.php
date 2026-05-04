<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../php/Database.php';
require_once '../php/Comment.php';

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

// VALIDATION: Ensure required fields are present
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$tip_id  = isset($_POST['tip_id'])  ? trim($_POST['tip_id'])  : '';

if (empty($content) || empty($tip_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Comment content and tip ID are required']);
    exit;
}

// VALIDATION: Enforce a reasonable comment length limit
if (strlen($content) > 1000) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Comment must be 1000 characters or fewer']);
    exit;
}

$db = (new Database())->connect();
$comment = new Comment($db);

$comment->comment_content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
$comment->user_id = $_SESSION['user_id'];
$comment->tip_id  = $tip_id;
$comment->id      = $comment->generateId();

if ($comment->create()) {
    http_response_code(201);
    echo json_encode(['status' => 'success', 'message' => 'Comment added']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to save comment']);
}
?>
