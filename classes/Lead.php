<?php
// Lead management class

class Lead {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        $sql = "INSERT INTO leads (first_name, last_name, company_name, email, phone, address, city, postal_code, county, country, source, status, assigned_to, notes, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['first_name'],
            $data['last_name'],
            $data['company_name'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['address'] ?? '',
            $data['city'] ?? '',
            $data['postal_code'] ?? '',
            $data['county'] ?? '',
            $data['country'] ?? 'SE', // Default to Sweden
            $data['source'] ?? 'website',
            $data['status'] ?? 'new',
            $data['assigned_to'] ?? null,
            $data['notes'] ?? ''
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->getLastInsertId();
    }
    
    public function findById($id) {
        $sql = "SELECT l.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM leads l 
                LEFT JOIN users u ON l.assigned_to = u.id 
                WHERE l.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getAll() {
        $sql = "SELECT l.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM leads l 
                LEFT JOIN users u ON l.assigned_to = u.id 
                ORDER BY l.created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE leads SET first_name = ?, last_name = ?, company_name = ?, email = ?, phone = ?, address = ?, city = ?, postal_code = ?, county = ?, country = ?, source = ?, status = ?, assigned_to = ?, notes = ? WHERE id = ?";
        
        $params = [
            $data['first_name'],
            $data['last_name'],
            $data['company_name'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['address'] ?? '',
            $data['city'] ?? '',
            $data['postal_code'] ?? '',
            $data['county'] ?? '',
            $data['country'] ?? 'SE',
            $data['source'] ?? 'website',
            $data['status'] ?? 'new',
            $data['assigned_to'] ?? null,
            $data['notes'] ?? '',
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM leads WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function search($query) {
        $sql = "SELECT l.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM leads l 
                LEFT JOIN users u ON l.assigned_to = u.id 
                WHERE l.first_name LIKE ? OR l.last_name LIKE ? OR l.company_name LIKE ? OR l.email LIKE ? OR l.phone LIKE ?
                ORDER BY l.created_at DESC";
        
        $searchTerm = "%$query%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    public function getByStatus($status) {
        $sql = "SELECT l.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM leads l 
                LEFT JOIN users u ON l.assigned_to = u.id 
                WHERE l.status = ? 
                ORDER BY l.created_at DESC";
        return $this->db->fetchAll($sql, [$status]);
    }
    
    public function getBySource($source) {
        $sql = "SELECT l.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM leads l 
                LEFT JOIN users u ON l.assigned_to = u.id 
                WHERE l.source = ? 
                ORDER BY l.created_at DESC";
        return $this->db->fetchAll($sql, [$source]);
    }
    
    public function getByAssignedUser($user_id) {
        $sql = "SELECT l.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM leads l 
                LEFT JOIN users u ON l.assigned_to = u.id 
                WHERE l.assigned_to = ? 
                ORDER BY l.created_at DESC";
        return $this->db->fetchAll($sql, [$user_id]);
    }
    
    public function convertToCustomer($lead_id) {
        // Get lead details
        $lead = $this->findById($lead_id);
        if (!$lead) {
            return false;
        }
        
        // Create customer from lead
        $customerSql = "INSERT INTO customers (company_name, contact_person, email, phone, address, city, postal_code, county, country, status, assigned_to, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())";
        
        $customerParams = [
            $lead['company_name'] ?? ($lead['first_name'] . ' ' . $lead['last_name']),
            $lead['first_name'] . ' ' . $lead['last_name'],
            $lead['email'] ?? null,
            $lead['phone'] ?? null,
            $lead['address'] ?? '',
            $lead['city'] ?? '',
            $lead['postal_code'] ?? '',
            $lead['county'] ?? '',
            $lead['country'] ?? 'SE',
            $lead['assigned_to'] ?? null
        ];
        
        $this->db->execute($customerSql, $customerParams);
        $customerId = $this->db->getLastInsertId();
        
        // Create primary contact
        if ($customerId) {
            $contactSql = "INSERT INTO contacts (customer_id, first_name, last_name, email, phone, position, is_primary, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, 1, NOW())";
            
            $contactParams = [
                $customerId,
                $lead['first_name'],
                $lead['last_name'],
                $lead['email'] ?? null,
                $lead['phone'] ?? null,
                'Kontakt', // Default position in Swedish
                1 // is_primary
            ];
            
            $this->db->execute($contactSql, $contactParams);
        }
        
        // Update lead status to converted
        $this->update($lead_id, ['status' => 'converted']);
        
        return $customerId;
    }
    
    public function getLeadStats() {
        $sql = "SELECT 
                    COUNT(*) as total_leads,
                    COUNT(CASE WHEN status = 'new' THEN 1 END) as new_leads,
                    COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted_leads,
                    COUNT(CASE WHEN status = 'qualified' THEN 1 END) as qualified_leads,
                    COUNT(CASE WHEN status = 'lost' THEN 1 END) as lost_leads,
                    COUNT(CASE WHEN status = 'converted' THEN 1 END) as converted_leads
                FROM leads";
        
        return $this->db->fetchOne($sql);
    }
    
    public function getLeadsByDateRange($start_date, $end_date) {
        $sql = "SELECT l.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name 
                FROM leads l 
                LEFT JOIN users u ON l.assigned_to = u.id 
                WHERE l.created_at >= ? AND l.created_at <= ?
                ORDER BY l.created_at DESC";
        
        return $this->db->fetchAll($sql, [$start_date, $end_date]);
    }
}
?>