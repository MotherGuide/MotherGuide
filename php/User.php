<?php
// php/User.php[cite: 19]

class User {
    private $conn;
    private $table = "users";

    public $id, $name, $email, $password, $sign_up_day, $sign_up_date, $pregnancy_week;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function generateId() {
        $query = "SELECT id FROM " . $this->table . " ORDER BY id DESC LIMIT 1";
        $result = $this->conn->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $number = intval(substr($row['id'], 1)) + 1;
            return "U" . str_pad($number, 4, "0", STR_PAD_LEFT);
        }
        return "U0001";
    }

    public function emailExists() {
        $query = "SELECT id FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (id, name, email, password, sign_up_day, sign_up_date, pregnancy_week)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssi", 
            $this->id, $this->name, $this->email, 
            $this->password, $this->sign_up_day, 
            $this->sign_up_date, $this->pregnancy_week
        );
        return $stmt->execute();
    }

    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function login($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            // Essential: Start session to maintain login state[cite: 19]
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["pregnancy_week"] = $user["pregnancy_week"];
            
            return ["status" => "success", "message" => "Login successful"];
        }
        return ["status" => "error", "message" => "Invalid email or password"];
    }
}
?>