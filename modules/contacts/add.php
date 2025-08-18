<?php
// Add new contact

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Contact.php';
require_once '../../classes/Customer.php';

// Check if user is logged in
require_login();

// Get customers for dropdown
$customerObj = new Customer();
$customers = $customerObj->getAll();

// Check if customer_id is passed in URL
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $data = [
        'customer_id' => !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null,
        'first_name' => sanitize_input($_POST['first_name']),
        'last_name' => sanitize_input($_POST['last_name']),
        'email' => sanitize_input($_POST['email']),
        'phone' => sanitize_input($_POST['phone']),
        'position' => sanitize_input($_POST['position']),
        'is_primary' => isset($_POST['is_primary']) ? 1 : 0
    ];
    
    // Basic validation
    if (empty($data['first_name']) || empty($data['last_name'])) {
        $error = 'Förnamn och efternamn är obligatoriska';
    } elseif (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Ogiltig e-postadress';
    } else {
        // Create contact
        $contactObj = new Contact();
        $contactId = $contactObj->create($data);
        
        if ($contactId) {
            $success = 'Kontakt skapad';
            // Redirect to view page after short delay
            header("refresh:2;url=view.php?id=$contactId");
        } else {
            $error = 'Kunde inte skapa kontakt. Försök igen.';
        }
    }
}

// Default values
$contact = [
    'customer_id' => $customer_id,
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'position' => '',
    'is_primary' => 0
];
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lägg till kontakt - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Lägg till kontakt</h1>
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
                                <label for="customer_id">Kund</label>
                                <select class="form-control" id="customer_id" name="customer_id">
                                    <option value="">Välj kund</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>" <?php echo $contact['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($customer['company_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">Förnamn *</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" 
                                               value="<?php echo htmlspecialchars($contact['first_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Efternamn *</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" 
                                               value="<?php echo htmlspecialchars($contact['last_name']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="position">Position</label>
                                <input type="text" class="form-control" id="position" name="position" 
                                       value="<?php echo htmlspecialchars($contact['position']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">E-post</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($contact['email']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Telefon</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($contact['phone']); ?>">
                            </div>
                            
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="is_primary" name="is_primary" <?php echo $contact['is_primary'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_primary">Primär kontakt</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Skapa kontakt</button>
                    <a href="index.php" class="btn btn-secondary">Avbryt</a>
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