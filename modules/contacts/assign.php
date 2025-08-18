<?php
// Assign contact to customer

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Contact.php';
require_once '../../classes/Customer.php';

// Check if user is logged in
require_login();

// Get contact ID and customer ID from URL
$contact_id = isset($_GET['contact_id']) ? intval($_GET['contact_id']) : 0;
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

if (!$contact_id || !$customer_id) {
    header('Location: index.php');
    exit;
}

// Get contact and customer details
$contactObj = new Contact();
$contact = $contactObj->findById($contact_id);

$customerObj = new Customer();
$customer = $customerObj->findById($customer_id);

if (!$contact || !$customer) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process assignment
    $data = [
        'customer_id' => $customer_id,
        'first_name' => $contact['first_name'],
        'last_name' => $contact['last_name'],
        'email' => $contact['email'],
        'phone' => $contact['phone'],
        'position' => sanitize_input($_POST['position']),
        'department' => sanitize_input($_POST['department']),
        'is_primary' => isset($_POST['is_primary']) ? 1 : 0
    ];
    
    // Update contact with new customer
    $updated = $contactObj->update($contact_id, $data);
    
    if ($updated !== false) {
        $success = 'Contact assigned to customer successfully';
        // If this is marked as primary, update other contacts
        if ($data['is_primary']) {
            $contactObj->makePrimary($contact_id, $customer_id);
        }
        // Redirect after short delay
        header("refresh:2;url=view.php?id=$contact_id");
    } else {
        $error = 'Failed to assign contact. Please try again.';
    }
}

// Default values
$assignment = [
    'position' => $contact['position'],
    'department' => $contact['department'],
    'is_primary' => 0
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Contact - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Assign Contact to Customer</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Contact to Assign</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Name:</th>
                                <td><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Customer</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Company:</th>
                                <td><?php echo htmlspecialchars($customer['company_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Contact Person:</th>
                                <td><?php echo htmlspecialchars($customer['contact_person']); ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <form method="POST">
                    <div class="card">
                        <div class="card-header">
                            <h5>Assignment Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="position">Position</label>
                                <input type="text" class="form-control" id="position" name="position" 
                                       value="<?php echo htmlspecialchars($assignment['position']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="department">Department</label>
                                <input type="text" class="form-control" id="department" name="department" 
                                       value="<?php echo htmlspecialchars($assignment['department']); ?>">
                            </div>
                            
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="is_primary" name="is_primary" <?php echo $assignment['is_primary'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_primary">Primary Contact</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-3">Assign Contact</button>
                    <a href="../customers/view.php?id=<?php echo $customer_id; ?>" class="btn btn-secondary mt-3">Cancel</a>
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