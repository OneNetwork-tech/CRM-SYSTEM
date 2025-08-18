<?php
// Edit customer details

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Customer.php';
require_once '../../classes/User.php';

// Check if user is logged in
require_login();

// Get customer ID from URL
$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$customer_id) {
    header('Location: index.php');
    exit;
}

// Get customer details
$customerObj = new Customer();
$customer = $customerObj->findById($customer_id);

if (!$customer) {
    header('Location: index.php');
    exit;
}

// Get users for assignment dropdown
$userObj = new User();
$users = $userObj->getAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $data = [
        'company_name' => sanitize_input($_POST['company_name']),
        'organization_number' => sanitize_input($_POST['organization_number']),
        'contact_person' => sanitize_input($_POST['contact_person']),
        'email' => sanitize_input($_POST['email']),
        'phone' => sanitize_input($_POST['phone']),
        'address' => sanitize_input($_POST['address']),
        'city' => sanitize_input($_POST['city']),
        'postal_code' => sanitize_input($_POST['postal_code']),
        'county' => sanitize_input($_POST['county']),
        'country' => sanitize_input($_POST['country']),
        'status' => sanitize_input($_POST['status']),
        'assigned_to' => !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null
    ];
    
    // Basic validation
    if (empty($data['company_name'])) {
        $error = 'Företagsnamn är obligatoriskt';
    } elseif (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Ogiltig e-postadress';
    } elseif (!empty($data['organization_number']) && !validate_org_number($data['organization_number'])) {
        $error = 'Ogiltigt organisationsnummer. Använd formatet XXXXXX-XXXX';
    } else {
        // Update customer
        $updated = $customerObj->update($customer_id, $data);
        
        if ($updated !== false) {
            $success = 'Kund uppdaterad';
            // Refresh customer data
            $customer = $customerObj->findById($customer_id);
        } else {
            $error = 'Kunde inte uppdatera kund. Försök igen.';
        }
    }
}

// Get Swedish counties
$counties = get_swedish_counties();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redigera kund - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Redigera kund: <?php echo htmlspecialchars($customer['company_name']); ?></h1>
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
                                <label for="company_name">Company Name *</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="<?php echo htmlspecialchars($customer['company_name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                       value="<?php echo htmlspecialchars($customer['contact_person']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($customer['email']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($customer['phone']); ?>">
                            </div>
                            
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($customer['address']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?php echo htmlspecialchars($customer['city']); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="state">State</label>
                                        <input type="text" class="form-control" id="state" name="state" 
                                               value="<?php echo htmlspecialchars($customer['state']); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="postal_code">Postal Code</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                               value="<?php echo htmlspecialchars($customer['postal_code']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="country">Country</label>
                                <input type="text" class="form-control" id="country" name="country" 
                                       value="<?php echo htmlspecialchars($customer['country']); ?>">
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="active" <?php echo $customer['status'] === 'active' ? 'selected' : ''; ?>>Aktiv</option>
                                    <option value="inactive" <?php echo $customer['status'] === 'inactive' ? 'selected' : ''; ?>>Inaktiv</option>
                                    <option value="lead" <?php echo $customer['status'] === 'lead' ? 'selected' : ''; ?>>Lead</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="assigned_to">Tilldelad till</label>
                                <select class="form-control" id="assigned_to" name="assigned_to">
                                    <option value="">Ej tilldelad</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>" <?php echo $customer['assigned_to'] == $user['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Uppdatera kund</button>
                    <a href="view.php?id=<?php echo $customer['id']; ?>" class="btn btn-secondary">Avbryt</a>
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