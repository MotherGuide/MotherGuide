<?php

class Tip {

    private $conn;
    private $table = "tips";

    public $id;
    public $title;
    public $content;
    public $pregnancy_week;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create Tip
    public function create() {

        $query = "INSERT INTO " . $this->table . "
                  (id, title, content, pregnancy_week)
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bind_param(
            "sssi",
            $this->id,
            $this->title,
            $this->content,
            $this->pregnancy_week
        );

        return $stmt->execute();
    }

    // Get Tips By Week
    public function getByWeek($week) {

        $query = "SELECT * FROM " . $this->table . " WHERE pregnancy_week = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $week);
        $stmt->execute();

        return $stmt->get_result();
    }
}