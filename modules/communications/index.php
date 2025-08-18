<?php
// Communications list

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Communication.php';

// Check if user is logged in
require_login();

// Get filter parameters
$type = isset($_GET['type']) ? $_GET['type'] : '';
$direction = isset($_GET['direction']) ? $_GET['direction'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Get communications
$commObj = new Communication();
if ($search) {
    $communications = $commObj->search($search);
} elseif ($type) {
    $communications = $commObj->getByType($type);
} else {
    $communications = $commObj->getAll();
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
    <title>Kommunikationer - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Kommunikationer</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="add.php" class="btn btn-sm btn-outline-secondary">Lägg till kommunikation</a>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <input class="form-control" type="search" name="search" placeholder="Sök kommunikationer..." aria-label="Sök" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            
                            <div class="form-group mr-2">
                                <select class="form-control" name="type">
                                    <option value="">Alla typer</option>
                                    <?php foreach ($type_options as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $type === $value ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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
                            
                            <button class="btn btn-outline-success mr-2" type="submit">Filtrera</button>
                            <a href="index.php" class="btn btn-outline-secondary">Rensa</a>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Ämne</th>
                                <th>Kund/Kontakt</th>
                                <th>Typ</th>
                                <th>Riktning</th>
                                <th>Status</th>
                                <th>Skapad av</th>
                                <th>Datum</th>
                                <th>Åtgärder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($communications)): ?>
                                <?php foreach ($communications as $comm): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(substr($comm['subject'], 0, 30)) . (strlen($comm['subject']) > 30 ? '...' : ''); ?></td>
                                        <td>
                                            <?php if ($comm['company_name']): ?>
                                                <a href="../customers/view.php?id=<?php echo $comm['customer_id']; ?>">
                                                    <?php echo htmlspecialchars($comm['company_name']); ?>
                                                </a>
                                            <?php elseif ($comm['contact_first_name'] && $comm['contact_last_name']): ?>
                                                <a href="../contacts/view.php?id=<?php echo $comm['contact_id']; ?>">
                                                    <?php echo htmlspecialchars($comm['contact_first_name'] . ' ' . $comm['contact_last_name']); ?>
                                                </a>
                                            <?php else: ?>
                                                Ej tilldelad
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $comm['type'] == 'email' ? 'primary' : 
                                                    ($comm['type'] == 'call' ? 'success' : 
                                                    ($comm['type'] == 'meeting' ? 'warning' : 'secondary')); ?>">
                                                <?php echo htmlspecialchars($type_options[$comm['type']] ?? $comm['type']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $comm['direction'] == 'inbound' ? 'info' : 'dark'; ?>">
                                                <?php echo htmlspecialchars($direction_options[$comm['direction']] ?? $comm['direction']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $comm['status'] == 'completed' ? 'success' : 
                                                    ($comm['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                <?php echo htmlspecialchars($status_options[$comm['status']] ?? $comm['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($comm['user_first_name'] . ' ' . $comm['user_last_name']); ?></td>
                                        <td><?php echo format_date($comm['created_at']); ?></td>
                                        <td>
                                            <a href="view.php?id=<?php echo $comm['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                            <a href="edit.php?id=<?php echo $comm['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Inga kommunikationer hittades</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p>Totalt antal kommunikationer: <?php echo count($communications); ?></p>
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