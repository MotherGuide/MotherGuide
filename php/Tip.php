<?php

class Tip {

    private $conn;
    private $table = "tips";

    // CLASS PROPERTIES: Define all public attributes for tip management
    public $id;
    public $title;
    public $content;
    public $pregnancy_week;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * CREATE: Persist a new tip record to the database
     * 
     * SECURITY NOTE: Uses prepared statements with parameterized queries to prevent SQL injection
     * The pregnancy_week parameter is explicitly cast to integer (i) to ensure data type safety
     * 
     * @return bool Returns true if insert was successful, false otherwise
     */
    public function create() {

        $query = "INSERT INTO " . $this->table . "
                  (id, title, content, pregnancy_week)
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // BINDING: Explicitly declare parameter types (s=string, i=integer)
        $stmt->bind_param(
            "sssi",
            $this->id,
            $this->title,
            $this->content,
            $this->pregnancy_week
        );

        return $stmt->execute();
    }

    /**
     * RETRIEVE: Fetch all tips for a specific pregnancy week
     * 
     * PERFORMANCE: Returns full result set for the given week. Consider pagination for large datasets.
     * 
     * @param int $week The pregnancy week to filter tips by (1-40)
     * @return mysqli_result Result set containing matching tips
     */
    public function getByWeek($week) {

        $query = "SELECT * FROM " . $this->table . " WHERE pregnancy_week = ?";

        $stmt = $this->conn->prepare($query);
        // BINDING: Parameter type "i" ensures week is treated as integer
        $stmt->bind_param("i", $week);
        $stmt->execute();

        return $stmt->get_result();
    }
}