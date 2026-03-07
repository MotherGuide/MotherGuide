<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Validate HTTP method to prevent unintended request types (security best practice)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Verify admin authentication before processing
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../php/Database.php';
require_once __DIR__ . '/../php/Tip.php';

// === INPUT COLLECTION AND SANITIZATION ===
$db = new Database();
$conn = $db->connect();

// Retrieve and sanitize input values
$week = isset($_POST['pregnancy_week']) ? (int)$_POST['pregnancy_week'] : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// VALIDATION: Verify pregnancy week is within valid range (1-40 weeks)
if ($week < 1 || $week > 40) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid week. Pregnancy week must be between 1 and 40.']);
    exit;
}

// VALIDATION: Ensure title meets minimum length and character requirements
if (strlen($title) < 3 || strlen($title) > 255) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Title must be between 3 and 255 characters.']);
    exit;
}

// VALIDATION: Ensure content has minimum substantial length to prevent empty tips
if (strlen($content) < 10) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Content must be at least 10 characters.']);
    exit;
}

// generate a tip id like T0001
$last = $conn->query("SELECT id FROM tips ORDER BY id DESC LIMIT 1");
$num = 1;
if ($last && $last->num_rows > 0) {
    $row = $last->fetch_assoc();
    if (preg_match('/^T(\d+)$/', $row['id'], $m)) {
        $num = intval($m[1]) + 1;
    }
}
$newId = 'T' . str_pad($num, 4, '0', STR_PAD_LEFT);

$tip = new Tip($conn);
$tip->id = $newId;
$tip->title = $title;
$tip->content = $content;
$tip->pregnancy_week = $week;

if ($tip->create()) {
    // Return 201 Created HTTP status for successful resource creation
    http_response_code(201);
    echo json_encode(['status' => 'success', 'message' => 'Tip added successfully', 'id' => $newId]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database insert failed']);
    exit;
}

?>
