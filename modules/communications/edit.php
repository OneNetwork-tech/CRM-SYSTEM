<?php
// Redigera kommunikationsdetaljer

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Communication.php';
require_once '../../classes/Customer.php';
require_once '../../classes/Contact.php';
require_once '../../classes/User.php';

// Kontrollera om användaren är inloggad
require_login();

// Hämta kommunikations-ID från URL
$comm_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$comm_id) {
    header('Location: index.php');
    exit;
}

// Hämta kommunikationsdetaljer
$commObj = new Communication();
$communication = $commObj->findById($comm_id);

if (!$communication) {
    header('Location: index.php');
    exit;
}

// Hämta kunder och kontakter för dropdowns
$customerObj = new Customer();
$customers = $customerObj->getAll();

$contactObj = new Contact();
$contacts = $contactObj->getAll();

$userObj = new User();
$users = $userObj->getAll();

// Hämta nuvarande användare
$current_user = get_current_user_data();

$error = '';
$success = '';

// Kommunikationstyper
$type_options = [
    'email' => 'E-post',
    'call' => 'Telefonsamtal',
    'meeting' => 'Möte',
    'note' => 'Anteckning'
];

// Riktningar
$direction_options = [
    'inbound' => 'Inkommande',
    'outbound' => 'Utgående'
];

// Statusalternativ
$status_options = [
    'pending' => 'Väntande',
    'completed' => 'Slutförd',
    'cancelled' => 'Avbruten'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processa formulärinlämning
    $data = [
        'customer_id' => !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null,
        'contact_id' => !empty($_POST['contact_id']) ? intval($_POST['contact_id']) : null,
        'user_id' => !empty($_POST['user_id']) ? intval($_POST['user_id']) : $current_user['id'],
        'type' => sanitize_input($_POST['type']),
        'subject' => sanitize_input($_POST['subject']),
        'content' => sanitize_input($_POST['content']),
        'direction' => sanitize_input($_POST['direction']),
        'status' => sanitize_input($_POST['status']),
        'scheduled_at' => !empty($_POST['scheduled_at']) ? sanitize_input($_POST['scheduled_at']) : null
    ];
    
    // Grundläggande validering
    if (empty($data['subject'])) {
        $error = 'Ämne är obligatoriskt';
    } elseif (empty($data['content'])) {
        $error = 'Innehåll är obligatoriskt';
    } else {
        // Uppdatera kommunikation
        $updated = $commObj->update($comm_id, $data);
        
        if ($updated !== false) {
            $success = 'Kommunikation uppdaterad';
            // Uppdatera kommunikationsdata
            $communication = $commObj->findById($comm_id);
        } else {
            $error = 'Kunde inte uppdatera kommunikation. Försök igen.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redigera kommunikation - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Redigera kommunikation: <?php echo htmlspecialchars($communication['subject']); ?></h1>
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
                                        <option value="<?php echo $customer['id']; ?>" <?php echo $communication['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($customer['company_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_id">Kontakt</label>
                                <select class="form-control" id="contact_id" name="contact_id">
                                    <option value="">Välj kontakt</option>
                                    <?php foreach ($contacts as $contact): ?>
                                        <option value="<?php echo $contact['id']; ?>" <?php echo $communication['contact_id'] == $contact['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?>
                                            <?php if ($contact['company_name']): ?>
                                                (<?php echo htmlspecialchars($contact['company_name']); ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type">Typ</label>
                                        <select class="form-control" id="type" name="type">
                                            <?php foreach ($type_options as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" <?php echo $communication['type'] == $value ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="direction">Riktning</label>
                                        <select class="form-control" id="direction" name="direction">
                                            <?php foreach ($direction_options as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" <?php echo $communication['direction'] == $value ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <?php foreach ($status_options as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $communication['status'] == $value ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Ämne *</label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="<?php echo htmlspecialchars($communication['subject']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="content">Innehåll *</label>
                                <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($communication['content']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="user_id">Tilldelad till</label>
                                <select class="form-control" id="user_id" name="user_id">
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>" <?php echo $communication['user_id'] == $user['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="scheduled_at">Schemalagd</label>
                                <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at" 
                                       value="<?php echo $communication['scheduled_at'] ? date('Y-m-d\TH:i', strtotime($communication['scheduled_at'])) : ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Uppdatera kommunikation</button>
                    <a href="view.php?id=<?php echo $communication['id']; ?>" class="btn btn-secondary">Avbryt</a>
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