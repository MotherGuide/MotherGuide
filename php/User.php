<?php

class User {

    private $conn;
    private $table = "users";

    // CLASS PROPERTIES: Define all public attributes for user management
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
     * GENERATE ID: Create a unique sequential user identifier
     * 
     * PATTERN: Generates IDs in format U0001, U0002, etc.
     * Retrieves the last user ID from database, increments it, and formats with padding
     * 
     * @return string The newly generated user ID with leading zeros
     */
    public function generateId() {

        $query = "SELECT id FROM " . $this->table . " ORDER BY id DESC LIMIT 1";
        $result = $this->conn->query($query);

        if ($result && $result->num_rows > 0) {

            $row = $result->fetch_assoc();
            $lastId = $row['id'];

            // Extract numeric portion from existing ID (e.g., "0001" from "U0001")
            $number = intval(substr($lastId, 1));
            $number++;

            return "U" . str_pad($number, 4, "0", STR_PAD_LEFT);
        }

        return "U0001";
    }

    /**
     * EMAIL EXISTS: Check for duplicate email addresses
     * 
     * SECURITY: Uses prepared statement to prevent SQL injection
     * Called during registration to ensure email uniqueness
     * 
     * @return bool True if email exists in database, false otherwise
     */
    public function emailExists() {

        $query = "SELECT id FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        // BINDING: Parameter type "s" for string email value
        $stmt->bind_param("s", $this->email);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    /**
     * CREATE: Persist a new user record to the database
     * 
     * SECURITY: Uses prepared statements with explicit type binding (s=string, i=integer)
     * Password should already be hashed before calling this method
     * 
     * @return bool Returns true if insert was successful, false otherwise
     */
    public function create() {

        $query = "INSERT INTO " . $this->table . " 
                  (id, name, email, password, sign_up_day, sign_up_date, pregnancy_week)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // BINDING: Parameter types - six strings (s) and one integer (i) for pregnancy_week
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

    /**
     * FIND BY EMAIL: Retrieve a user record using their email address
     * 
     * SECURITY: Uses prepared statement to prevent SQL injection
     * Returns complete user record including hashed password for verification
     * 
     * @param string $email The user's email address
     * @return array|null User data array if found, null if not found
     */
    public function findByEmail($email) {

        $query = "SELECT * FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        // BINDING: Parameter type "s" for email string
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * LOGIN: Authenticate user credentials and establish session
     * 
     * SECURITY: 
     * - Uses password_verify() for secure bcrypt password comparison
     * - Clears previous session data to prevent privilege escalation
     * - Stores minimal user data in session for access control
     * 
     * @param string $email The user's email address
     * @param string $password The user's plaintext password (verified against bcrypt hash)
     * @return array JSON-compatible response array with status and message
     */
    public function login($email, $password) {

        $user = $this->findByEmail($email);

        // VERIFICATION: Check both that user exists AND password matches hashed value
        if ($user && password_verify($password, $user['password'])) {

            session_start();
            // SESSION CLEANUP: Remove any admin credentials if present to prevent privilege escalation
            if (isset($_SESSION['admin_id'])) {
                unset($_SESSION['admin_id']);
                unset($_SESSION['admin_name']);
                unset($_SESSION['admin_email']);
                unset($_SESSION['sign_up_day']);
                unset($_SESSION['sign_up_date']);
            }
            
            // SESSION CREATION: Store minimal user data for authenticated requests
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["sign_up_day"] = $user["sign_up_day"];
            $_SESSION["sign_up_date"] = $user["sign_up_date"];
            $_SESSION["pregnancy_week"] = $user["pregnancy_week"];

            return ["status" => "success", "message" => "Login successful"];
        }

        // SECURITY: Use generic error message to prevent user enumeration attacks
        return ["status" => "error", "message" => "Invalid email or password"];
    }
}