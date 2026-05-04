<?php

class Comment {

    private $conn;
    private $table = 'comments';

    public $id;
    public $comment_content;
    public $user_id;
    public $tip_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Generate a sequential ID for a new comment (e.g. C0001, C0002 …)
     */
    public function generateId() {
        $result = $this->conn->query("SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1");

        if ($result && $result->num_rows > 0) {
            $row    = $result->fetch_assoc();
            $number = intval(substr($row['id'], 1)) + 1;
            return 'C' . str_pad($number, 4, '0', STR_PAD_LEFT);
        }
        return 'C0001';
    }

    /**
     * Insert a new comment into the database.
     * SECURITY: comment_content should be sanitized before calling this method.
     */
    public function create() {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (id, comment_content, user_id, tip_id)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'ssss',
            $this->id,
            $this->comment_content,
            $this->user_id,
            $this->tip_id
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
?>
