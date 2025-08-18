<?php
// Add new calendar event

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Calendar.php';
require_once '../../classes/Customer.php';
require_once '../../classes/Contact.php';

// Check if user is logged in
require_login();

// Get current user
$current_user = get_current_user_data();

// Get customers and contacts for dropdowns
$customerObj = new Customer();
$customers = $customerObj->getAll();

$contactObj = new Contact();
$contacts = $contactObj->getAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $data = [
        'title' => sanitize_input($_POST['title']),
        'description' => sanitize_input($_POST['description']),
        'start_datetime' => sanitize_input($_POST['start_date']) . ' ' . sanitize_input($_POST['start_time']),
        'end_datetime' => !empty($_POST['end_date']) ? sanitize_input($_POST['end_date']) . ' ' . sanitize_input($_POST['end_time']) : null,
        'location' => sanitize_input($_POST['location']),
        'user_id' => $current_user['id'],
        'customer_id' => !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null,
        'contact_id' => !empty($_POST['contact_id']) ? intval($_POST['contact_id']) : null,
        'type' => sanitize_input($_POST['type'])
    ];
    
    // Basic validation
    if (empty($data['title'])) {
        $error = 'Event title is required';
    } elseif (empty($_POST['start_date'])) {
        $error = 'Start date is required';
    } else {
        // Create event
        $calObj = new Calendar();
        $eventId = $calObj->createEvent($data);
        
        if ($eventId) {
            $success = 'Event created successfully';
            // Redirect to view page after short delay
            header("refresh:2;url=view.php?id=$eventId");
        } else {
            $error = 'Failed to create event. Please try again.';
        }
    }
}

// Default values
$event = [
    'title' => '',
    'description' => '',
    'start_date' => date('Y-m-d'),
    'start_time' => '09:00',
    'end_date' => date('Y-m-d'),
    'end_time' => '10:00',
    'location' => '',
    'customer_id' => '',
    'contact_id' => '',
    'type' => 'event'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Add Event</h1>
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
                                <label for="title">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($event['title']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($event['description']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="event" <?php echo $event['type'] === 'event' ? 'selected' : ''; ?>>Event</option>
                                    <option value="meeting" <?php echo $event['type'] === 'meeting' ? 'selected' : ''; ?>>Meeting</option>
                                    <option value="task" <?php echo $event['type'] === 'task' ? 'selected' : ''; ?>>Task</option>
                                    <option value="deadline" <?php echo $event['type'] === 'deadline' ? 'selected' : ''; ?>>Deadline</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?php echo htmlspecialchars($event['location']); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Start Date *</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="<?php echo htmlspecialchars($event['start_date']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_time">Start Time</label>
                                        <input type="time" class="form-control" id="start_time" name="start_time" 
                                               value="<?php echo htmlspecialchars($event['start_time']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="<?php echo htmlspecialchars($event['end_date']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_time">End Time</label>
                                        <input type="time" class="form-control" id="end_time" name="end_time" 
                                               value="<?php echo htmlspecialchars($event['end_time']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="customer_id">Customer</label>
                                <select class="form-control" id="customer_id" name="customer_id">
                                    <option value="">Select a Customer</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>" <?php echo $event['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($customer['company_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_id">Contact</label>
                                <select class="form-control" id="contact_id" name="contact_id">
                                    <option value="">Select a Contact</option>
                                    <?php foreach ($contacts as $contact): ?>
                                        <option value="<?php echo $contact['id']; ?>" <?php echo $event['contact_id'] == $contact['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?>
                                            <?php if ($contact['company_name']): ?>
                                                (<?php echo htmlspecialchars($contact['company_name']); ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Create Event</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
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