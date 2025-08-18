<?php
// Delete contact

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Contact.php';

// Check if user is logged in
require_login();

// Get contact ID from URL
$contact_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$contact_id) {
    header('Location: index.php');
    exit;
}

// Check if confirmation is provided
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Delete the contact
    $contactObj = new Contact();
    $deleted = $contactObj->delete($contact_id);
    
    if ($deleted) {
        // Redirect to contact list with success message
        $_SESSION['message'] = 'Contact deleted successfully';
        header('Location: index.php');
        exit;
    } else {
        // Redirect to contact list with error message
        $_SESSION['error'] = 'Failed to delete contact';
        header("Location: view.php?id=$contact_id");
        exit;
    }
} else {
    // Show confirmation page
    $contactObj = new Contact();
    $contact = $contactObj->findById($contact_id);
    
    if (!$contact) {
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Contact - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Delete Contact</h1>
                </div>

                <div class="alert alert-warning">
                    <h4>Confirm Deletion</h4>
                    <p>Are you sure you want to delete the contact <strong><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></strong>?</p>
                    <p>This action cannot be undone and will permanently remove all associated data.</p>
                    
                    <a href="delete.php?id=<?php echo $contact_id; ?>&confirm=yes" class="btn btn-danger">Yes, Delete Contact</a>
                    <a href="view.php?id=<?php echo $contact_id; ?>" class="btn btn-secondary">Cancel</a>
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