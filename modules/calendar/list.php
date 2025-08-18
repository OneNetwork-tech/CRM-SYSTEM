<?php
// List view for calendar events

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Calendar.php';

// Check if user is logged in
require_login();

// Get current user
$current_user = get_current_user_data();

// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Get calendar events
$calObj = new Calendar();
$events = $calObj->getEventsByDateRange($start_date, $end_date, $current_user['id']);

// Filter by type if specified
if ($type) {
    $events = $calObj->getEventsByType($type, $current_user['id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event List - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Event List</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="add.php" class="btn btn-sm btn-outline-secondary">Add Event</a>
                            <a href="index.php" class="btn btn-sm btn-outline-secondary">Calendar View</a>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <label for="start_date" class="mr-2">From:</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                            </div>
                            
                            <div class="form-group mr-2">
                                <label for="end_date" class="mr-2">To:</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                            </div>
                            
                            <div class="form-group mr-2">
                                <label for="type" class="mr-2">Type:</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="">All Types</option>
                                    <option value="event" <?php echo ($type == 'event') ? 'selected' : ''; ?>>Event</option>
                                    <option value="meeting" <?php echo ($type == 'meeting') ? 'selected' : ''; ?>>Meeting</option>
                                    <option value="task" <?php echo ($type == 'task') ? 'selected' : ''; ?>>Task</option>
                                    <option value="deadline" <?php echo ($type == 'deadline') ? 'selected' : ''; ?>>Deadline</option>
                                </select>
                            </div>
                            
                            <button class="btn btn-outline-success mr-2" type="submit">Filter</button>
                            <a href="list.php" class="btn btn-outline-secondary">Clear</a>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($events)): ?>
                                <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $event['type'] == 'meeting' ? 'success' : 
                                                    ($event['type'] == 'task' ? 'warning' : 
                                                    ($event['type'] == 'deadline' ? 'danger' : 'primary')); ?>">
                                                <?php echo ucfirst($event['type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo format_datetime($event['start_datetime']); ?></td>
                                        <td>
                                            <?php if ($event['end_datetime']): ?>
                                                <?php echo format_datetime($event['end_datetime']); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($event['company_name']): ?>
                                                <a href="../customers/view.php?id=<?php echo $event['customer_id']; ?>">
                                                    <?php echo htmlspecialchars($event['company_name']); ?>
                                                </a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($event['contact_first_name'] && $event['contact_last_name']): ?>
                                                <a href="../contacts/view.php?id=<?php echo $event['contact_id']; ?>">
                                                    <?php echo htmlspecialchars($event['contact_first_name'] . ' ' . $event['contact_last_name']); ?>
                                                </a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="view.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                            <a href="edit.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No events found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p>Total events: <?php echo count($events); ?></p>
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