<?php

namespace App\Models;

use App\Models\BaseModel;
use \PDO;

class User extends BaseModel
{
    public function save($username, $email, $first_name, $last_name, $password) {
        $sql = "INSERT INTO users (username, email, first_name, last_name, password_hash) 
                VALUES (:username, :email, :first_name, :last_name, :password_hash)";
        $statement = $this->db->prepare($sql);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $statement->bindParam(':username', $username);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':first_name', $first_name);
        $statement->bindParam(':last_name', $last_name);
        $statement->bindParam(':password_hash', $hashed_password);

        try {
            $statement->execute();
            return $statement->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Error saving user: " . $e->getMessage());
        }
    }

    public function getAllUsers() {
        $query = "SELECT id, first_name, last_name, email FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPassword($username) {
        $sql = "SELECT password_hash FROM users WHERE username = :username;";
        $statement = $this->db->prepare($sql);
        $statement->execute(['username' => $username]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['password_hash'] ?? null;
    }
}
