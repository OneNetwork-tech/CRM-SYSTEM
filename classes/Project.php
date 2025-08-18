<?php
// Project management class

class Project {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        $sql = "INSERT INTO projects (name, description, customer_id, start_date, end_date, status, priority, assigned_to, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['name'],
            $data['description'] ?? '',
            $data['customer_id'] ?? null,
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['status'] ?? 'planned',
            $data['priority'] ?? 'medium',
            $data['assigned_to'] ?? null
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->getLastInsertId();
    }
    
    public function findById($id) {
        $sql = "SELECT p.*, c.company_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM projects p
                LEFT JOIN customers c ON p.customer_id = c.id
                LEFT JOIN users u ON p.assigned_to = u.id
                WHERE p.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getAll() {
        $sql = "SELECT p.*, c.company_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM projects p
                LEFT JOIN customers c ON p.customer_id = c.id
                LEFT JOIN users u ON p.assigned_to = u.id
                ORDER BY p.created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE projects SET name = ?, description = ?, customer_id = ?, start_date = ?, end_date = ?, status = ?, priority = ?, assigned_to = ? WHERE id = ?";
        
        $params = [
            $data['name'],
            $data['description'] ?? '',
            $data['customer_id'] ?? null,
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['status'] ?? 'planned',
            $data['priority'] ?? 'medium',
            $data['assigned_to'] ?? null,
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM projects WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function search($query) {
        $sql = "SELECT p.*, c.company_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM projects p
                LEFT JOIN customers c ON p.customer_id = c.id
                LEFT JOIN users u ON p.assigned_to = u.id
                WHERE p.name LIKE ? OR p.description LIKE ? OR c.company_name LIKE ?
                ORDER BY p.created_at DESC";
        
        $searchTerm = "%$query%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    public function getByCustomerId($customer_id) {
        $sql = "SELECT p.*, c.company_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM projects p
                LEFT JOIN customers c ON p.customer_id = c.id
                LEFT JOIN users u ON p.assigned_to = u.id
                WHERE p.customer_id = ?
                ORDER BY p.created_at DESC";
        return $this->db->fetchAll($sql, [$customer_id]);
    }
    
    public function getByAssignedUser($user_id) {
        $sql = "SELECT p.*, c.company_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM projects p
                LEFT JOIN customers c ON p.customer_id = c.id
                LEFT JOIN users u ON p.assigned_to = u.id
                WHERE p.assigned_to = ?
                ORDER BY p.created_at DESC";
        return $this->db->fetchAll($sql, [$user_id]);
    }
    
    public function getByStatus($status) {
        $sql = "SELECT p.*, c.company_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM projects p
                LEFT JOIN customers c ON p.customer_id = c.id
                LEFT JOIN users u ON p.assigned_to = u.id
                WHERE p.status = ?
                ORDER BY p.created_at DESC";
        return $this->db->fetchAll($sql, [$status]);
    }
    
    public function getByPriority($priority) {
        $sql = "SELECT p.*, c.company_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM projects p
                LEFT JOIN customers c ON p.customer_id = c.id
                LEFT JOIN users u ON p.assigned_to = u.id
                WHERE p.priority = ?
                ORDER BY p.created_at DESC";
        return $this->db->fetchAll($sql, [$priority]);
    }
    
    public function getProjectStats() {
        $sql = "SELECT 
                    COUNT(*) as total_projects,
                    COUNT(CASE WHEN status = 'planned' THEN 1 END) as planned_projects,
                    COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress_projects,
                    COUNT(CASE WHEN status = 'on_hold' THEN 1 END) as on_hold_projects,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_projects,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_projects,
                    COUNT(CASE WHEN priority = 'high' THEN 1 END) as high_priority_projects
                FROM projects";
        
        return $this->db->fetchOne($sql);
    }
    
    public function getProjectsByDateRange($start_date, $end_date) {
        $sql = "SELECT p.*, c.company_name, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM projects p
                LEFT JOIN customers c ON p.customer_id = c.id
                LEFT JOIN users u ON p.assigned_to = u.id
                WHERE (p.start_date >= ? AND p.start_date <= ?) OR (p.end_date >= ? AND p.end_date <= ?)
                ORDER BY p.start_date ASC";
        
        return $this->db->fetchAll($sql, [$start_date, $end_date, $start_date, $end_date]);
    }
    
    // Project task methods
    public function createTask($data) {
        $sql = "INSERT INTO project_tasks (project_id, title, description, assigned_to, due_date, status, priority, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['project_id'],
            $data['title'],
            $data['description'] ?? '',
            $data['assigned_to'] ?? null,
            $data['due_date'] ?? null,
            $data['status'] ?? 'pending',
            $data['priority'] ?? 'medium'
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->getLastInsertId();
    }
    
    public function getProjectTasks($project_id) {
        $sql = "SELECT pt.*, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM project_tasks pt
                LEFT JOIN users u ON pt.assigned_to = u.id
                WHERE pt.project_id = ?
                ORDER BY pt.due_date ASC, pt.priority DESC";
        return $this->db->fetchAll($sql, [$project_id]);
    }
    
    public function getTaskById($task_id) {
        $sql = "SELECT pt.*, p.name as project_name, p.id as project_id, u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM project_tasks pt
                LEFT JOIN projects p ON pt.project_id = p.id
                LEFT JOIN users u ON pt.assigned_to = u.id
                WHERE pt.id = ?";
        return $this->db->fetchOne($sql, [$task_id]);
    }
    
    public function updateTask($id, $data) {
        $sql = "UPDATE project_tasks SET title = ?, description = ?, assigned_to = ?, due_date = ?, status = ?, priority = ? WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['description'] ?? '',
            $data['assigned_to'] ?? null,
            $data['due_date'] ?? null,
            $data['status'] ?? 'pending',
            $data['priority'] ?? 'medium',
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    public function deleteTask($id) {
        $sql = "DELETE FROM project_tasks WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}
?>