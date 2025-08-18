<?php
// Report management class

class Report {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getCustomerReport($start_date = null, $end_date = null) {
        $sql = "SELECT 
                    COUNT(*) as total_customers,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_customers,
                    COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_customers,
                    COUNT(CASE WHEN created_at >= ? AND created_at <= ? THEN 1 END) as new_customers_period
                FROM customers";
        
        $params = [
            $start_date ?? date('Y-m-01'),
            $end_date ?? date('Y-m-t')
        ];
        
        return $this->db->fetchOne($sql, $params);
    }
    
    public function getLeadReport($start_date = null, $end_date = null) {
        $sql = "SELECT 
                    COUNT(*) as total_leads,
                    COUNT(CASE WHEN status = 'new' THEN 1 END) as new_leads,
                    COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted_leads,
                    COUNT(CASE WHEN status = 'qualified' THEN 1 END) as qualified_leads,
                    COUNT(CASE WHEN status = 'lost' THEN 1 END) as lost_leads,
                    COUNT(CASE WHEN status = 'converted' THEN 1 END) as converted_leads,
                    COUNT(CASE WHEN created_at >= ? AND created_at <= ? THEN 1 END) as new_leads_period
                FROM leads";
        
        $params = [
            $start_date ?? date('Y-m-01'),
            $end_date ?? date('Y-m-t')
        ];
        
        return $this->db->fetchOne($sql, $params);
    }
    
    public function getCommunicationReport($start_date = null, $end_date = null) {
        $sql = "SELECT 
                    COUNT(*) as total_communications,
                    COUNT(CASE WHEN type = 'email' THEN 1 END) as email_count,
                    COUNT(CASE WHEN type = 'call' THEN 1 END) as call_count,
                    COUNT(CASE WHEN type = 'meeting' THEN 1 END) as meeting_count,
                    COUNT(CASE WHEN type = 'note' THEN 1 END) as note_count,
                    COUNT(CASE WHEN direction = 'inbound' THEN 1 END) as inbound_count,
                    COUNT(CASE WHEN direction = 'outbound' THEN 1 END) as outbound_count,
                    COUNT(CASE WHEN created_at >= ? AND created_at <= ? THEN 1 END) as communications_period
                FROM communications";
        
        $params = [
            $start_date ?? date('Y-m-01'),
            $end_date ?? date('Y-m-t')
        ];
        
        return $this->db->fetchOne($sql, $params);
    }
    
    public function getTaskReport($start_date = null, $end_date = null) {
        $sql = "SELECT 
                    COUNT(*) as total_tasks,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_tasks,
                    COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress_tasks,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_tasks,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_tasks,
                    COUNT(CASE WHEN due_date < CURDATE() AND status != 'completed' THEN 1 END) as overdue_tasks,
                    COUNT(CASE WHEN created_at >= ? AND created_at <= ? THEN 1 END) as new_tasks_period
                FROM tasks";
        
        $params = [
            $start_date ?? date('Y-m-01'),
            $end_date ?? date('Y-m-t')
        ];
        
        return $this->db->fetchOne($sql, $params);
    }
    
    public function getDocumentReport($start_date = null, $end_date = null) {
        $sql = "SELECT 
                    COUNT(*) as total_documents,
                    SUM(file_size) as total_size,
                    COUNT(CASE WHEN file_type LIKE 'application/pdf%' THEN 1 END) as pdf_count,
                    COUNT(CASE WHEN file_type LIKE 'image/%' THEN 1 END) as image_count,
                    COUNT(CASE WHEN file_type LIKE 'application/msword%' OR file_type LIKE 'application/vnd.openxmlformats-officedocument.wordprocessingml.document%' THEN 1 END) as word_count,
                    COUNT(CASE WHEN created_at >= ? AND created_at <= ? THEN 1 END) as new_documents_period
                FROM documents";
        
        $params = [
            $start_date ?? date('Y-m-01'),
            $end_date ?? date('Y-m-t')
        ];
        
        return $this->db->fetchOne($sql, $params);
    }
    
