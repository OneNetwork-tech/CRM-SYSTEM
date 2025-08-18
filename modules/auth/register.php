<?php
// Unified business registration page for all types of business registrations including suppliers and organizations

session_start();
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../classes/User.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: ../../dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process registration form
    $data = [
        'username' => sanitize_input($_POST['username']),
        'email' => sanitize_input($_POST['email']),
        'password' => $_POST['password'],
        'confirm_password' => $_POST['confirm_password'],
        'first_name' => sanitize_input($_POST['first_name']),
        'last_name' => sanitize_input($_POST['last_name']),
        'company_name' => sanitize_input($_POST['company_name']),
        'business_type' => sanitize_input($_POST['business_type']),
        'phone' => sanitize_input($_POST['phone'])
    ];
    
    // Validation
    if (empty($data['username']) || empty($data['email']) || empty($data['password']) || 
        empty($data['first_name']) || empty($data['last_name']) || empty($data['company_name'])) {
        $error = 'All fields are required';
    } elseif ($data['password'] !== $data['confirm_password']) {
        $error = 'Passwords do not match';
    } elseif (strlen($data['password']) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        // Check if username or email already exists
        $userObj = new User();
        $existingUser = $userObj->findByUsername($data['username']);
        
        if ($existingUser) {
            $error = 'Username already exists';
        } else {
            // Create new user with business information
            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'role' => 'user' // Default role
            ];
            
            $userId = $userObj->create($userData);
            
            if ($userId) {
                $success = 'Registration successful! You can now log in.';
                // Log the business registration
                log_activity($userId, 'business_registration', 'Registered as ' . $data['business_type'] . ' - ' . $data['company_name']);
                
                // Redirect to login after short delay
                header("refresh:3;url=../auth/login.php");
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

// Default values
$registration = [
    'username' => '',
    'email' => '',
    'first_name' => '',
    'last_name' => '',
    'company_name' => '',
    'business_type' => 'customer',
    'phone' => ''
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Registration - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="registration-form">
                    <div class="text-center mb-4">
                        <img src="../../assets/images/logo.png" alt="OneNetworkCRM" class="logo">
                        <h2>Business Registration</h2>
                        <p>Register your business for full access to our CRM system</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="card">
                            <div class="card-header">
                                <h5>Account Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Username *</label>
                                            <input type="text" class="form-control" id="username" name="username" 
                                                   value="<?php echo htmlspecialchars($registration['username']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email Address *</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($registration['email']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password *</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                            <small class="form-text text-muted">At least 6 characters long</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="confirm_password">Confirm Password *</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name *</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                                   value="<?php echo htmlspecialchars($registration['first_name']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name *</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                                   value="<?php echo htmlspecialchars($registration['last_name']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($registration['phone']); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Business Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="company_name">Company/Organization Name *</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" 
                                           value="<?php echo htmlspecialchars($registration['company_name']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="business_type">Business Type *</label>
                                    <select class="form-control" id="business_type" name="business_type" required>
                                        <option value="customer" <?php echo $registration['business_type'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                                        <option value="supplier" <?php echo $registration['business_type'] === 'supplier' ? 'selected' : ''; ?>>Supplier</option>
                                        <option value="partner" <?php echo $registration['business_type'] === 'partner' ? 'selected' : ''; ?>>Partner</option>
                                        <option value="vendor" <?php echo $registration['business_type'] === 'vendor' ? 'selected' : ''; ?>>Vendor</option>
                                        <option value="other" <?php echo $registration['business_type'] === 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-block">Register Business</button>
                        </div>
                        
                        <div class="text-center">
                            <p>Already have an account? <a href="login.php">Sign in here</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/app.js"></script>
</body>
</html>