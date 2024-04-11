<?php

class Database
{
    private $servername;
    private $username;
    private $db_name;
    private $db_password;
    private $db_port; // New property for database port

    private static $instance = null;
    private $conn = null;

    private function __construct()
    {
        $this->servername = $_ENV['DB_SERVER_NAME'];
        $this->username = $_ENV['DB_USER_NAME'];
        $this->db_name = $_ENV['DB_NAME'];
        $this->db_password = $_ENV['DB_PASSWORD'];
        $this->db_port = $_ENV['DB_PORT']; // Set the database port from environment variables

        try {
            $this->conn = new PDO("mysql:host=$this->servername;port=$this->db_port;dbname=$this->db_name", $this->username, $this->db_password);
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Database connection failed: " . $e->getMessage();
            die();
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
           self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}