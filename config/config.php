<?php
// Configuration file

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'crm_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('APP_NAME', 'OneNetworkCRM Sverige');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/crm');

// Language settings
define('DEFAULT_LANGUAGE', 'sv'); // Swedish
define('CURRENCY', 'SEK'); // Swedish Krona
define('COUNTRY', 'SE'); // Sweden

// Date and time format for Sweden
define('DATE_FORMAT', 'Y-m-d'); // Swedish standard format
define('DATETIME_FORMAT', 'Y-m-d H:i'); // Swedish standard format
define('TIMEZONE', 'Europe/Stockholm'); // Sweden timezone

// Company information
define('COMPANY_NAME', 'OneNetworkCRM Sverige');
define('COMPANY_ADDRESS', 'Kungsgatan 1, 111 35 Stockholm');
define('COMPANY_PHONE', '+46 8 123 456 78');
define('COMPANY_EMAIL', 'info@onenetworkcrm.se');

// VAT settings for Sweden
define('VAT_RATE', 25); // Standard VAT rate in Sweden

// Swedish organization number format
define('ORG_NUMBER_FORMAT', 'XXXXXX-XXXX');

// Session configuration
define('SESSION_TIMEOUT', 3600); // 1 hour

// Upload settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Initialize timezone
date_default_timezone_set(TIMEZONE);
?>