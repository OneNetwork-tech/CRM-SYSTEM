<?php
// Projects list

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Project.php';

// Check if user is logged in
require_login();

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$priority = isset($_GET['priority']) ? $_GET['priority'] : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Get projects
$projectObj = new Project();
if ($search) {
    $projects = $projectObj->search($search);
} elseif ($status) {
    $projects = $projectObj->getByStatus($status);
} elseif ($priority) {
    $projects = $projectObj->getByPriority($priority);
} else {
    $projects = $projectObj->getAll();
}

// Get project statistics
$stats = $projectObj->getProjectStats();

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
    <title>Projekt - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Projekt</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="add.php" class="btn btn-sm btn-outline-secondary">Lägg till projekt</a>
                            <a href="calendar.php" class="btn btn-sm btn-outline-secondary">Kalender</a>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card-deck">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $stats['total_projects']; ?></h5>
                                    <p class="card-text">Totalt projekt</p>
                                </div>
                            </div>
                            <div class="card text-white bg-info">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $stats['in_progress_projects']; ?></h5>
                                    <p class="card-text">Pågående projekt</p>
                                </div>
                            </div>
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $stats['planned_projects']; ?></h5>
                                    <p class="card-text">Planerade projekt</p>
                                </div>
                            </div>
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $stats['completed_projects']; ?></h5>
                                    <p class="card-text">Slutförda projekt</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <input class="form-control" type="search" name="search" placeholder="Sök projekt..." aria-label="Sök" value="<?php echo htmlspecialchars($search); ?>">
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
                                <th>Projektnamn</th>
                                <th>Kund</th>
                                <th>Startdatum</th>
                                <th>Slutdatum</th>
                                <th>Status</th>
                                <th>Prioritet</th>
                                <th>Tilldelad till</th>
                                <th>Åtgärder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($projects)): ?>
                                <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(substr($project['name'], 0, 30)) . (strlen($project['name']) > 30 ? '...' : ''); ?></td>
                                        <td>
                                            <?php if ($project['company_name']): ?>
                                                <a href="../customers/view.php?id=<?php echo $project['customer_id']; ?>">
                                                    <?php echo htmlspecialchars($project['company_name']); ?>
                                                </a>
                                            <?php else: ?>
                                                Ej tilldelad
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $project['start_date'] ? format_date($project['start_date']) : '-'; ?></td>
                                        <td><?php echo $project['end_date'] ? format_date($project['end_date']) : '-'; ?></td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $project['status'] == 'completed' ? 'success' : 
                                                    ($project['status'] == 'in_progress' ? 'primary' : 
                                                    ($project['status'] == 'on_hold' ? 'warning' : 
                                                    ($project['status'] == 'cancelled' ? 'danger' : 'info'))); ?>">
                                                <?php echo htmlspecialchars($status_options[$project['status']] ?? $project['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $project['priority'] == 'high' ? 'danger' : 
                                                    ($project['priority'] == 'medium' ? 'warning' : 'secondary'); ?>">
                                                <?php echo htmlspecialchars($priority_options[$project['priority']] ?? $project['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($project['assigned_first_name'] && $project['assigned_last_name']): ?>
                                                <?php echo htmlspecialchars($project['assigned_first_name'] . ' ' . $project['assigned_last_name']); ?>
                                            <?php else: ?>
                                                Ej tilldelad
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="view.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                            <a href="edit.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Inga projekt hittades</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p>Totalt antal projekt: <?php echo count($projects); ?></p>
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