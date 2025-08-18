<?php
// View contact profile

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

// Get contact details
$contactObj = new Contact();
$contact = $contactObj->findById($contact_id);

if (!$contact) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?> - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2"><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="edit.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                            <a href="delete.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Är du säker på att du vill ta bort denna kontakt?')">Ta bort</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Kontaktinformation</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Namn:</th>
                                        <td><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></td>
                                    </tr>
                                    <?php if ($contact['company_name']): ?>
                                    <tr>
                                        <th>Företag:</th>
                                        <td>
                                            <a href="../customers/view.php?id=<?php echo $contact['customer_id']; ?>">
                                                <?php echo htmlspecialchars($contact['company_name']); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($contact['position']): ?>
                                    <tr>
                                        <th>Position:</th>
                                        <td><?php echo htmlspecialchars($contact['position']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>E-post:</th>
                                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Telefon:</th>
                                        <td><?php echo format_phone($contact['phone']); ?></td>
                                    </tr>
                                    <?php if ($contact['is_primary']): ?>
                                    <tr>
                                        <th>Primär kontakt:</th>
                                        <td><span class="badge badge-success">Ja</span></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Skapad:</th>
                                        <td><?php echo format_datetime($contact['created_at']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Kontaktåtgärder</h5>
                            </div>
                            <div class="card-body">
                                <a href="../communications/add.php?contact_id=<?php echo $contact['id']; ?>" class="btn btn-primary btn-block">Lägg till kommunikation</a>
                                <a href="../tasks/add.php?contact_id=<?php echo $contact['id']; ?>" class="btn btn-secondary btn-block">Lägg till uppgift</a>
                                <a href="edit.php?id=<?php echo $contact['id']; ?>&set_primary=1" class="btn btn-secondary btn-block">
                                    <?php echo $contact['is_primary'] ? 'Redan primär' : 'Gör till primär kontakt'; ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Statistik</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2">Kommunikationer: 0</li>
                                    <li class="mb-2">Uppgifter: 0</li>
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