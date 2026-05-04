<?php

class Database {

    // DATABASE CONFIGURATION: Connection parameters for MySQL/MariaDB
    // NOTE: In production, these should be loaded from environment variables or secure configuration files
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "motherguidedb";

    public $conn;

    /**
     * CONNECT: Establish database connection
     * 
     * SECURITY: Creates a new mysqli connection with error checking
     * In production environment, ensure:
     * - Credentials are stored in environment variables
     * - Use SSL for database connections (add mysqli_options: MYSQLI_OPT_SSL_MODE)
     * - Implement connection pooling for high-traffic applications
     * 
     * @return mysqli Database connection object
     * @throws Exception If connection fails, terminates with error message
     */
    public function connect() {

        $this->conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->dbname
        );

        // ERROR HANDLING: Check for connection failures and terminate gracefully
        if ($this->conn->connect_error) {
            die("Connection Failed: " . $this->conn->connect_error);
        }

        return $this->conn;
    }
}