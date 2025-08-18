<?php
// View lead profile

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Lead.php';
require_once '../../classes\User.php';

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
    <title><?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?> - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2"><?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="edit.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                            <a href="delete.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Är du säker på att du vill ta bort denna lead?')">Ta bort</a>
                            <?php if ($lead['status'] != 'converted'): ?>
                                <a href="convert.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-success" 
                                   onclick="return confirm('Är du säker på att du vill konvertera denna lead till kund?')">Konvertera till kund</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Leadinformation</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Namn:</th>
                                        <td><?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?></td>
                                    </tr>
                                    <?php if ($lead['company_name']): ?>
                                    <tr>
                                        <th>Företag:</th>
                                        <td><?php echo htmlspecialchars($lead['company_name']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>E-post:</th>
                                        <td><?php echo htmlspecialchars($lead['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Telefon:</th>
                                        <td><?php echo format_phone($lead['phone']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Adress:</th>
                                        <td>
                                            <?php echo htmlspecialchars($lead['address']); ?><br>
                                            <?php echo htmlspecialchars($lead['postal_code'] . ' ' . $lead['city']); ?><br>
                                            <?php echo htmlspecialchars($lead['county']); ?><br>
                                            <?php 
                                            $country_names = [
                                                'SE' => 'Sverige',
                                                'NO' => 'Norge',
                                                'DK' => 'Danmark',
                                                'FI' => 'Finland',
                                                'OTHER' => 'Annat'
                                            ];
                                            echo htmlspecialchars($country_names[$lead['country']] ?? $lead['country']); 
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Källa:</th>
                                        <td>
                                            <span class="badge badge-info">
                                                <?php echo htmlspecialchars($source_options[$lead['source']] ?? $lead['source']); ?>
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
                                                <?php echo htmlspecialchars($status_options[$lead['status']] ?? $lead['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php if ($lead['assigned_first_name'] && $lead['assigned_last_name']): ?>
                                    <tr>
                                        <th>Tilldelad till:</th>
                                        <td><?php echo htmlspecialchars($lead['assigned_first_name'] . ' ' . $lead['assigned_last_name']); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Skapad:</th>
                                        <td><?php echo format_datetime($lead['created_at']); ?></td>
                                    </tr>
                                    <?php if ($lead['notes']): ?>
                                    <tr>
                                        <th>Anteckningar:</th>
                                        <td><?php echo nl2br(htmlspecialchars($lead['notes'])); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Leadåtgärder</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($lead['status'] != 'converted'): ?>
                                    <a href="convert.php?id=<?php echo $lead['id']; ?>" class="btn btn-success btn-block">Konvertera till kund</a>
                                <?php else: ?>
                                    <div class="alert alert-success">Denna lead har konverterats till kund</div>
                                <?php endif; ?>
                                
                                <a href="../communications/add.php?lead_id=<?php echo $lead['id']; ?>" class="btn btn-secondary btn-block">Lägg till kommunikation</a>
                                <a href="../tasks/add.php?lead_id=<?php echo $lead['id']; ?>" class="btn btn-secondary btn-block">Lägg till uppgift</a>
                                
                                <div class="mt-3">
                                    <h6>Snabbstatusuppdatering</h6>
                                    <div class="btn-group-vertical w-100">
                                        <a href="edit.php?id=<?php echo $lead['id']; ?>&status=contacted" class="btn btn-outline-warning btn-sm">Markera som kontaktad</a>
                                        <a href="edit.php?id=<?php echo $lead['id']; ?>&status=qualified" class="btn btn-outline-primary btn-sm">Markera som kvalificerad</a>
                                        <a href="edit.php?id=<?php echo $lead['id']; ?>&status=lost" class="btn btn-outline-danger btn-sm">Markera som förlorad</a>
                                    </div>
                                </div>
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