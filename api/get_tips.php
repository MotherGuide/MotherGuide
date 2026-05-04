<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../php/Database.php';

// Allow GET or POST (this is a read endpoint)
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$db   = new Database();
$conn = $db->connect();

// FIX: The original had no null-check on $result before calling fetch_assoc(),
// which would throw a fatal error if the query failed.
$result = $conn->query('SELECT * FROM tips ORDER BY pregnancy_week ASC, id ASC');

if (!$result) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $conn->error]);
    $conn->close();
    exit;
}

$tips = [];
while ($row = $result->fetch_assoc()) {
    $tips[] = $row;
}

$conn->close();

echo json_encode([
    'status' => 'success',
    'data'   => $tips,
    'count'  => count($tips),
]);
?>
