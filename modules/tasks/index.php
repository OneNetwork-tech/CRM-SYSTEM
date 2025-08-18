<?php
// Tasks list

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Task.php';

// Check if user is logged in
require_login();

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$priority = isset($_GET['priority']) ? $_GET['priority'] : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Get tasks
$taskObj = new Task();
if ($search) {
    $tasks = $taskObj->search($search);
} elseif ($status) {
    $tasks = $taskObj->getByStatus($status);
} elseif ($priority) {
    $tasks = $taskObj->getByPriority($priority);
} else {
    $tasks = $taskObj->getAll();
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
    <title>Uppgifter - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../../includes/sidebar.php'; ?>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Uppgifter</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="add.php" class="btn btn-sm btn-outline-secondary">Lägg till uppgift</a>
                            <a href="calendar.php" class="btn btn-sm btn-outline-secondary">Kalender</a>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <input class="form-control" type="search" name="search" placeholder="Sök uppgifter..." aria-label="Sök" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            
                            <div class="form-group mr-2">
                                <select class="form-control" name="status">
                                    <option value="">Alla status</option>
                                    <?php foreach ($status_options as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $status === $value ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group mr-2">
                                <select class="form-control" name="priority">
                                    <option value="">Alla prioriteringar</option>
                                    <?php foreach ($priority_options as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $priority === $value ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button class="btn btn-outline-success mr-2" type="submit">Filtrera</button>
                            <a href="index.php" class="btn btn-outline-secondary">Rensa</a>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Titel</th>
                                <th>Kund/Kontakt</th>
                                <th>Förfallodatum</th>
                                <th>Prioritet</th>
                                <th>Status</th>
                                <th>Tilldelad till</th>
                                <th>Åtgärder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tasks)): ?>
                                <?php foreach ($tasks as $task): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(substr($task['title'], 0, 30)) . (strlen($task['title']) > 30 ? '...' : ''); ?></td>
                                        <td>
                                            <?php if ($task['company_name']): ?>
                                                <a href="../customers/view.php?id=<?php echo $task['customer_id']; ?>">
                                                    <?php echo htmlspecialchars($task['company_name']); ?>
                                                </a>
                                            <?php elseif ($task['contact_first_name'] && $task['contact_last_name']): ?>
                                                <a href="../contacts/view.php?id=<?php echo $task['contact_id']; ?>">
                                                    <?php echo htmlspecialchars($task['contact_first_name'] . ' ' . $task['contact_last_name']); ?>
                                                </a>
                                            <?php else: ?>
                                                Ej tilldelad
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo $task['due_date'] ? format_date($task['due_date']) : '-'; ?>
                                            <?php if ($task['due_date'] && strtotime($task['due_date']) < time() && $task['status'] != 'completed'): ?>
                                                <span class="badge badge-danger">Försenad</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $task['priority'] == 'high' ? 'danger' : 
                                                    ($task['priority'] == 'medium' ? 'warning' : 'secondary'); ?>">
                                                <?php echo htmlspecialchars($priority_options[$task['priority']] ?? $task['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $task['status'] == 'completed' ? 'success' : 
                                                    ($task['status'] == 'in_progress' ? 'primary' : 
                                                    ($task['status'] == 'pending' ? 'warning' : 'danger')); ?>">
                                                <?php echo htmlspecialchars($status_options[$task['status']] ?? $task['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($task['assigned_first_name'] && $task['assigned_last_name']): ?>
                                                <?php echo htmlspecialchars($task['assigned_first_name'] . ' ' . $task['assigned_last_name']); ?>
                                            <?php else: ?>
                                                Ej tilldelad
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="view.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                            <a href="edit.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Inga uppgifter hittades</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p>Totalt antal uppgifter: <?php echo count($tasks); ?></p>
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