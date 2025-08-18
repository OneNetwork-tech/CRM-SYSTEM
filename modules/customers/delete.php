<?php
// Delete customer

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Customer.php';

// Check if user is logged in
require_login();

// Get customer ID from URL
$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$customer_id) {
    header('Location: index.php');
    exit;
}

// Check if confirmation is provided
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Delete the customer
    $customerObj = new Customer();
    $deleted = $customerObj->delete($customer_id);
    
    if ($deleted) {
        // Redirect to customer list with success message
        $_SESSION['message'] = 'Customer deleted successfully';
        header('Location: index.php');
        exit;
    } else {
        // Redirect to customer list with error message
        $_SESSION['error'] = 'Failed to delete customer';
        header("Location: view.php?id=$customer_id");
        exit;
    }
} else {
    // Show confirmation page
    $customerObj = new Customer();
    $customer = $customerObj->findById($customer_id);
    
    if (!$customer) {
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
    <title>Delete Customer - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Delete Customer</h1>
                </div>

                <div class="alert alert-warning">
                    <h4>Confirm Deletion</h4>
                    <p>Are you sure you want to delete the customer <strong><?php echo htmlspecialchars($customer['company_name']); ?></strong>?</p>
                    <p>This action cannot be undone and will permanently remove all associated data.</p>
                    
                    <a href="delete.php?id=<?php echo $customer_id; ?>&confirm=yes" class="btn btn-danger">Yes, Delete Customer</a>
                    <a href="view.php?id=<?php echo $customer_id; ?>" class="btn btn-secondary">Cancel</a>
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