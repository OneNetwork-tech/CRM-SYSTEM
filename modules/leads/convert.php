<?php
// Convert lead to customer

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Lead.php';

// Check if user is logged in
require_login();

// Get lead ID from URL
$lead_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$lead_id) {
    header('Location: index.php');
    exit;
}

// Get lead details
$leadObj = new Lead();
$lead = $leadObj->findById($lead_id);

if (!$lead) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Convert lead to customer
    $customerId = $leadObj->convertToCustomer($lead_id);
    
    if ($customerId) {
        $success = 'Lead successfully converted to customer';
        // Redirect to the new customer page after short delay
        header("refresh:2;url=../customers/view.php?id=$customerId");
    } else {
        $error = 'Failed to convert lead. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convert Lead - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Convert Lead to Customer</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Lead Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Name:</th>
                                <td><?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Company:</th>
                                <td><?php echo htmlspecialchars($lead['company_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo htmlspecialchars($lead['email']); ?></td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td><?php echo htmlspecialchars($lead['phone']); ?></td>
                            </tr>
                            <tr>
                                <th>Source:</th>
                                <td>
                                    <span class="badge badge-secondary">
                                        <?php echo ucfirst(str_replace('_', ' ', $lead['source'])); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $lead['status'] == 'converted' ? 'success' : 
                                            ($lead['status'] == 'lost' ? 'danger' : 
                                            ($lead['status'] == 'qualified' ? 'primary' : 
                                            ($lead['status'] == 'contacted' ? 'warning' : 'info'))); ?>">
                                        <?php echo ucfirst($lead['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="alert alert-info">
                    <h4>Conversion Process</h4>
                    <p>Converting this lead will:</p>
                    <ul>
                        <li>Create a new customer record with the lead's information</li>
                        <li>Create a primary contact for the customer</li>
                        <li>Set the lead status to "converted"</li>
                        <li>Preserve all lead information in the leads table</li>
                    </ul>
                    <p>Are you sure you want to proceed with the conversion?</p>
                </div>

                <form method="POST">
                    <button type="submit" class="btn btn-success">Yes, Convert to Customer</button>
                    <a href="view.php?id=<?php echo $lead['id']; ?>" class="btn btn-secondary">Cancel</a>
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