<?php
ini_set('session.gc_maxlifetime', 900); //Expire session after 900s -> 15mins
ini_set('session.cookie_maxlifetime', 900); //Expire session after 900s -> 15mins
session_start();

// Check if the last user activity timestamp is set
if (isset($_SESSION['last_activity'])) {
    $session_lifetime = ini_get('session.gc_maxlifetime');
    $current_time = time();
    $last_activity_time = $_SESSION['last_activity'];

    // Check if the user has been inactive for too long
    if ($current_time - $last_activity_time > $session_lifetime) {
        // Expire the session and destroy it
        session_unset();
        session_destroy();
        header('Location: ' . SERVER_ROOT . '/index.php'); // Redirect to a login page or wherever you want
        exit();
    }
}

// Update the last activity timestamp
$_SESSION['last_activity'] = time();
class Database
{
    private $host;
    private $username;
    private $password;
    private $database;

    public function __construct()
    {
        // Use environment variables if available (Docker), otherwise use defaults (local development)
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: 'beyondm';
        $this->database = getenv('DB_NAME') ?: 'uoj';
    }

    public function getConnection()
    {
        $conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($conn->connect_error) {
            die("MySQL Connection error occurred!: " . $conn->connect_error);
        } else {
            return $conn;
        }
    }
}