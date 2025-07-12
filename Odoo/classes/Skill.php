<?php
require_once 'config/database.php';

class Skill {
    private $conn;
    private $table_name = "skills";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllSkills() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addSkill($name, $category = null) {
        $query = "INSERT INTO " . $this->table_name . " (name, category) VALUES (:name, :category)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category', $category);
        return $stmt->execute();
    }

    public function getUserSkillsOffered($user_id) {
        $query = "SELECT s.*, uso.proficiency_level, uso.description 
                  FROM skills s 
                  JOIN user_skills_offered uso ON s.id = uso.skill_id 
                  WHERE uso.user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserSkillsWanted($user_id) {
        $query = "SELECT s.*, usw.desired_level, usw.description 
                  FROM skills s 
                  JOIN user_skills_wanted usw ON s.id = usw.skill_id 
                  WHERE usw.user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUserSkillOffered($user_id, $skill_id, $proficiency_level, $description = null) {
        $query = "INSERT INTO user_skills_offered (user_id, skill_id, proficiency_level, description) 
                  VALUES (:user_id, :skill_id, :proficiency_level, :description)
                  ON DUPLICATE KEY UPDATE proficiency_level = :proficiency_level, description = :description";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':skill_id', $skill_id);
        $stmt->bindParam(':proficiency_level', $proficiency_level);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    public function addUserSkillWanted($user_id, $skill_id, $desired_level, $description = null) {
        $query = "INSERT INTO user_skills_wanted (user_id, skill_id, desired_level, description) 
                  VALUES (:user_id, :skill_id, :desired_level, :description)
                  ON DUPLICATE KEY UPDATE desired_level = :desired_level, description = :description";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':skill_id', $skill_id);
        $stmt->bindParam(':desired_level', $desired_level);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    public function removeUserSkillOffered($user_id, $skill_id) {
        $query = "DELETE FROM user_skills_offered WHERE user_id = :user_id AND skill_id = :skill_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':skill_id', $skill_id);
        return $stmt->execute();
    }

    public function removeUserSkillWanted($user_id, $skill_id) {
        $query = "DELETE FROM user_skills_wanted WHERE user_id = :user_id AND skill_id = :skill_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':skill_id', $skill_id);
        return $stmt->execute();
    }
}
?>
