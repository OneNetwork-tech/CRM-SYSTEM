<?php
// View calendar event details

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Calendar.php';

// Check if user is logged in
require_login();

// Get event ID from URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$event_id) {
    header('Location: index.php');
    exit;
}

// Get event details
$calObj = new Calendar();
$event = $calObj->getEventById($event_id);

if (!$event) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2"><?php echo htmlspecialchars($event['title']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="edit.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <a href="delete.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Event Details</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Title:</th>
                                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Description:</th>
                                        <td><?php echo nl2br(htmlspecialchars($event['description'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Type:</th>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $event['type'] == 'meeting' ? 'success' : 
                                                    ($event['type'] == 'task' ? 'warning' : 
                                                    ($event['type'] == 'deadline' ? 'danger' : 'primary')); ?>">
                                                <?php echo ucfirst($event['type']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Start:</th>
                                        <td><?php echo format_datetime($event['start_datetime']); ?></td>
                                    </tr>
                                    <?php if ($event['end_datetime']): ?>
                                    <tr>
                                        <th>End:</th>
                                        <td><?php echo format_datetime($event['end_datetime']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($event['location']): ?>
                                    <tr>
                                        <th>Location:</th>
                                        <td><?php echo htmlspecialchars($event['location']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Created:</th>
                                        <td><?php echo format_datetime($event['created_at']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Related Information</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($event['company_name']): ?>
                                    <h6>Customer</h6>
                                    <p>
                                        <a href="../customers/view.php?id=<?php echo $event['customer_id']; ?>">
                                            <?php echo htmlspecialchars($event['company_name']); ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($event['contact_first_name'] && $event['contact_last_name']): ?>
                                    <h6>Contact</h6>
                                    <p>
                                        <a href="../contacts/view.php?id=<?php echo $event['contact_id']; ?>">
                                            <?php echo htmlspecialchars($event['contact_first_name'] . ' ' . $event['contact_last_name']); ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                                
                                <h6>Created By</h6>
                                <p>
                                    <?php echo htmlspecialchars($event['user_first_name'] . ' ' . $event['user_last_name']); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-body">
                                <a href="edit.php?id=<?php echo $event['id']; ?>" class="btn btn-primary btn-block">Edit Event</a>
                                <a href="delete.php?id=<?php echo $event['id']; ?>" class="btn btn-danger btn-block" 
                                   onclick="return confirm('Are you sure you want to delete this event?')">Delete Event</a>
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