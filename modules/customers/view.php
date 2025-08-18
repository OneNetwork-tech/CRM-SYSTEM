<?php
// View customer profile

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Customer.php';
require_once '../../classes\Contact.php';

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

// Get contacts for this customer
$contactObj = new Contact();
$contacts = $contactObj->getByCustomerId($customer_id);
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($customer['company_name']); ?> - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2"><?php echo htmlspecialchars($customer['company_name']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="edit.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                            <a href="delete.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Är du säker på att du vill ta bort denna kund?')">Ta bort</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Kundinformation</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Företagsnamn:</th>
                                        <td><?php echo htmlspecialchars($customer['company_name']); ?></td>
                                    </tr>
                                    <?php if ($customer['organization_number']): ?>
                                    <tr>
                                        <th>Organisationsnummer:</th>
                                        <td><?php echo htmlspecialchars($customer['organization_number']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Kontaktperson:</th>
                                        <td><?php echo htmlspecialchars($customer['contact_person']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>E-post:</th>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Telefon:</th>
                                        <td><?php echo format_phone($customer['phone']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Adress:</th>
                                        <td>
                                            <?php echo htmlspecialchars($customer['address']); ?><br>
                                            <?php echo htmlspecialchars($customer['postal_code'] . ' ' . $customer['city']); ?><br>
                                            <?php echo htmlspecialchars($customer['county']); ?><br>
                                            <?php 
                                            $country_names = [
                                                'SE' => 'Sverige',
                                                'NO' => 'Norge',
                                                'DK' => 'Danmark',
                                                'FI' => 'Finland',
                                                'OTHER' => 'Annat'
                                            ];
                                            echo htmlspecialchars($country_names[$customer['country']] ?? $customer['country']); 
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $customer['status'] == 'active' ? 'success' : 
                                                    ($customer['status'] == 'inactive' ? 'secondary' : 'info'); ?>">
                                                <?php 
                                                $status_names = [
                                                    'active' => 'Aktiv',
                                                    'inactive' => 'Inaktiv',
                                                    'lead' => 'Lead'
                                                ];
                                                echo htmlspecialchars($status_names[$customer['status']] ?? $customer['status']); 
                                                ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Skapad:</th>
                                        <td><?php echo format_datetime($customer['created_at']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Kontakter</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($contacts)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Namn</th>
                                                    <th>E-post</th>
                                                    <th>Telefon</th>
                                                    <th>Primär</th>
                                                    <th>Åtgärder</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($contacts as $contact): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                                        <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                                                        <td>
                                                            <?php if ($contact['is_primary']): ?>
                                                                <span class="badge badge-success">Ja</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <a href="../contacts/view.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                                            <a href="../contacts/edit.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>Inga kontakter registrerade för denna kund.</p>
                                <?php endif; ?>
                                <a href="../contacts/add.php?customer_id=<?php echo $customer['id']; ?>" class="btn btn-primary">Lägg till kontakt</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Kundåtgärder</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($customer['assigned_first_name'] && $customer['assigned_last_name']): ?>
                                    <h6>Tilldelad till</h6>
                                    <p>
                                        <?php echo htmlspecialchars($customer['assigned_first_name'] . ' ' . $customer['assigned_last_name']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <a href="../contacts/add.php?customer_id=<?php echo $customer['id']; ?>" class="btn btn-primary btn-block">Lägg till kontakt</a>
                                <a href="../communications/add.php?customer_id=<?php echo $customer['id']; ?>" class="btn btn-secondary btn-block">Lägg till kommunikation</a>
                                <a href="../tasks/add.php?customer_id=<?php echo $customer['id']; ?>" class="btn btn-secondary btn-block">Lägg till uppgift</a>
                                <a href="../documents/upload.php?customer_id=<?php echo $customer['id']; ?>" class="btn btn-secondary btn-block">Ladda upp dokument</a>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Statistik</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2">Kontakter: <?php echo count($contacts); ?></li>
                                    <li class="mb-2">Kommunikationer: 0</li>
                                    <li class="mb-2">Uppgifter: 0</li>
                                    <li class="mb-2">Dokument: 0</li>
                                </ul>
                            </div>
                        </div>
                    </div>
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