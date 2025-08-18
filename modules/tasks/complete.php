<?php
// Mark task as complete

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Task.php';

// Check if user is logged in
require_login();

// Get task ID from URL
$task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$task_id) {
    header('Location: index.php');
    exit;
}

// Mark task as complete
$taskObj = new Task();
$completed = $taskObj->completeTask($task_id);

if ($completed) {
    $_SESSION['message'] = 'Task marked as completed';
} else {
    $_SESSION['error'] = 'Failed to mark task as completed';
}

// Redirect back to task view or task list
if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: view.php?id=$task_id");
}
exit;
?>