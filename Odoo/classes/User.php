<?php
require_once 'config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $email, $password, $full_name, $location = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password_hash, full_name, location) 
                  VALUES (:username, :email, :password_hash, :full_name, :location)";
        
        $stmt = $this->conn->prepare($query);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':location', $location);
        
        return $stmt->execute();
    }

    public function login($username, $password) {
        $query = "SELECT id, username, email, password_hash, full_name, is_admin 
                  FROM " . $this->table_name . " 
                  WHERE username = :username OR email = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password_hash'])) {
                return $row;
            }
        }
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET full_name = :full_name, location = :location, 
                      availability = :availability, is_public = :is_public 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':location', $data['location']);
        $stmt->bindParam(':availability', $data['availability']);
        $stmt->bindParam(':is_public', $data['is_public']);
        
        return $stmt->execute();
    }

    public function searchUsers($skill_name = null, $location = null) {
        $query = "SELECT DISTINCT u.* FROM users u 
                  LEFT JOIN user_skills_offered uso ON u.id = uso.user_id 
                  LEFT JOIN skills s ON uso.skill_id = s.id 
                  WHERE u.is_public = 1";
        
        $params = [];
        
        if ($skill_name) {
            $query .= " AND s.name LIKE :skill_name";
            $params[':skill_name'] = '%' . $skill_name . '%';
        }
        
        if ($location) {
            $query .= " AND u.location LIKE :location";
            $params[':location'] = '%' . $location . '%';
        }
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
