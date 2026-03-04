<?php

class Admin {
    private $conn;
    private $table = "admins";

    public $id;
    public $name;
    public $email;
    public $password;
    public $sign_up_day;
    public $sign_up_date;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function generateId() {
        $query = "SELECT id FROM " . $this->table . " ORDER BY id DESC LIMIT 1";
        $result = $this->conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = $row['id'];
            $number = intval(substr($lastId, 1));
            $number++;
            return "A" . str_pad($number, 4, "0", STR_PAD_LEFT);
        }
        return "A0001";
    }

    public function emailExists() {
        $query = "SELECT id FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (id, name, email, password, sign_up_day, sign_up_date)
                  VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "ssssss",
            $this->id,
            $this->name,
            $this->email,
            $this->password,
            $this->sign_up_day,
            $this->sign_up_date
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
        $admin = $this->findByEmail($email);

        if ($admin && password_verify($password, $admin['password'])) {
            session_start();
            // Clear any user session vars when an admin logs in
            if (isset($_SESSION['user_id'])) {
                unset($_SESSION['user_id']);
                unset($_SESSION['user_name']);
                unset($_SESSION['email']);
                unset($_SESSION['pregnancy_week']);
                unset($_SESSION['sign_up_day']);
                unset($_SESSION['sign_up_date']);
            }
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_name"] = $admin["name"];
            $_SESSION["admin_email"] = $admin["email"];
            $_SESSION["sign_up_day"] = $admin["sign_up_day"];
            $_SESSION["sign_up_date"] = $admin["sign_up_date"];

            return ["status" => "success", "message" => "Admin login successful"];
        }

        return ["status" => "error", "message" => "Invalid email or password"];
    }
}
