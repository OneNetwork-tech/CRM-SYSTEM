<?php
// Task management class

class Task {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        $sql = "INSERT INTO tasks (customer_id, contact_id, title, description, due_date, priority, status, assigned_to, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['customer_id'] ?? null,
            $data['contact_id'] ?? null,
            $data['title'],
            $data['description'] ?? '',
            $data['due_date'] ?? null,
            $data['priority'] ?? 'medium',
            $data['status'] ?? 'pending',
            $data['assigned_to'] ?? null
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->getLastInsertId();
    }
    
    public function findById($id) {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getAll() {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                ORDER BY t.due_date ASC, t.priority DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE tasks SET customer_id = ?, contact_id = ?, title = ?, description = ?, due_date = ?, priority = ?, status = ?, assigned_to = ? WHERE id = ?";
        
        $params = [
            $data['customer_id'] ?? null,
            $data['contact_id'] ?? null,
            $data['title'],
            $data['description'] ?? '',
            $data['due_date'] ?? null,
            $data['priority'] ?? 'medium',
            $data['status'] ?? 'pending',
            $data['assigned_to'] ?? null,
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM tasks WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function search($query) {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.title LIKE ? OR t.description LIKE ? OR cu.company_name LIKE ? OR co.first_name LIKE ? OR co.last_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?
                ORDER BY t.due_date ASC, t.priority DESC";
        
        $searchTerm = "%$query%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    public function getByCustomerId($customer_id) {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.customer_id = ?
                ORDER BY t.due_date ASC, t.priority DESC";
        return $this->db->fetchAll($sql, [$customer_id]);
    }
    
    public function getByContactId($contact_id) {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.contact_id = ?
                ORDER BY t.due_date ASC, t.priority DESC";
        return $this->db->fetchAll($sql, [$contact_id]);
    }
    
    public function getByAssignedUser($user_id) {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.assigned_to = ?
                ORDER BY t.due_date ASC, t.priority DESC";
        return $this->db->fetchAll($sql, [$user_id]);
    }
    
    public function getByStatus($status) {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.status = ?
                ORDER BY t.due_date ASC, t.priority DESC";
        return $this->db->fetchAll($sql, [$status]);
    }
    
    public function getByPriority($priority) {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.priority = ?
                ORDER BY t.due_date ASC";
        return $this->db->fetchAll($sql, [$priority]);
    }
    
    public function getByDateRange($start_date, $end_date) {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.due_date >= ? AND t.due_date <= ?
                ORDER BY t.due_date ASC, t.priority DESC";
        return $this->db->fetchAll($sql, [$start_date, $end_date]);
    }
    
    public function getOverdueTasks() {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.due_date < CURDATE() AND t.status != 'completed'
                ORDER BY t.due_date ASC";
        return $this->db->fetchAll($sql);
    }
    
    public function getTodaysTasks() {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE DATE(t.due_date) = CURDATE() AND t.status != 'completed'
                ORDER BY t.priority DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function getUpcomingTasks($days = 7) {
        $sql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM tasks t
                LEFT JOIN customers cu ON t.customer_id = cu.id
                LEFT JOIN contacts co ON t.contact_id = co.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY) AND t.status != 'completed'
                ORDER BY t.due_date ASC";
        return $this->db->fetchAll($sql, [$days]);
    }
    
    public function completeTask($id) {
        $sql = "UPDATE tasks SET status = 'completed', updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function getTaskStats() {
        $sql = "SELECT 
                    COUNT(*) as total_tasks,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_tasks,
                    COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress_tasks,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_tasks,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_tasks,
                    COUNT(CASE WHEN due_date < CURDATE() AND status != 'completed' THEN 1 END) as overdue_tasks,
                    COUNT(CASE WHEN priority = 'high' AND status != 'completed' THEN 1 END) as high_priority_tasks
                FROM tasks";
        
        return $this->db->fetchOne($sql);
    }
}
?>