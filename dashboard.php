<?php
// Main dashboard after login

// Start session and include required files
session_start();
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../classes/Calendar.php'; // Add calendar class

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: index.php');
    exit;
}

// Get current user
$current_user = get_current_user_data();

// Get upcoming calendar events for this user
$calObj = new Calendar();
$events = $calObj->getUpcomingEvents(5, $current_user['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <div class="row">
                    <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card-deck">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo count($customers); ?></h5>
                                    <p class="card-text">Kunder</p>
                                </div>
                            </div>
                            <div class="card text-white bg-info">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $leads['total_leads']; ?></h5>
                                    <p class="card-text">Leads</p>
                                </div>
                            </div>
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo count($tasks); ?></h5>
                                    <p class="card-text">Dagens uppgifter</p>
                                </div>
                            </div>
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $leads['converted_leads']; ?></h5>
                                    <p class="card-text">Konverterade leads</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Senaste aktiviteter</h5>
                            </div>
                            <div class="card-body">
                                <p>Senaste kund-, lead- och uppgiftsaktiviteterna visas här.</p>
                                <a href="modules/reports/" class="btn btn-primary">Visa rapporter</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Dagens uppgifter</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                <?php if (!empty($tasks)): ?>
                                    <div class="list-group">
                                        <?php foreach ($tasks as $task): ?>
                                            <a href="modules/tasks/view.php?id=<?php echo $task['id']; ?>" 
                                               class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($task['title']); ?></h6>
                                                    <small>
                                                        <span class="badge badge-<?php 
                                                            echo $task['priority'] == 'high' ? 'danger' : 
                                                                ($task['priority'] == 'medium' ? 'warning' : 'secondary'); ?>">
                                                            <?php echo ucfirst($task['priority']); ?>
                                                        </span>
                                                    </small>
                                                </div>
                                                <small>
                                                    <?php if ($task['company_name']): ?>
                                                        <?php echo htmlspecialchars($task['company_name']); ?>
                                                    <?php elseif ($task['contact_first_name'] && $task['contact_last_name']): ?>
                                                        <?php echo htmlspecialchars($task['contact_first_name'] . ' ' . $task['contact_last_name']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p>Inga uppgifter idag.</p>
                                <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                Upcoming Events
                            </div>
                            <div class="card-body">
                                <?php if (!empty($events)): ?>
                                    <div class="list-group">
                                        <?php foreach ($events as $event): ?>
                                            <a href="modules/calendar/view.php?id=<?php echo $event['id']; ?>" 
                                               class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                                    <small>
                                                        <span class="badge badge-<?php 
                                                            echo $event['type'] == 'meeting' ? 'success' : 
                                                                ($event['type'] == 'task' ? 'warning' : 
                                                                ($event['type'] == 'deadline' ? 'danger' : 'primary')); ?>">
                                                            <?php echo ucfirst($event['type']); ?>
                                                        </span>
                                                    </small>
                                                </div>
                                                <small><?php echo format_datetime($event['start_datetime']); ?></small>
                                                <br>
                                                <small>
                                                    <?php if ($event['company_name']): ?>
                                                        <?php echo htmlspecialchars($event['company_name']); ?>
                                                    <?php elseif ($event['contact_first_name'] && $event['contact_last_name']): ?>
                                                        <?php echo htmlspecialchars($event['contact_first_name'] . ' ' . $event['contact_last_name']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p>No upcoming events.</p>
                                <?php endif; ?>
                                <a href="modules/calendar/" class="btn btn-primary btn-sm mt-2">Visa kalender</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>