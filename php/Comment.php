<?php

class Comment {

    private $conn;
    private $table = "comments";

    public $id;
    public $comment_content;
    public $user_id;
    public $tip_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add Comment
    public function create() {

        $query = "INSERT INTO " . $this->table . "
                  (id, comment_content, user_id, tip_id)
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bind_param(
            "ssss",
            $this->id,
            $this->comment_content,
            $this->user_id,
            $this->tip_id
        );

        return $stmt->execute();
    }
}