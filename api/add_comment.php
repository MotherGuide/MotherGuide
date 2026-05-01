<?php
session_start();
require_once '../php/Database.php';
require_once '../php/Comment.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $db = (new Database())->connect();
    $comment = new Comment($db);
    
    $comment->comment_content = htmlspecialchars($_POST['content']);
    $comment->user_id = $_SESSION['user_id'];
    $comment->tip_id = $_POST['tip_id'];

    if ($comment->create()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>