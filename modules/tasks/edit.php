<?php
// Edit task details

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Task.php';
require_once '../../classes/Customer.php';
require_once '../../classes/Contact.php';
require_once '../../classes/User.php';

// Check if user is logged in
require_login();

// Get task ID from URL
$task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$task_id) {
    header('Location: index.php');
    exit;
}

// Get task details
$taskObj = new Task();
$task = $taskObj->findById($task_id);

if (!$task) {
    header('Location: index.php');
    exit;
}

// Get customers, contacts, and users for dropdowns
$customerObj = new Customer();
$customers = $customerObj->getAll();

$contactObj = new Contact();
$contacts = $contactObj->getAll();

$userObj = new User();
$users = $userObj->getAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $data = [
        'customer_id' => !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null,
        'contact_id' => !empty($_POST['contact_id']) ? intval($_POST['contact_id']) : null,
        'user_id' => $task['user_id'], // Keep original creator
        'assigned_to' => !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null,
        'title' => sanitize_input($_POST['title']),
        'description' => sanitize_input($_POST['description']),
        'priority' => sanitize_input($_POST['priority']),
        'status' => sanitize_input($_POST['status']),
        'due_date' => !empty($_POST['due_date']) ? sanitize_input($_POST['due_date']) : null
    ];
    
    // Basic validation
    if (empty($data['title'])) {
        $error = 'Task title is required';
    } else {
        // Update task
        $updated = $taskObj->update($task_id, $data);
        
        if ($updated !== false) {
            $success = 'Task updated successfully';
            // Refresh task data
            $task = $taskObj->findById($task_id);
        } else {
            $error = 'Failed to update task. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redigera uppgift - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../../includes/sidebar.php'; ?>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Redigera uppgift: <?php echo htmlspecialchars($task['title']); ?></h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($task['title']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($task['description']); ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority">Prioritet</label>
                                        <select class="form-control" id="priority" name="priority">
                                            <option value="low" <?php echo $task['priority'] === 'low' ? 'selected' : ''; ?>>Låg</option>
                                            <option value="medium" <?php echo $task['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                            <option value="high" <?php echo $task['priority'] === 'high' ? 'selected' : ''; ?>>Hög</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>Väntande</option>
                                            <option value="in_progress" <?php echo $task['status'] === 'in_progress' ? 'selected' : ''; ?>>Pågående</option>
                                            <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>Slutförd</option>
                                            <option value="cancelled" <?php echo $task['status'] === 'cancelled' ? 'selected' : ''; ?>>Avbruten</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_id">Customer</label>
                                <select class="form-control" id="customer_id" name="customer_id">
                                    <option value="">Select a Customer</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>" <?php echo $task['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($customer['company_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_id">Contact</label>
                                <select class="form-control" id="contact_id" name="contact_id">
                                    <option value="">Select a Contact</option>
                                    <?php foreach ($contacts as $contact): ?>
                                        <option value="<?php echo $contact['id']; ?>" <?php echo $task['contact_id'] == $contact['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?>
                                            <?php if ($contact['company_name']): ?>
                                                (<?php echo htmlspecialchars($contact['company_name']); ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="assigned_to">Assigned To</label>
                                <select class="form-control" id="assigned_to" name="assigned_to">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>" <?php echo $task['assigned_to'] == $user['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="due_date">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" 
                                       value="<?php echo $task['due_date'] ? date('Y-m-d', strtotime($task['due_date'])) : ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Uppdatera uppgift</button>
                    <a href="view.php?id=<?php echo $task['id']; ?>" class="btn btn-secondary">Avbryt</a>
                </form>
            </main>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/app.js"></script>
</body>
</html>