<?php
// Leads list

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Lead.php';

// Check if user is logged in
require_login();

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$source = isset($_GET['source']) ? $_GET['source'] : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Get leads
$leadObj = new Lead();
if ($search) {
    $leads = $leadObj->search($search);
} elseif ($status) {
    $leads = $leadObj->getByStatus($status);
} elseif ($source) {
    $leads = $leadObj->getBySource($source);
} else {
    $leads = $leadObj->getAll();
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
    <title>Leads - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../../includes/sidebar.php'; ?>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Leads</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="add.php" class="btn btn-sm btn-outline-secondary">Lägg till lead</a>
                            <a href="pipeline.php" class="btn btn-sm btn-outline-secondary">Pipeline</a>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <input class="form-control" type="search" name="search" placeholder="Sök leads..." aria-label="Sök" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            
                            <div class="form-group mr-2">
                                <select class="form-control" name="status">
                                    <option value="">Alla status</option>
                                    <?php foreach ($status_options as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $status === $value ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group mr-2">
                                <select class="form-control" name="source">
                                    <option value="">Alla källor</option>
                                    <?php foreach ($source_options as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $source === $value ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button class="btn btn-outline-success mr-2" type="submit">Filtrera</button>
                            <a href="index.php" class="btn btn-outline-secondary">Rensa</a>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Namn</th>
                                <th>Företag</th>
                                <th>E-post</th>
                                <th>Telefon</th>
                                <th>Källa</th>
                                <th>Status</th>
                                <th>Tilldelad till</th>
                                <th>Skapad</th>
                                <th>Åtgärder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($leads)): ?>
                                <?php foreach ($leads as $lead): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($lead['company_name']); ?></td>
                                        <td><?php echo htmlspecialchars($lead['email']); ?></td>
                                        <td><?php echo format_phone($lead['phone']); ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?php echo htmlspecialchars($source_options[$lead['source']] ?? $lead['source']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $lead['status'] == 'converted' ? 'success' : 
                                                    ($lead['status'] == 'lost' ? 'danger' : 
                                                    ($lead['status'] == 'qualified' ? 'primary' : 
                                                    ($lead['status'] == 'contacted' ? 'warning' : 'info'))); ?>">
                                                <?php echo htmlspecialchars($status_options[$lead['status']] ?? $lead['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($lead['assigned_first_name'] && $lead['assigned_last_name']): ?>
                                                <?php echo htmlspecialchars($lead['assigned_first_name'] . ' ' . $lead['assigned_last_name']); ?>
                                            <?php else: ?>
                                                Ej tilldelad
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo format_date($lead['created_at']); ?></td>
                                        <td>
                                            <a href="view.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                            <a href="edit.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Inga leads hittades</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p>Totalt antal leads: <?php echo count($leads); ?></p>
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