<?php
// Edit project details

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Project.php';
require_once '../../classes/Customer.php';
require_once '../../classes/User.php';

// Check if user is logged in
require_login();

// Get project ID from URL
$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$project_id) {
    header('Location: index.php');
    exit;
}

// Get project details
$projectObj = new Project();
$project = $projectObj->findById($project_id);

if (!$project) {
    header('Location: index.php');
    exit;
}

// Get customers and users for dropdowns
$customerObj = new Customer();
$customers = $customerObj->getAll();

$userObj = new User();
$users = $userObj->getAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $data = [
        'name' => sanitize_input($_POST['name']),
        'description' => sanitize_input($_POST['description']),
        'customer_id' => !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null,
        'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
        'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
        'status' => sanitize_input($_POST['status']),
        'priority' => sanitize_input($_POST['priority']),
        'assigned_to' => !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null
    ];
    
    // Basic validation
    if (empty($data['name'])) {
        $error = 'Projektnamn är obligatoriskt';
    } else {
        // Update project
        $updated = $projectObj->update($project_id, $data);
        
        if ($updated !== false) {
            $success = 'Projekt uppdaterat';
            // Refresh project data
            $project = $projectObj->findById($project_id);
        } else {
            $error = 'Kunde inte uppdatera projekt. Försök igen.';
        }
    }
}

// Priority options
$priority_options = [
    'low' => 'Låg',
    'medium' => 'Medium',
    'high' => 'Hög'
];

// Status options
$status_options = [
    'planned' => 'Planerad',
    'in_progress' => 'Pågående',
    'on_hold' => 'Pausad',
    'completed' => 'Slutförd',
    'cancelled' => 'Avbruten'
];
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redigera projekt - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Redigera projekt: <?php echo htmlspecialchars($project['name']); ?></h1>
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
                                <label for="name">Projektnamn *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($project['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Beskrivning</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($project['description']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="customer_id">Kund</label>
                                <select class="form-control" id="customer_id" name="customer_id">
                                    <option value="">Välj kund</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>" <?php echo $project['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($customer['company_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Startdatum</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="<?php echo htmlspecialchars($project['start_date']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date">Slutdatum</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="<?php echo htmlspecialchars($project['end_date']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <?php foreach ($status_options as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" <?php echo $project['status'] == $value ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority">Prioritet</label>
                                        <select class="form-control" id="priority" name="priority">
                                            <?php foreach ($priority_options as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" <?php echo $project['priority'] == $value ? 'selected' : ''; ?>>
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
                                        <option value="<?php echo $user['id']; ?>" <?php echo $project['assigned_to'] == $user['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Uppdatera projekt</button>
                    <a href="view.php?id=<?php echo $project['id']; ?>" class="btn btn-secondary">Avbryt</a>
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