<?php

class Database {
    private $host = "localhost";
    private $db_name = "authdb";
    private $username = "root";
    private $password = ""; 
    public $conn; 

    public function __construct() 
    {
        $this->conn = null; // Fixed variable name from 'connect' to 'conn'
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connected successfully"; // Good for testing, but usually hidden in production
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
} 
?>
