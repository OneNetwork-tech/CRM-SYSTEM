<?php
// Global helper functions

function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function format_date($date) {
    if (empty($date) || $date == '0000-00-00') {
        return '-';
    }
    
    // Format date according to Swedish standards (YYYY-MM-DD)
    $timestamp = strtotime($date);
    return date('Y-m-d', $timestamp);
}

function format_datetime($datetime) {
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
        return '-';
    }
    
    // Format datetime according to Swedish standards
    $timestamp = strtotime($datetime);
    return date('Y-m-d H:i', $timestamp);
}

function generate_password($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $password;
}

function send_email($to, $subject, $message) {
    // Email sending function
    // In a real application, you would use PHPMailer or similar
    $headers = "From: " . COMPANY_EMAIL . "\r\n";
    $headers .= "Reply-To: " . COMPANY_EMAIL . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // For Swedish characters
    $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    
    return mail($to, $subject, $message, $headers);
}

function log_activity($user_id, $action, $details = '') {
    $log_message = date('Y-m-d H:i:s') . " - User ID: $user_id - Action: $action - Details: $details\n";
    file_put_contents('../logs/user-activity.log', $log_message, FILE_APPEND);
}

function format_currency($amount) {
    // Format currency for Swedish Krona
    return number_format($amount, 2, ',', ' ') . ' kr';
}

function format_phone($phone) {
    // Format Swedish phone numbers
    // Remove all non-digit characters
    $phone = preg_replace('/\D/', '', $phone);
    
    // Add Swedish country code if missing
    if (substr($phone, 0, 2) !== '46' && strlen($phone) === 10) {
        $phone = '46' . substr($phone, 1);
    }
    
    // Format as +46 XX XXX XX XX
    if (strlen($phone) === 11 && substr($phone, 0, 2) === '46') {
        return '+' . substr($phone, 0, 2) . ' ' . 
               substr($phone, 2, 2) . ' ' . 
               substr($phone, 4, 3) . ' ' . 
               substr($phone, 7, 2) . ' ' . 
               substr($phone, 9, 2);
    }
    
    return $phone;
}

function validate_org_number($org_number) {
    // Validate Swedish organization number format (XXXXXX-XXXX)
    return preg_match('/^\d{6}-\d{4}$/', $org_number);
}

function get_swedish_counties() {
    // Return list of Swedish counties
    return [
        'Blekinge län',
        'Dalarnas län',
        'Gotlands län',
        'Gävleborgs län',
        'Hallands län',
        'Jämtlands län',
        'Jönköpings län',
        'Kalmar län',
        'Kronobergs län',
        'Norrbottens län',
        'Skåne län',
        'Stockholms län',
        'Södermanlands län',
        'Uppsala län',
        'Värmlands län',
        'Västerbottens län',
        'Västernorrlands län',
        'Västmanlands län',
        'Västra Götalands län',
        'Örebro län',
        'Östergötlands län'
    ];
}

function get_swedish_municipalities() {
    // Return some major Swedish municipalities
    return [
        'Stockholm',
        'Göteborg',
        'Malmö',
        'Uppsala',
        'Västerås',
        'Örebro',
        'Linköping',
        'Helsingborg',
        'Jönköping',
        'Norrköping',
        'Lund',
        'Umeå',
        'Gävle',
        'Borås',
        'Södertälje',
        'Eskilstuna',
        'Karlstad',
        'Halmstad',
        'Växjö',
        'Sundsvall'
    ];
}

function get_gravatar($email, $size = 80) {
    $default = urlencode(BASE_URL . '/assets/images/default-avatar.png');
    return "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?d=" . $default . "&s=" . $size;
}

?>