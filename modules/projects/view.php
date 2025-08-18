<?php
// View project details

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Project.php';

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

// Get project tasks
$tasks = $projectObj->getProjectTasks($project_id);

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

// Task status options
$task_status_options = [
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
    <title><?php echo htmlspecialchars($project['name']); ?> - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2"><?php echo htmlspecialchars($project['name']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="edit.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                            <a href="delete.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Är du säker på att du vill ta bort detta projekt?')">Ta bort</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Projektinformation</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Projektnamn:</th>
                                        <td><?php echo htmlspecialchars($project['name']); ?></td>
                                    </tr>
                                    <?php if ($project['description']): ?>
                                    <tr>
                                        <th>Beskrivning:</th>
                                        <td><?php echo nl2br(htmlspecialchars($project['description'])); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $project['status'] == 'completed' ? 'success' : 
                                                    ($project['status'] == 'in_progress' ? 'primary' : 
                                                    ($project['status'] == 'on_hold' ? 'warning' : 
                                                    ($project['status'] == 'cancelled' ? 'danger' : 'info'))); ?>">
                                                <?php echo htmlspecialchars($status_options[$project['status']] ?? $project['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Prioritet:</th>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $project['priority'] == 'high' ? 'danger' : 
                                                    ($project['priority'] == 'medium' ? 'warning' : 'secondary'); ?>">
                                                <?php echo htmlspecialchars($priority_options[$project['priority']] ?? $project['priority']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php if ($project['start_date']): ?>
                                    <tr>
                                        <th>Startdatum:</th>
                                        <td><?php echo format_date($project['start_date']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($project['end_date']): ?>
                                    <tr>
                                        <th>Slutdatum:</th>
                                        <td><?php echo format_date($project['end_date']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($project['company_name']): ?>
                                    <tr>
                                        <th>Kund:</th>
                                        <td>
                                            <a href="../customers/view.php?id=<?php echo $project['customer_id']; ?>">
                                                <?php echo htmlspecialchars($project['company_name']); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($project['assigned_first_name'] && $project['assigned_last_name']): ?>
                                    <tr>
                                        <th>Tilldelad till:</th>
                                        <td><?php echo htmlspecialchars($project['assigned_first_name'] . ' ' . $project['assigned_last_name']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Skapad:</th>
                                        <td><?php echo format_datetime($project['created_at']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Projektuppgifter</h5>
                                <a href="add_task.php?project_id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-primary float-right">Lägg till uppgift</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($tasks)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Uppgift</th>
                                                    <th>Tilldelad till</th>
                                                    <th>Förfallodatum</th>
                                                    <th>Status</th>
                                                    <th>Prioritet</th>
                                                    <th>Åtgärder</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($tasks as $task): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars(substr($task['title'], 0, 30)) . (strlen($task['title']) > 30 ? '...' : ''); ?></td>
                                                        <td>
                                                            <?php if ($task['assigned_first_name'] && $task['assigned_last_name']): ?>
                                                                <?php echo htmlspecialchars($task['assigned_first_name'] . ' ' . $task['assigned_last_name']); ?>
                                                            <?php else: ?>
                                                                Ej tilldelad
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $task['due_date'] ? format_date($task['due_date']) : '-'; ?></td>
                                                        <td>
                                                            <span class="badge badge-<?php 
                                                                echo $task['status'] == 'completed' ? 'success' : 
                                                                    ($task['status'] == 'in_progress' ? 'primary' : 
                                                                    ($task['status'] == 'pending' ? 'warning' : 'danger')); ?>">
                                                                <?php echo htmlspecialchars($task_status_options[$task['status']] ?? $task['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-<?php 
                                                                echo $task['priority'] == 'high' ? 'danger' : 
                                                                    ($task['priority'] == 'medium' ? 'warning' : 'secondary'); ?>">
                                                                <?php echo htmlspecialchars($priority_options[$task['priority']] ?? $task['priority']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                                                            <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                                               onclick="return confirm('Är du säker på att du vill ta bort denna uppgift?')">Ta bort</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>Inga uppgifter har skapats för detta projekt ännu.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Projektåtgärder</h5>
                            </div>
                            <div class="card-body">
                                <a href="edit.php?id=<?php echo $project['id']; ?>" class="btn btn-primary btn-block">Redigera projekt</a>
                                <a href="add_task.php?project_id=<?php echo $project['id']; ?>" class="btn btn-secondary btn-block">Lägg till uppgift</a>
                                <a href="add.php" class="btn btn-secondary btn-block">Skapa nytt projekt</a>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Projektstatistik</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2">Totalt antal uppgifter: <?php echo count($tasks); ?></li>
                                    <li class="mb-2">
                                        Slutförda uppgifter: <?php 
                                            $completed_tasks = array_filter($tasks, function($task) {
                                                return $task['status'] == 'completed';
                                            });
                                            echo count($completed_tasks);
                                        ?>
                                    </li>
                                    <li class="mb-2">
                                        Pågående uppgifter: <?php 
                                            $in_progress_tasks = array_filter($tasks, function($task) {
                                                return $task['status'] == 'in_progress';
                                            });
                                            echo count($in_progress_tasks);
                                        ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Relaterad information</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($project['customer_id']): ?>
                                    <a href="../customers/view.php?id=<?php echo $project['customer_id']; ?>" class="btn btn-outline-primary btn-block">Visa kund</a>
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