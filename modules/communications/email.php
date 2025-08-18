<?php
// Send email interface

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Communication.php';
require_once '../../classes/Customer.php';
require_once '../../classes/Contact.php';

// Check if user is logged in
require_login();

// Get current user
$current_user = get_current_user_data();

// Check if we're pre-filling form based on customer or contact
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$contact_id = isset($_GET['contact_id']) ? intval($_GET['contact_id']) : 0;

// Get customer and contact details if specified
$customer = null;
$contact = null;

if ($customer_id) {
    $customerObj = new Customer();
    $customer = $customerObj->findById($customer_id);
}

if ($contact_id) {
    $contactObj = new Contact();
    $contact = $contactObj->findById($contact_id);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process email form submission
    $to = sanitize_input($_POST['to']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);
    
    // Basic validation
    if (empty($to) || empty($subject) || empty($message)) {
        $error = 'All fields are required';
    } elseif (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        // Send email (in a real application, you would use PHPMailer or similar)
        $sent = send_email($to, $subject, $message);
        
        if ($sent) {
            $success = 'Email sent successfully';
            
            // Log the communication
            $commObj = new Communication();
            $commData = [
                'customer_id' => $customer_id ?: null,
                'contact_id' => $contact_id ?: null,
                'user_id' => $current_user['id'],
                'type' => 'email',
                'subject' => $subject,
                'description' => $message,
                'direction' => 'outbound',
                'status' => 'completed'
            ];
            
            $commId = $commObj->create($commData);
            
            // Redirect after short delay
            header("refresh:2;url=view.php?id=$commId");
        } else {
            $error = 'Failed to send email. Please try again.';
        }
    }
}

// Default values
$email = [
    'to' => '',
    'subject' => '',
    'message' => ''
];

// Pre-fill if we have contact info
if ($contact && $contact['email']) {
    $email['to'] = $contact['email'];
} elseif ($customer && $customer['email']) {
    $email['to'] = $customer['email'];
}

if ($customer) {
    $email['subject'] = 'Regarding your account with ' . APP_NAME;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Send Email</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <?php if ($customer || $contact): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Recipient</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($contact): ?>
                                <p><strong>Contact:</strong> <?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($customer): ?>
                                <p><strong>Customer:</strong> <?php echo htmlspecialchars($customer['company_name']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="to">To *</label>
                        <input type="email" class="form-control" id="to" name="to" 
                               value="<?php echo htmlspecialchars($email['to']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" class="form-control" id="subject" name="subject" 
                               value="<?php echo htmlspecialchars($email['subject']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea class="form-control" id="message" name="message" rows="10" required><?php echo htmlspecialchars($email['message']); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Email</button>
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