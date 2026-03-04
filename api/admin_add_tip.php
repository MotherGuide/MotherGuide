<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../php/Database.php';
require_once __DIR__ . '/../php/Tip.php';

$db = new Database();
$conn = $db->connect();

$week = isset($_POST['pregnancy_week']) ? (int)$_POST['pregnancy_week'] : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

if ($week < 1 || $week > 40) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid week']);
    exit;
}
if ($title === '' || $content === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Title and content required']);
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
    echo json_encode(['status' => 'success', 'message' => 'Tip added', 'id' => $newId]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database insert failed']);
    exit;
}

?>
