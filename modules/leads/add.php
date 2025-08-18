<?php
// Add new lead

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Lead.php';
require_once '../../classes/User.php';

// Check if user is logged in
require_login();

// Get users for assignment dropdown
$userObj = new User();
$users = $userObj->getAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $data = [
        'first_name' => sanitize_input($_POST['first_name']),
        'last_name' => sanitize_input($_POST['last_name']),
        'company_name' => sanitize_input($_POST['company_name']),
        'email' => sanitize_input($_POST['email']),
        'phone' => sanitize_input($_POST['phone']),
        'address' => sanitize_input($_POST['address']),
        'city' => sanitize_input($_POST['city']),
        'postal_code' => sanitize_input($_POST['postal_code']),
        'county' => sanitize_input($_POST['county']),
        'country' => sanitize_input($_POST['country']),
        'source' => sanitize_input($_POST['source']),
        'status' => sanitize_input($_POST['status']),
        'assigned_to' => !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null,
        'notes' => sanitize_input($_POST['notes'])
    ];
    
    // Basic validation
    if (empty($data['first_name']) || empty($data['last_name'])) {
        $error = 'Förnamn och efternamn är obligatoriska';
    } elseif (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Ogiltig e-postadress';
    } else {
        // Create lead
        $leadObj = new Lead();
        $leadId = $leadObj->create($data);
        
        if ($leadId) {
            $success = 'Lead skapad';
            // Redirect to view page after short delay
            header("refresh:2;url=view.php?id=$leadId");
        } else {
            $error = 'Kunde inte skapa lead. Försök igen.';
        }
    }
}

// Default values
$lead = [
    'first_name' => '',
    'last_name' => '',
    'company_name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'postal_code' => '',
    'county' => '',
    'country' => 'SE',
    'source' => 'website',
    'status' => 'new',
    'assigned_to' => '',
    'notes' => ''
];

// Get Swedish counties
$counties = get_swedish_counties();

// Lead source options
$source_options = [
    'website' => 'Webbplats',
    'referral' => 'Referens',
    'social_media' => 'Sociala medier',
    'email_marketing' => 'E-postmarknadsföring',
    'event' => 'Event',
    'other' => 'Annat'
];

// Lead status options
$status_options = [
    'new' => 'Ny',
    'contacted' => 'Kontaktad',
    'qualified' => 'Kvalificerad',
    'lost' => 'Förlorad',
    'converted' => 'Konverterad'
];
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lägg till lead - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Lägg till lead</h1>
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
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">Förnamn *</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" 
                                               value="<?php echo htmlspecialchars($lead['first_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Efternamn *</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" 
                                               value="<?php echo htmlspecialchars($lead['last_name']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="company_name">Företagsnamn</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="<?php echo htmlspecialchars($lead['company_name']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">E-post</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($lead['email']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Telefon</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($lead['phone']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Adress</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?php echo htmlspecialchars($lead['address']); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="postal_code">Postnummer</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                               value="<?php echo htmlspecialchars($lead['postal_code']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="city">Ort</label>
                                        <input type="text" class="form-control" id="city" name="city" 
                                               value="<?php echo htmlspecialchars($lead['city']); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="county">Län</label>
                                <select class="form-control" id="county" name="county">
                                    <option value="">Välj län</option>
                                    <?php foreach ($counties as $county): ?>
                                        <option value="<?php echo htmlspecialchars($county); ?>" <?php echo $lead['county'] == $county ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($county); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="country">Land</label>
                                <select class="form-control" id="country" name="country">
                                    <option value="SE" <?php echo $lead['country'] == 'SE' ? 'selected' : ''; ?>>Sverige</option>
                                    <option value="NO" <?php echo $lead['country'] == 'NO' ? 'selected' : ''; ?>>Norge</option>
                                    <option value="DK" <?php echo $lead['country'] == 'DK' ? 'selected' : ''; ?>>Danmark</option>
                                    <option value="FI" <?php echo $lead['country'] == 'FI' ? 'selected' : ''; ?>>Finland</option>
                                    <option value="OTHER" <?php echo $lead['country'] == 'OTHER' ? 'selected' : ''; ?>>Annat</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="source">Källa</label>
                                <select class="form-control" id="source" name="source">
                                    <?php foreach ($source_options as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $lead['source'] == $value ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <?php foreach ($status_options as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $lead['status'] == $value ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="assigned_to">Tilldelad till</label>
                                <select class="form-control" id="assigned_to" name="assigned_to">
                                    <option value="">Ej tilldelad</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>" <?php echo $lead['assigned_to'] == $user['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes">Anteckningar</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($lead['notes']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Skapa lead</button>
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