<?php
// View communication details

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Communication.php';

// Check if user is logged in
require_login();

// Get communication ID from URL
$comm_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$comm_id) {
    header('Location: index.php');
    exit;
}

// Get communication details
$commObj = new Communication();
$communication = $commObj->findById($comm_id);

if (!$communication) {
    header('Location: index.php');
    exit;
}

// Communication type options
$type_options = [
    'email' => 'E-post',
    'call' => 'Telefonsamtal',
    'meeting' => 'Möte',
    'note' => 'Anteckning'
];

// Direction options
$direction_options = [
    'inbound' => 'Inkommande',
    'outbound' => 'Utgående'
];

// Status options
$status_options = [
    'pending' => 'Väntande',
    'completed' => 'Slutförd',
    'cancelled' => 'Avbruten'
];
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($communication['subject']); ?> - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2"><?php echo htmlspecialchars($communication['subject']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="edit.php?id=<?php echo $communication['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                            <a href="delete.php?id=<?php echo $communication['id']; ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Är du säker på att du vill ta bort denna kommunikation?')">Ta bort</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Kommunikationsdetaljer</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Ämne:</th>
                                        <td><?php echo htmlspecialchars($communication['subject']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Typ:</th>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $communication['type'] == 'email' ? 'primary' : 
                                                    ($communication['type'] == 'call' ? 'success' : 
                                                    ($communication['type'] == 'meeting' ? 'warning' : 'secondary')); ?>">
                                                <?php echo htmlspecialchars($type_options[$communication['type']] ?? $communication['type']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Riktning:</th>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $communication['direction'] == 'inbound' ? 'info' : 'dark'; ?>">
                                                <?php echo htmlspecialchars($direction_options[$communication['direction']] ?? $communication['direction']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $communication['status'] == 'completed' ? 'success' : 
                                                    ($communication['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                <?php echo htmlspecialchars($status_options[$communication['status']] ?? $communication['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php if ($communication['company_name']): ?>
                                    <tr>
                                        <th>Kund:</th>
                                        <td>
                                            <a href="../customers/view.php?id=<?php echo $communication['customer_id']; ?>">
                                                <?php echo htmlspecialchars($communication['company_name']); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($communication['contact_first_name'] && $communication['contact_last_name']): ?>
                                    <tr>
                                        <th>Kontakt:</th>
                                        <td>
                                            <a href="../contacts/view.php?id=<?php echo $communication['contact_id']; ?>">
                                                <?php echo htmlspecialchars($communication['contact_first_name'] . ' ' . $communication['contact_last_name']); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Skapad av:</th>
                                        <td><?php echo htmlspecialchars($communication['user_first_name'] . ' ' . $communication['user_last_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Skapad:</th>
                                        <td><?php echo format_datetime($communication['created_at']); ?></td>
                                    </tr>
                                </table>
                                
                                <h5>Innehåll</h5>
                                <div class="border p-3 bg-light">
                                    <?php echo nl2br(htmlspecialchars($communication['content'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Kommunikationsåtgärder</h5>
                            </div>
                            <div class="card-body">
                                <a href="edit.php?id=<?php echo $communication['id']; ?>" class="btn btn-primary btn-block">Redigera kommunikation</a>
                                <a href="add.php" class="btn btn-secondary btn-block">Lägg till ny kommunikation</a>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Relaterad information</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($communication['customer_id']): ?>
                                    <a href="../customers/view.php?id=<?php echo $communication['customer_id']; ?>" class="btn btn-outline-primary btn-block">Visa kund</a>
                                <?php endif; ?>
                                
                                <?php if ($communication['contact_id']): ?>
                                    <a href="../contacts/view.php?id=<?php echo $communication['contact_id']; ?>" class="btn btn-outline-primary btn-block">Visa kontakt</a>
                                <?php endif; ?>
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