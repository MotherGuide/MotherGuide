<?php

class Tip {
    private $conn;
    private $table = 'tips';

    public $id;
    public $title;
    public $content;
    public $pregnancy_week;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Generate a sequential ID for a new tip (e.g. T0001, T0002 …)
     */
    public function generateId() {
        $result = $this->conn->query("SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1");

        if ($result && $result->num_rows > 0) {
            $row    = $result->fetch_assoc();
            $number = intval(substr($row['id'], 1)) + 1;
            return 'T' . str_pad($number, 4, '0', STR_PAD_LEFT);
        }
        return 'T0001';
    }

    /**
     * Insert a new tip into the database.
     * SECURITY: Uses prepared statement to prevent SQL injection.
     *
     * @return bool
     */
    public function create() {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (id, title, content, pregnancy_week, like_count, dislike_count, comment_count, views)
             VALUES (?, ?, ?, ?, 0, 0, 0, 0)"
        );
        // BINDING: s=id, s=title, s=content, i=pregnancy_week
        $week = (int) $this->pregnancy_week;
        $stmt->bind_param('sssi', $this->id, $this->title, $this->content, $week);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Retrieve all tips for a given pregnancy week.
     * PERFORMANCE: For large datasets consider adding LIMIT/OFFSET pagination.
     *
     * @param int $week Pregnancy week (1–40)
     * @return array
     */
    public function getByWeek($week) {
        $week = (int) $week;
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE pregnancy_week = ? ORDER BY id ASC"
        );
        $stmt->bind_param('i', $week);
        $stmt->execute();
        $rows   = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    /**
     * Fetch live like/dislike/comment counts for a given tip from their
     * respective tables (more accurate than cached columns).
     *
     * FIX: The original getStats() bound tip_id as an integer ("i") but tip IDs
     * in this project are strings like "T0001". Changed binding to "s".
     *
     * @param string $tip_id
     * @return array ['likes' => int, 'dislikes' => int, 'comments' => int]
     */
    public function getStats($tip_id) {
        $stats = ['likes' => 0, 'dislikes' => 0, 'comments' => 0];

        // Count likes and dislikes grouped by type
        $stmt = $this->conn->prepare(
            "SELECT type, COUNT(*) AS count FROM likes WHERE tip_id = ? GROUP BY type"
        );
        $stmt->bind_param('s', $tip_id);  // FIX: was "i", tips use string IDs
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if ($row['type'] === 'like')    $stats['likes']    = (int)$row['count'];
            if ($row['type'] === 'dislike') $stats['dislikes'] = (int)$row['count'];
        }
        $stmt->close();

        // Count comments for this tip
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS count FROM comments WHERE tip_id = ?"
        );
        $stmt->bind_param('s', $tip_id);  // FIX: was "i"
        $stmt->execute();
        $stats['comments'] = (int)$stmt->get_result()->fetch_assoc()['count'];
        $stmt->close();

        return $stats;
    }
}
?>
