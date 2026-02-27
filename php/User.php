<?php

class User {

    private $conn;
    private $table = "users";

    public $id;
    public $name;
    public $email;
    public $password;
    public $sign_up_day;
    public $sign_up_date;
    public $pregnancy_week;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create User
    public function create() {

        $query = "INSERT INTO " . $this->table . " 
                  (id, name, email, password, sign_up_day, sign_up_date, pregnancy_week)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bind_param(
            "ssssssi",
            $this->id,
            $this->name,
            $this->email,
            $this->password,
            $this->sign_up_day,
            $this->sign_up_date,
            $this->pregnancy_week
        );

        return $stmt->execute();
    }

    // Find User By Email
    public function findByEmail($email) {

        $query = "SELECT * FROM " . $this->table . " WHERE email = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}