    public function getCalendarReport($start_date = null, $end_date = null) {
        $sql = "SELECT 
                    COUNT(*) as total_events,
                    COUNT(CASE WHEN type = 'event' THEN 1 END) as event_count,
                    COUNT(CASE WHEN type = 'meeting' THEN 1 END) as meeting_count,
                    COUNT(CASE WHEN type = 'task' THEN 1 END) as task_count,
                    COUNT(CASE WHEN type = 'deadline' THEN 1 END) as deadline_count,
                    COUNT(CASE WHEN start_datetime >= ? AND start_datetime <= ? THEN 1 END) as events_period
                FROM calendar_events";
        
        $params = [
            $start_date ?? date('Y-m-01'),
            $end_date ?? date('Y-m-t')
        ];
        
        return $this->db->fetchOne($sql, $params);
    }
    
    public function getCustomerGrowthData($months = 12) {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as new_customers
                FROM customers 
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC";
        
        return $this->db->fetchAll($sql, [$months]);
    }
    
    public function getLeadConversionData($months = 12) {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as total_leads,
                    COUNT(CASE WHEN status = 'converted' THEN 1 END) as converted_leads
                FROM leads 
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC";
        
        return $this->db->fetchAll($sql, [$months]);
    }
    
    public function getSalesReport($start_date = null, $end_date = null) {
        // This would be implemented when we add sales functionality
        return [
            'total_sales' => 0,
            'sales_count' => 0,
            'average_sale' => 0
        ];
    }
    
    public function getTopCustomers($limit = 10) {
        // This would be implemented when we add sales functionality
        return [];
    }
    
    public function getUserActivityReport($user_id = null, $start_date = null, $end_date = null) {
        $sql = "SELECT 
                    u.id,
                    u.first_name,
                    u.last_name,
                    COUNT(c.id) as communications_count,
                    COUNT(t.id) as tasks_count,
                    COUNT(ce.id) as events_count
                FROM users u
                LEFT JOIN communications c ON u.id = c.user_id AND c.created_at >= ? AND c.created_at <= ?
                LEFT JOIN tasks t ON u.id = t.assigned_to AND t.created_at >= ? AND t.created_at <= ?
                LEFT JOIN calendar_events ce ON u.id = ce.user_id AND ce.created_at >= ? AND ce.created_at <= ?
                WHERE u.role != 'admin'";
        
        $params = [
            $start_date ?? date('Y-m-01'),
            $end_date ?? date('Y-m-t'),
            $start_date ?? date('Y-m-01'),
            $end_date ?? date('Y-m-t'),
            $start_date ?? date('Y-m-01'),
            $end_date ?? date('Y-m-t')
        ];
        
        if ($user_id) {
            $sql .= " AND u.id = ?";
            $params[] = $user_id;
        }
        
        $sql .= " GROUP BY u.id, u.first_name, u.last_name ORDER BY communications_count + tasks_count + events_count DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getLeadSourceReport($start_date = null, $end_date = null) {
        $sql = "SELECT 
                    source,
                    COUNT(*) as lead_count,
                    COUNT(CASE WHEN status = 'converted' THEN 1 END) as converted_count
                FROM leads 
                WHERE created_at >= ? AND created_at <= ?
                GROUP BY source
                ORDER BY lead_count DESC";
        
        $params = [
            $start_date ?? date('Y-m-01'),
            $end_date ?? date('Y-m-t')
        ];
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getTaskCompletionReport($start_date = null, $end_date = null) {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m-%d') as date,
                    COUNT(*) as total_tasks,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_tasks
                FROM tasks 
                WHERE created_at >= ? AND created_at <= ?
                GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d')
                ORDER BY date ASC";
        
        $params = [
            $start_date ?? date('Y-m-01'),
            $end_date ?? date('Y-m-t')
        ];
        
        return $this->db->fetchAll($sql, $params);
    }
}
?>