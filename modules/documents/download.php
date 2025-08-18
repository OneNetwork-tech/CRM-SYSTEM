<?php
// Secure file download

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Document.php';

// Check if user is logged in
require_login();

// Get document ID from URL
$doc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$doc_id) {
    header('Location: index.php');
    exit;
}

// Get document details
$docObj = new Document();
$document = $docObj->findById($doc_id);

if (!$document) {
    header('Location: index.php');
    exit;
}

// Get the full file path
$file_path = '../../' . $document['file_path'];

// Check if file exists
if (!file_exists($file_path)) {
    header('Location: index.php?error=file_not_found');
    exit;
}

// Get file info
$file_name = $document['name'] . '.' . pathinfo($file_path, PATHINFO_EXTENSION);
$file_size = filesize($file_path);
$file_type = mime_content_type($file_path);

// Set headers for download
header('Content-Description: File Transfer');
header('Content-Type: ' . $file_type);
header('Content-Disposition: attachment; filename="' . basename($document['file_path']) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . $file_size);

// Clear output buffer
ob_clean();
flush();

// Read and output the file
readfile($file_path);
exit;
?>