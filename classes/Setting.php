<?php
// Setting management class

class Setting {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function get($key) {
        $sql = "SELECT value FROM settings WHERE setting_key = ?";
        $result = $this->db->fetchOne($sql, [$key]);
        return $result ? $result['value'] : null;
    }
    
    public function set($key, $value) {
        $sql = "INSERT INTO settings (setting_key, value) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE value = ?";
        return $this->db->execute($sql, [$key, $value, $value]);
    }
    
    public function getAll() {
        $sql = "SELECT setting_key, value FROM settings";
        $results = $this->db->fetchAll($sql);
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['value'];
        }
        
        return $settings;
    }
    
    public function getSystemSettings() {
        return [
            'company_name' => $this->get('company_name') ?? APP_NAME,
            'company_address' => $this->get('company_address') ?? '',
            'company_city' => $this->get('company_city') ?? '',
            'company_postal_code' => $this->get('company_postal_code') ?? '',
            'company_country' => $this->get('company_country') ?? 'SE',
            'company_phone' => $this->get('company_phone') ?? '',
            'company_email' => $this->get('company_email') ?? '',
            'company_organization_number' => $this->get('company_organization_number') ?? '',
            'currency' => $this->get('currency') ?? 'SEK',
            'timezone' => $this->get('timezone') ?? 'Europe/Stockholm',
            'date_format' => $this->get('date_format') ?? 'Y-m-d',
            'time_format' => $this->get('time_format') ?? 'H:i:s',
            'language' => $this->get('language') ?? 'sv',
            'week_start' => $this->get('week_start') ?? '1', // Monday
            'max_upload_size' => $this->get('max_upload_size') ?? '5242880', // 5MB
        ];
    }
    
    public function updateSystemSettings($data) {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
        return true;
    }
    
    public function getNotificationSettings($user_id) {
        $sql = "SELECT setting_key, value FROM user_settings 
                WHERE user_id = ? AND setting_key LIKE 'notification_%'";
        $results = $this->db->fetchAll($sql, [$user_id]);
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['value'];
        }
        
        return $settings;
    }
    
    public function updateNotificationSettings($user_id, $data) {
        foreach ($data as $key => $value) {
            $sql = "INSERT INTO user_settings (user_id, setting_key, value) VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE value = ?";
            $this->db->execute($sql, [$user_id, $key, $value, $value]);
        }
        return true;
    }
    
    public function getEmailSettings() {
        return [
            'smtp_host' => $this->get('smtp_host') ?? '',
            'smtp_port' => $this->get('smtp_port') ?? '587',
            'smtp_username' => $this->get('smtp_username') ?? '',
            'smtp_password' => $this->get('smtp_password') ?? '',
            'smtp_encryption' => $this->get('smtp_encryption') ?? 'tls',
            'email_from' => $this->get('email_from') ?? '',
            'email_from_name' => $this->get('email_from_name') ?? APP_NAME,
        ];
    }
    
    public function updateEmailSettings($data) {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
        return true;
    }
}
?>