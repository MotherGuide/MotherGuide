<?php

class Admin {
    private $conn;
    private $table = 'admins';

    public $id;
    public $name;
    public $email;
    public $password;
    public $sign_up_day;
    public $sign_up_date;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Generate a sequential ID for a new admin (e.g. A0001, A0002 …)
     */
    public function generateId() {
        $result = $this->conn->query("SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1");

        if ($result && $result->num_rows > 0) {
            $row    = $result->fetch_assoc();
            $number = intval(substr($row['id'], 1)) + 1;
            return 'A' . str_pad($number, 4, '0', STR_PAD_LEFT);
        }
        return 'A0001';
    }

    /**
     * Return true if the current $this->email already exists in the DB.
     */
    public function emailExists() {
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $this->email);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /**
     * Persist the current admin record to the database.
     * Password must already be hashed before calling this method.
     */
    public function create() {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (id, name, email, password, sign_up_day, sign_up_date)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'ssssss',
            $this->id,
            $this->name,
            $this->email,
            $this->password,
            $this->sign_up_day,
            $this->sign_up_date
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Find an admin record by email address.
     */
    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    /**
     * Attempt to log in an admin.
     * Starts / continues the session, clears any lingering user session vars,
     * and populates admin session vars on success.
     *
     * SECURITY: Generic error message prevents user enumeration.
     */
    public function login($email, $password) {
        $admin = $this->findByEmail($email);

        if ($admin && password_verify($password, $admin['password'])) {
            // Safe to start/resume session here
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Clear any existing user (mother) session to prevent privilege mixing
            foreach (['user_id', 'user_name', 'email', 'pregnancy_week', 'sign_up_day', 'sign_up_date'] as $key) {
                unset($_SESSION[$key]);
            }

            $_SESSION['admin_id']    = $admin['id'];
            $_SESSION['admin_name']  = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];

            return ['status' => 'success', 'message' => 'Admin login successful'];
        }

        return ['status' => 'error', 'message' => 'Invalid email or password'];
    }

    /**
     * Generate a sequential ID for a new tip (e.g. T0001, T0002 …)
     */
    public function generateTipId() {
        $result = $this->conn->query("SELECT id FROM tips ORDER BY id DESC LIMIT 1");

        if ($result && $result->num_rows > 0) {
            $row    = $result->fetch_assoc();
            $number = intval(substr($row['id'], 1)) + 1;
            return 'T' . str_pad($number, 4, '0', STR_PAD_LEFT);
        }
        return 'T0001';
    }

    /**
     * Insert a new tip into the database.
     *
     * @param string $title
     * @param string $content
     * @param int    $week    Pregnancy week (1–40)
     * @return array ['status' => 'success'|'error', 'id' => string, 'message' => string]
     */
    public function addTip($title, $content, $week) {
        $id   = $this->generateTipId();
        $week = (int) $week;

        $stmt = $this->conn->prepare(
            "INSERT INTO tips (id, title, content, pregnancy_week, like_count, dislike_count, comment_count, views)
             VALUES (?, ?, ?, ?, 0, 0, 0, 0)"
        );
        // BINDING: s=id(string), s=title(string), s=content(string), i=week(int)
        $stmt->bind_param('sssi', $id, $title, $content, $week);

        if ($stmt->execute()) {
            $stmt->close();
            return ['status' => 'success', 'id' => $id];
        }

        $error = $stmt->error;
        $stmt->close();
        return ['status' => 'error', 'message' => $error];
    }
}
?>
