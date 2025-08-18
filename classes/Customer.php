<?php
// Customer management class

class Customer {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        $sql = "INSERT INTO customers (company_name, organization_number, contact_person, email, phone, address, city, postal_code, county, country, status, assigned_to, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['company_name'],
            $data['organization_number'] ?? null,
            $data['contact_person'],
            $data['email'],
            $data['phone'],
            $data['address'] ?? '',
            $data['city'] ?? '',
            $data['postal_code'] ?? '',
            $data['county'] ?? '',
            $data['country'] ?? 'SE', // Default to Sweden
            $data['status'] ?? 'active',
            $data['assigned_to'] ?? null
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->getLastInsertId();
    }
    
    public function findById($id) {
        $sql = "SELECT c.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM customers c 
                LEFT JOIN users u ON c.assigned_to = u.id 
                WHERE c.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getAll() {
        $sql = "SELECT c.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM customers c 
                LEFT JOIN users u ON c.assigned_to = u.id 
                ORDER BY c.company_name ASC";
        return $this->db->fetchAll($sql);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE customers SET company_name = ?, organization_number = ?, contact_person = ?, email = ?, phone = ?, address = ?, city = ?, postal_code = ?, county = ?, country = ?, status = ?, assigned_to = ? WHERE id = ?";
        
        $params = [
            $data['company_name'],
            $data['organization_number'] ?? null,
            $data['contact_person'],
            $data['email'],
            $data['phone'],
            $data['address'] ?? '',
            $data['city'] ?? '',
            $data['postal_code'] ?? '',
            $data['county'] ?? '',
            $data['country'] ?? 'SE',
            $data['status'] ?? 'active',
            $data['assigned_to'] ?? null,
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM customers WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function search($query) {
        $sql = "SELECT c.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM customers c 
                LEFT JOIN users u ON c.assigned_to = u.id 
                WHERE c.company_name LIKE ? OR c.contact_person LIKE ? OR c.email LIKE ? OR c.phone LIKE ? OR c.organization_number LIKE ?
                ORDER BY c.company_name ASC";
        
        $searchTerm = "%$query%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    public function getByStatus($status) {
        $sql = "SELECT c.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM customers c 
                LEFT JOIN users u ON c.assigned_to = u.id 
                WHERE c.status = ? 
                ORDER BY c.company_name ASC";
        return $this->db->fetchAll($sql, [$status]);
    }
    
    public function getByAssignedUser($user_id) {
        $sql = "SELECT c.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM customers c 
                LEFT JOIN users u ON c.assigned_to = u.id 
                WHERE c.assigned_to = ? 
                ORDER BY c.company_name ASC";
        return $this->db->fetchAll($sql, [$user_id]);
    }
}
?>