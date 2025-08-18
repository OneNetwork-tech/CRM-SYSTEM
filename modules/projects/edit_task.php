<?php
// Edit project task

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Project.php';
require_once '../../classes/User.php';

// Check if user is logged in
require_login();

// Get task ID from URL
$task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$task_id) {
    header('Location: index.php');
    exit;
}

// Get project object
$projectObj = new Project();

// Get task details using the new method
$task = $projectObj->getTaskById($task_id);

if (!$task) {
    header('Location: index.php');
    exit;
}

// Get users for dropdown
$userObj = new User();
$users = $userObj->getAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $data = [
        'title' => sanitize_input($_POST['title']),
        'description' => sanitize_input($_POST['description']),
        'assigned_to' => !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null,
        'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null,
        'status' => sanitize_input($_POST['status']),
        'priority' => sanitize_input($_POST['priority'])
    ];
    
    // Basic validation
    if (empty($data['title'])) {
        $error = 'Uppgiftstitel är obligatorisk';
    } else {
        // Update project task
        $updated = $projectObj->updateTask($task_id, $data);
        
        if ($updated !== false) {
            $success = 'Projektuppgift uppdaterad';
            // Refresh task data
            $task = $projectObj->getTaskById($task_id);
        } else {
            $error = 'Kunde inte uppdatera projektuppgift. Försök igen.';
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
    <title>Redigera projektuppgift - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Redigera projektuppgift</h1>
                    <div>
                        <h5>Projekt: <?php echo htmlspecialchars($task['project_name']); ?></h5>
                    </div>
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
                                <label for="title">Uppgiftstitel *</label>
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
                            
                            <div class="form-group">
                                <label for="due_date">Förfallodatum</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" 
                                       value="<?php echo htmlspecialchars($task['due_date']); ?>">
                            </div>
                            
                            <div class="form-row">
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
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Uppdatera projektuppgift</button>
                    <a href="view.php?id=<?php echo $task['project_id']; ?>" class="btn btn-secondary">Avbryt</a>
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