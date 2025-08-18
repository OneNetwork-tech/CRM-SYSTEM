<?php
// View task details

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

// Get task details
$taskObj = new Task();
$task = $taskObj->findById($task_id);

if (!$task) {
    header('Location: index.php');
    exit;
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
    <title><?php echo htmlspecialchars($task['title']); ?> - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../../includes/sidebar.php'; ?>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo htmlspecialchars($task['title']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="edit.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                            <a href="delete.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Är du säker på att du vill ta bort denna uppgift?')">Ta bort</a>
                            <?php if ($task['status'] != 'completed'): ?>
                                <a href="complete.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-outline-success">Markera som slutförd</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Uppgiftsdetaljer</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Titel:</th>
                                        <td><?php echo htmlspecialchars($task['title']); ?></td>
                                    </tr>
                                    <?php if ($task['description']): ?>
                                    <tr>
                                        <th>Beskrivning:</th>
                                        <td><?php echo nl2br(htmlspecialchars($task['description'])); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Prioritet:</th>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $task['priority'] == 'high' ? 'danger' : 
                                                    ($task['priority'] == 'medium' ? 'warning' : 'secondary'); ?>">
                                                <?php echo htmlspecialchars($priority_options[$task['priority']] ?? $task['priority']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $task['status'] == 'completed' ? 'success' : 
                                                    ($task['status'] == 'in_progress' ? 'primary' : 
                                                    ($task['status'] == 'pending' ? 'warning' : 'danger')); ?>">
                                                <?php echo htmlspecialchars($status_options[$task['status']] ?? $task['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php if ($task['due_date']): ?>
                                    <tr>
                                        <th>Förfallodatum:</th>
                                        <td>
                                            <?php echo format_date($task['due_date']); ?>
                                            <?php if (strtotime($task['due_date']) < time() && $task['status'] != 'completed'): ?>
                                                <span class="badge badge-danger">Försenad</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($task['company_name']): ?>
                                    <tr>
                                        <th>Kund:</th>
                                        <td>
                                            <a href="../customers/view.php?id=<?php echo $task['customer_id']; ?>">
                                                <?php echo htmlspecialchars($task['company_name']); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($task['contact_first_name'] && $task['contact_last_name']): ?>
                                    <tr>
                                        <th>Kontakt:</th>
                                        <td>
                                            <a href="../contacts/view.php?id=<?php echo $task['contact_id']; ?>">
                                                <?php echo htmlspecialchars($task['contact_first_name'] . ' ' . $task['contact_last_name']); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($task['assigned_first_name'] && $task['assigned_last_name']): ?>
                                    <tr>
                                        <th>Tilldelad till:</th>
                                        <td><?php echo htmlspecialchars($task['assigned_first_name'] . ' ' . $task['assigned_last_name']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Skapad:</th>
                                        <td><?php echo format_datetime($task['created_at']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Uppgiftsåtgärder</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($task['status'] != 'completed'): ?>
                                    <a href="complete.php?id=<?php echo $task['id']; ?>" class="btn btn-success btn-block">Markera som slutförd</a>
                                <?php else: ?>
                                    <div class="alert alert-success">Denna uppgift är slutförd</div>
                                <?php endif; ?>
                                
                                <a href="edit.php?id=<?php echo $task['id']; ?>" class="btn btn-primary btn-block">Redigera uppgift</a>
                                <a href="add.php" class="btn btn-secondary btn-block">Lägg till ny uppgift</a>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Relaterad information</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($task['customer_id']): ?>
                                    <a href="../customers/view.php?id=<?php echo $task['customer_id']; ?>" class="btn btn-outline-primary btn-block">Visa kund</a>
                                <?php endif; ?>
                                
                                <?php if ($task['contact_id']): ?>
                                    <a href="../contacts/view.php?id=<?php echo $task['contact_id']; ?>" class="btn btn-outline-primary btn-block">Visa kontakt</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/app.js"></script>
</body>
</html>