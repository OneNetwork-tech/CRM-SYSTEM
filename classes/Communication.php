<?php
// Communication management class

class Communication {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        $sql = "INSERT INTO communications (customer_id, contact_id, user_id, type, subject, content, direction, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['customer_id'] ?? null,
            $data['contact_id'] ?? null,
            $data['user_id'],
            $data['type'],
            $data['subject'],
            $data['content'],
            $data['direction'] ?? 'outbound',
            $data['status'] ?? 'completed'
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->getLastInsertId();
    }
    
    public function findById($id) {
        $sql = "SELECT c.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as user_first_name, u.last_name as user_last_name
                FROM communications c
                LEFT JOIN customers cu ON c.customer_id = cu.id
                LEFT JOIN contacts co ON c.contact_id = co.id
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getAll() {
        $sql = "SELECT c.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as user_first_name, u.last_name as user_last_name
                FROM communications c
                LEFT JOIN customers cu ON c.customer_id = cu.id
                LEFT JOIN contacts co ON c.contact_id = co.id
                LEFT JOIN users u ON c.user_id = u.id
                ORDER BY c.created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE communications SET customer_id = ?, contact_id = ?, type = ?, subject = ?, content = ?, direction = ?, status = ? WHERE id = ?";
        
        $params = [
            $data['customer_id'] ?? null,
            $data['contact_id'] ?? null,
            $data['type'],
            $data['subject'],
            $data['content'],
            $data['direction'] ?? 'outbound',
            $data['status'] ?? 'completed',
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM communications WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function search($query) {
        $sql = "SELECT c.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as user_first_name, u.last_name as user_last_name
                FROM communications c
                LEFT JOIN customers cu ON c.customer_id = cu.id
                LEFT JOIN contacts co ON c.contact_id = co.id
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.subject LIKE ? OR c.content LIKE ? OR cu.company_name LIKE ? OR co.first_name LIKE ? OR co.last_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?
                ORDER BY c.created_at DESC";
        
        $searchTerm = "%$query%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    public function getByCustomerId($customer_id) {
        $sql = "SELECT c.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as user_first_name, u.last_name as user_last_name
                FROM communications c
                LEFT JOIN customers cu ON c.customer_id = cu.id
                LEFT JOIN contacts co ON c.contact_id = co.id
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.customer_id = ?
                ORDER BY c.created_at DESC";
        return $this->db->fetchAll($sql, [$customer_id]);
    }
    
    public function getByContactId($contact_id) {
        $sql = "SELECT c.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as user_first_name, u.last_name as user_last_name
                FROM communications c
                LEFT JOIN customers cu ON c.customer_id = cu.id
                LEFT JOIN contacts co ON c.contact_id = co.id
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.contact_id = ?
                ORDER BY c.created_at DESC";
        return $this->db->fetchAll($sql, [$contact_id]);
    }
    
    public function getByUserId($user_id) {
        $sql = "SELECT c.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as user_first_name, u.last_name as user_last_name
                FROM communications c
                LEFT JOIN customers cu ON c.customer_id = cu.id
                LEFT JOIN contacts co ON c.contact_id = co.id
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.user_id = ?
                ORDER BY c.created_at DESC";
        return $this->db->fetchAll($sql, [$user_id]);
    }
    
    public function getByType($type) {
        $sql = "SELECT c.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as user_first_name, u.last_name as user_last_name
                FROM communications c
                LEFT JOIN customers cu ON c.customer_id = cu.id
                LEFT JOIN contacts co ON c.contact_id = co.id
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.type = ?
                ORDER BY c.created_at DESC";
        return $this->db->fetchAll($sql, [$type]);
    }
    
    public function getCommunicationStats() {
        $sql = "SELECT 
                    COUNT(*) as total_communications,
                    COUNT(CASE WHEN type = 'email' THEN 1 END) as email_count,
                    COUNT(CASE WHEN type = 'call' THEN 1 END) as call_count,
                    COUNT(CASE WHEN type = 'meeting' THEN 1 END) as meeting_count,
                    COUNT(CASE WHEN type = 'note' THEN 1 END) as note_count,
                    COUNT(CASE WHEN direction = 'inbound' THEN 1 END) as inbound_count,
                    COUNT(CASE WHEN direction = 'outbound' THEN 1 END) as outbound_count
                FROM communications";
        
        return $this->db->fetchOne($sql);
    }
    
    public function getCommunicationsByDateRange($start_date, $end_date) {
        $sql = "SELECT c.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as user_first_name, u.last_name as user_last_name
                FROM communications c
                LEFT JOIN customers cu ON c.customer_id = cu.id
                LEFT JOIN contacts co ON c.contact_id = co.id
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.created_at >= ? AND c.created_at <= ?
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, [$start_date, $end_date]);
    }
}
?>