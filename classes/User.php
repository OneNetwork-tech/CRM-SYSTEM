<?php
// User management class

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        $sql = "INSERT INTO users (username, email, password, first_name, last_name, role, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $params = [
            $data['username'],
            $data['email'],
            $hashed_password,
            $data['first_name'],
            $data['last_name'],
            $data['role'] ?? 'user'
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->getLastInsertId();
    }
    
    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        return $this->db->fetchOne($sql, [$username]);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ? WHERE id = ?";
        $params = [
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['role'],
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    public function getAll() {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function authenticate($username, $password) {
        $user = $this->findByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
}
?>