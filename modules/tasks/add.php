<?php
// Add new task

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

// Get current user
$current_user = get_current_user_data();

// Check if customer_id or contact_id is passed in URL
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$contact_id = isset($_GET['contact_id']) ? intval($_GET['contact_id']) : 0;

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
        'title' => sanitize_input($_POST['title']),
        'description' => sanitize_input($_POST['description']),
        'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null,
        'priority' => sanitize_input($_POST['priority']),
        'status' => sanitize_input($_POST['status']),
        'assigned_to' => !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null
    ];
    
    // Basic validation
    if (empty($data['title'])) {
        $error = 'Titel är obligatorisk';
    } else {
        // Create task
        $taskObj = new Task();
        $taskId = $taskObj->create($data);
        
        if ($taskId) {
            $success = 'Uppgift skapad';
            // Redirect to view page after short delay
            header("refresh:2;url=view.php?id=$taskId");
        } else {
            $error = 'Kunde inte skapa uppgift. Försök igen.';
        }
    }
}

// Default values
$task = [
    'customer_id' => $customer_id,
    'contact_id' => $contact_id,
    'title' => '',
    'description' => '',
    'due_date' => '',
    'priority' => 'medium',
    'status' => 'pending',
    'assigned_to' => $current_user['id']
];

// Priority options
$priority_options = [
    'low' => 'Låg',
    'medium' => 'Medium',
    'high' => 'Hög'
];

// Status options
$status_options = [
    'pending' => 'Väntande',
    'in_progress' => 'Pågående',
    'completed' => 'Slutförd',
    'cancelled' => 'Avbruten'
];
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lägg till uppgift - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Lägg till uppgift</h1>
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
                                <label for="customer_id">Kund</label>
                                <select class="form-control" id="customer_id" name="customer_id">
                                    <option value="">Välj kund</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>" <?php echo $task['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($customer['company_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_id">Kontakt</label>
                                <select class="form-control" id="contact_id" name="contact_id">
                                    <option value="">Välj kontakt</option>
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
                                <label for="title">Titel *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($task['title']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Beskrivning</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($task['description']); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="due_date">Förfallodatum</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" 
                                       value="<?php echo htmlspecialchars($task['due_date']); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority">Prioritet</label>
                                        <select class="form-control" id="priority" name="priority">
                                            <?php foreach ($priority_options as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" <?php echo $task['priority'] == $value ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <?php foreach ($status_options as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" <?php echo $task['status'] == $value ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="assigned_to">Tilldelad till</label>
                                <select class="form-control" id="assigned_to" name="assigned_to">
                                    <option value="">Ej tilldelad</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>" <?php echo $task['assigned_to'] == $user['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Skapa uppgift</button>
                    <a href="index.php" class="btn btn-secondary">Avbryt</a>
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