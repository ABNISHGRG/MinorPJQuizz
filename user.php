<?php
include_once 'Database.php';

class User extends Database {
    private $table_name = "user";
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;

    public function register(){
        $check = $this->conn->prepare("SELECT id FROM " . $this->table_name . " WHERE email = :email");
        $check->execute([':email' => $this->email]);
        if($check->rowCount() > 0) { return false; }

        $query = "INSERT INTO " . $this->table_name . " (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':name', $this->name); 
        $stmt->bindParam(':email', $this->email); 
        $stmt->bindParam(':password', $hashed_password);
        
        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId(); 
            return true;
        }
        return false;
    }

    public function login(){
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row && password_verify($this->password, $row['password'])){
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->role = $row['role']; 
            return true;
        }
        return false;
    }
}
?>