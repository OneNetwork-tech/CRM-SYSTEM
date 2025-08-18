<?php
// Document management class

class Document {
    private $db;
    private $uploadDir;
    
    public function __construct() {
        $this->db = new Database();
        $this->uploadDir = dirname(__DIR__) . '/uploads/documents/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    public function create($data, $file) {
        // Handle file upload
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];
        
        // Generate unique filename
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueFileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $uploadPath = $this->uploadDir . $uniqueFileName;
        
        // Move uploaded file
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            $sql = "INSERT INTO documents (customer_id, name, file_path, file_type, file_size, uploaded_by, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $params = [
                $data['customer_id'] ?? null,
                $data['name'] ?? $fileName,
                $uniqueFileName,
                $fileType,
                $fileSize,
                $data['uploaded_by']
            ];
            
            $this->db->execute($sql, $params);
            return $this->db->getLastInsertId();
        }
        
        return false;
    }
    
    public function findById($id) {
        $sql = "SELECT d.*, cu.company_name, u.first_name as uploaded_first_name, u.last_name as uploaded_last_name
                FROM documents d
                LEFT JOIN customers cu ON d.customer_id = cu.id
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getAll() {
        $sql = "SELECT d.*, c.company_name, u.first_name as uploaded_first_name, u.last_name as uploaded_last_name 
                FROM documents d 
                LEFT JOIN customers c ON d.customer_id = c.id 
                LEFT JOIN users u ON d.uploaded_by = u.id 
                ORDER BY d.created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function getByCustomerId($customer_id) {
        $sql = "SELECT d.*, c.company_name, u.first_name as uploaded_first_name, u.last_name as uploaded_last_name 
                FROM documents d 
                LEFT JOIN customers c ON d.customer_id = c.id 
                LEFT JOIN users u ON d.uploaded_by = u.id 
                WHERE d.customer_id = ? 
                ORDER BY d.created_at DESC";
        return $this->db->fetchAll($sql, [$customer_id]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE documents SET customer_id = ?, name = ? WHERE id = ?";
        
        $params = [
            $data['customer_id'] ?? null,
            $data['name'],
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        // Get document to delete file
        $document = $this->findById($id);
        if ($document) {
            $filePath = $this->uploadDir . $document['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $sql = "DELETE FROM documents WHERE id = ?";
            return $this->db->execute($sql, [$id]);
        }
        
        return false;
    }
    
    public function search($query) {
        $sql = "SELECT d.*, c.company_name, u.first_name as uploaded_first_name, u.last_name as uploaded_last_name 
                FROM documents d 
                LEFT JOIN customers c ON d.customer_id = c.id 
                LEFT JOIN users u ON d.uploaded_by = u.id 
                WHERE d.name LIKE ? OR c.company_name LIKE ?
                ORDER BY d.created_at DESC";
        
        $searchTerm = "%$query%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm]);
    }
    
    public function getByFileType($file_type) {
        $sql = "SELECT d.*, cu.company_name, u.first_name as uploaded_first_name, u.last_name as uploaded_last_name
                FROM documents d
                LEFT JOIN customers cu ON d.customer_id = cu.id
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.file_type LIKE ?
                ORDER BY d.created_at DESC";
        return $this->db->fetchAll($sql, ["%$file_type%"]);
    }
    
    public function getDocumentStats() {
        $sql = "SELECT 
                    COUNT(*) as total_documents,
                    SUM(file_size) as total_size,
                    COUNT(CASE WHEN file_type LIKE 'application/pdf%' THEN 1 END) as pdf_count,
                    COUNT(CASE WHEN file_type LIKE 'image/%' THEN 1 END) as image_count,
                    COUNT(CASE WHEN file_type LIKE 'application/msword%' OR file_type LIKE 'application/vnd.openxmlformats-officedocument.wordprocessingml.document%' THEN 1 END) as word_count
                FROM documents";
        
        return $this->db->fetchOne($sql);
    }
    public function getDocumentsByDateRange($start_date, $end_date) {
        $sql = "SELECT d.*, cu.company_name, u.first_name as uploaded_first_name, u.last_name as uploaded_last_name
                FROM documents d
                LEFT JOIN customers cu ON d.customer_id = cu.id
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.created_at >= ? AND d.created_at <= ?
                ORDER BY d.created_at DESC";
        
        return $this->db->fetchAll($sql, [$start_date, $end_date]);
    }
}
?>