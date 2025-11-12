<?php
// Enable clear error reporting during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Database Connection Handler
 * Works reliably in both local and hosted environments (InfinityFree, XAMPP, etc.)
 */

require_once __DIR__ . '/config.php';  // Always load from the same directory

class DB {
    private $conn;

    public function __construct() {
        // Load config (expects class Config with host, user, pass, db)
        $cfg = new Config();

        // Try connecting to MySQL
        $this->conn = @new mysqli($cfg->host, $cfg->user, $cfg->pass, $cfg->db);

        // If connection fails, show detailed error for debugging
        if ($this->conn->connect_error) {
    error_log("DB connection error: " . $this->conn->connect_error);
    // Exit with a generic message to client
    http_response_code(500);
    die(json_encode(["success" => false, "msg" => "database_unavailable"]));
}


        // Always use UTF-8 for proper encoding
        $this->conn->set_charset("utf8mb4");
    }

    // Expose connection object safely
    public function conn() {
        return $this->conn;
    }

    // Optional helper: execute query safely
    public function query($sql) {
        $res = $this->conn->query($sql);
        if (!$res) {
            throw new Exception("Database query failed: " . $this->conn->error);
        }
        return $res;
    }

    // Optional helper: close DB connection
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
