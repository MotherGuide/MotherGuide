<?php

class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $name;
    public $email;
    public $password;
    public $sign_up_day;
    public $sign_up_date;
    public $pregnancy_week;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Generate a sequential ID for a new user (e.g. U0001, U0002 …)
     */
    public function generateId() {
        $result = $this->conn->query("SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $row    = $result->fetch_assoc();
            $number = intval(substr($row['id'], 1)) + 1;
            return 'U' . str_pad($number, 4, '0', STR_PAD_LEFT);
        }
        return 'U0001';
    }

    /**
     * Return true if $this->email already exists in the database.
     * SECURITY: Uses prepared statement to prevent SQL injection.
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
     * Persist the current user record to the database.
     * IMPORTANT: $this->password must already be hashed before calling this.
     * BINDING: 6 strings + 1 integer = "ssssssi"
     */
    public function create() {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table}
             (id, name, email, password, sign_up_day, sign_up_date, pregnancy_week)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'ssssssi',
            $this->id,
            $this->name,
            $this->email,
            $this->password,
            $this->sign_up_day,
            $this->sign_up_date,
            $this->pregnancy_week
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Find a user record by email address.
     * SECURITY: Uses prepared statement to prevent SQL injection.
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
     * Attempt to log in a user.
     * Starts the PHP session safely and populates session vars on success.
     *
     * SECURITY:
     * - password_verify() prevents timing attacks compared to direct comparison.
     * - Generic error message prevents user enumeration.
     * - session_regenerate_id() prevents session fixation attacks.
     */
    public function login($email, $password) {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // SECURITY: Regenerate session ID on privilege escalation
            session_regenerate_id(true);

            $_SESSION['user_id']        = $user['id'];
            $_SESSION['user_name']      = $user['name'];
            $_SESSION['email']          = $user['email'];
            $_SESSION['pregnancy_week'] = $user['pregnancy_week'];

            return ['status' => 'success', 'message' => 'Login successful'];
        }

        return ['status' => 'error', 'message' => 'Invalid email or password'];
    }
}
?>
