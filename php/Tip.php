<?php
class Tip {
    private $conn;
    private $table = "tips";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get a specific tip with stats[cite: 9, 13]
    public function getStats($tip_id) {
        $stats = ['likes' => 0, 'dislikes' => 0, 'comments' => 0];
        
        // Count Likes/Dislikes
        $query = "SELECT type, COUNT(*) as count FROM likes WHERE tip_id = ? GROUP BY type";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $tip_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            if($row['type'] == 'like') $stats['likes'] = $row['count'];
            if($row['type'] == 'dislike') $stats['dislikes'] = $row['count'];
        }

        // Count Comments[cite: 13]
        $query = "SELECT COUNT(*) as count FROM comments WHERE tip_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $tip_id);
        $stmt->execute();
        $stats['comments'] = $stmt->get_result()->fetch_assoc()['count'];

        return $stats;
    }
}
?>