<?php
// Contact management class

class Contact {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        $sql = "INSERT INTO contacts (customer_id, first_name, last_name, email, phone, position, is_primary, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['customer_id'] ?? null,
            $data['first_name'],
            $data['last_name'],
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['position'] ?? null,
            $data['is_primary'] ?? 0
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->getLastInsertId();
    }
    
    public function findById($id) {
        $sql = "SELECT c.*, cu.company_name 
                FROM contacts c 
                LEFT JOIN customers cu ON c.customer_id = cu.id 
                WHERE c.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getAll() {
        $sql = "SELECT c.*, cu.company_name 
                FROM contacts c 
                LEFT JOIN customers cu ON c.customer_id = cu.id 
                ORDER BY c.last_name ASC, c.first_name ASC";
        return $this->db->fetchAll($sql);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE contacts SET customer_id = ?, first_name = ?, last_name = ?, email = ?, phone = ?, position = ?, is_primary = ? WHERE id = ?";
        
        $params = [
            $data['customer_id'] ?? null,
            $data['first_name'],
            $data['last_name'],
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['position'] ?? null,
            $data['is_primary'] ?? 0,
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM contacts WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function search($query) {
        $sql = "SELECT c.*, cu.company_name 
                FROM contacts c 
                LEFT JOIN customers cu ON c.customer_id = cu.id 
                WHERE c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ? OR c.phone LIKE ? OR cu.company_name LIKE ?
                ORDER BY c.last_name ASC, c.first_name ASC";
        
        $searchTerm = "%$query%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    public function getByCustomerId($customer_id) {
        $sql = "SELECT c.*, cu.company_name 
                FROM contacts c 
                LEFT JOIN customers cu ON c.customer_id = cu.id 
                WHERE c.customer_id = ? 
                ORDER BY c.is_primary DESC, c.last_name ASC, c.first_name ASC";
        return $this->db->fetchAll($sql, [$customer_id]);
    }
    
    public function getByCustomerIdPrimaryFirst($customer_id) {
        $sql = "SELECT c.*, cu.company_name 
                FROM contacts c 
                LEFT JOIN customers cu ON c.customer_id = cu.id 
                WHERE c.customer_id = ? 
                ORDER BY c.is_primary DESC, c.last_name ASC, c.first_name ASC";
        return $this->db->fetchAll($sql, [$customer_id]);
    }
    
    public function setPrimary($id, $customer_id) {
        // First set all contacts for this customer as non-primary
        $sql1 = "UPDATE contacts SET is_primary = 0 WHERE customer_id = ?";
        $this->db->execute($sql1, [$customer_id]);
        
        // Then set this contact as primary
        $sql2 = "UPDATE contacts SET is_primary = 1 WHERE id = ?";
        return $this->db->execute($sql2, [$id]);
    }
    
    public function getContactStats() {
        $sql = "SELECT 
                    COUNT(*) as total_contacts,
                    COUNT(CASE WHEN email IS NOT NULL AND email != '' THEN 1 END) as contacts_with_email,
                    COUNT(CASE WHEN phone IS NOT NULL AND phone != '' THEN 1 END) as contacts_with_phone
                FROM contacts";
        
        return $this->db->fetchOne($sql);
    }
}
?>