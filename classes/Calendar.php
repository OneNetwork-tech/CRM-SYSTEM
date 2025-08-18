<?php
// Calendar management class

class Calendar {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function createEvent($data) {
        $sql = "INSERT INTO calendar_events (title, description, start_datetime, end_datetime, location, user_id, customer_id, contact_id, type, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['title'],
            $data['description'] ?? '',
            $data['start_datetime'],
            $data['end_datetime'] ?? null,
            $data['location'] ?? '',
            $data['user_id'],
            $data['customer_id'] ?? null,
            $data['contact_id'] ?? null,
            $data['type'] ?? 'event'
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->getLastInsertId();
    }
    
    public function getEventById($id) {
        $sql = "SELECT ce.*, u.first_name as user_first_name, u.last_name as user_last_name,
                       c.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name
                FROM calendar_events ce
                LEFT JOIN users u ON ce.user_id = u.id
                LEFT JOIN customers c ON ce.customer_id = c.id
                LEFT JOIN contacts co ON ce.contact_id = co.id
                WHERE ce.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getEventsByDateRange($start_date, $end_date, $user_id = null) {
        $sql = "SELECT ce.*, u.first_name as user_first_name, u.last_name as user_last_name,
                       c.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name
                FROM calendar_events ce
                LEFT JOIN users u ON ce.user_id = u.id
                LEFT JOIN customers c ON ce.customer_id = c.id
                LEFT JOIN contacts co ON ce.contact_id = co.id
                WHERE ce.start_datetime >= ? AND ce.start_datetime <= ?";
        
        $params = [$start_date, $end_date];
        
        if ($user_id) {
            $sql .= " AND ce.user_id = ?";
            $params[] = $user_id;
        }
        
        $sql .= " ORDER BY ce.start_datetime ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getUpcomingEvents($limit = 10, $user_id = null) {
        $sql = "SELECT ce.*, u.first_name as user_first_name, u.last_name as user_last_name,
                       c.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name
                FROM calendar_events ce
                LEFT JOIN users u ON ce.user_id = u.id
                LEFT JOIN customers c ON ce.customer_id = c.id
                LEFT JOIN contacts co ON ce.contact_id = co.id
                WHERE ce.start_datetime >= NOW()";
        
        $params = [];
        
        if ($user_id) {
            $sql .= " AND ce.user_id = ?";
            $params[] = $user_id;
        }
        
        $sql .= " ORDER BY ce.start_datetime ASC LIMIT ?";
        $params[] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function updateEvent($id, $data) {
        $sql = "UPDATE calendar_events SET title = ?, description = ?, start_datetime = ?, end_datetime = ?, 
                location = ?, customer_id = ?, contact_id = ?, type = ? WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['description'] ?? '',
            $data['start_datetime'],
            $data['end_datetime'] ?? null,
            $data['location'] ?? '',
            $data['customer_id'] ?? null,
            $data['contact_id'] ?? null,
            $data['type'] ?? 'event',
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    public function deleteEvent($id) {
        $sql = "DELETE FROM calendar_events WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function searchEvents($query, $user_id = null) {
        $sql = "SELECT ce.*, u.first_name as user_first_name, u.last_name as user_last_name,
                       c.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name
                FROM calendar_events ce
                LEFT JOIN users u ON ce.user_id = u.id
                LEFT JOIN customers c ON ce.customer_id = c.id
                LEFT JOIN contacts co ON ce.contact_id = co.id
                WHERE ce.title LIKE ? OR ce.description LIKE ? OR c.company_name LIKE ? 
                   OR co.first_name LIKE ? OR co.last_name LIKE ?";
        
        $searchTerm = "%$query%";
        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];
        
        if ($user_id) {
            $sql .= " AND ce.user_id = ?";
            $params[] = $user_id;
        }
        
        $sql .= " ORDER BY ce.start_datetime ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getEventsByType($type, $user_id = null) {
        $sql = "SELECT ce.*, u.first_name as user_first_name, u.last_name as user_last_name,
                       c.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name
                FROM calendar_events ce
                LEFT JOIN users u ON ce.user_id = u.id
                LEFT JOIN customers c ON ce.customer_id = c.id
                LEFT JOIN contacts co ON ce.contact_id = co.id
                WHERE ce.type = ?";
        
        $params = [$type];
        
        if ($user_id) {
            $sql .= " AND ce.user_id = ?";
            $params[] = $user_id;
        }
        
        $sql .= " ORDER BY ce.start_datetime ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getEventsForDay($date, $user_id = null) {
        $startOfDay = $date . ' 00:00:00';
        $endOfDay = $date . ' 23:59:59';
        
        return $this->getEventsByDateRange($startOfDay, $endOfDay, $user_id);
    }
    
    public function getEventsByDateRangeWithTasks($start_date, $end_date, $user_id = null) {
        // Get calendar events
        $events = $this->getEventsByDateRange($start_date, $end_date, $user_id);
        
        // Get tasks for the same period
        $taskSql = "SELECT t.*, cu.company_name, co.first_name as contact_first_name, co.last_name as contact_last_name, u.first_name as user_first_name, u.last_name as user_last_name
                    FROM tasks t
                    LEFT JOIN customers cu ON t.customer_id = cu.id
                    LEFT JOIN contacts co ON t.contact_id = co.id
                    LEFT JOIN users u ON t.assigned_to = u.id
                    WHERE t.due_date >= ? AND t.due_date <= ?";
        
        $taskParams = [$start_date, $end_date];
        
        if ($user_id) {
            $taskSql .= " AND t.assigned_to = ?";
            $taskParams[] = $user_id;
        }
        
        $taskSql .= " ORDER BY t.due_date ASC";
        
        $taskObj = new Task();
        $tasks = $taskObj->getByDateRange($start_date, $end_date);
        
        // Combine events and tasks
        $combined = array_merge($events, $tasks);
        
        // Sort by date
        usort($combined, function($a, $b) {
            $dateA = isset($a['start_datetime']) ? $a['start_datetime'] : $a['due_date'];
            $dateB = isset($b['start_datetime']) ? $b['start_datetime'] : $b['due_date'];
            return strtotime($dateA) - strtotime($dateB);
        });
        
        return $combined;
    }
}
?>