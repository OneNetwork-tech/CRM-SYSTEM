<?php
// Customers list

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Customer.php';

// Check if user is logged in
require_login();

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Get customers
$customerObj = new Customer();
if ($search) {
    $customers = $customerObj->search($search);
} elseif ($status) {
    $customers = $customerObj->getByStatus($status);
} else {
    $customers = $customerObj->getAll();
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kunder - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Kunder</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="add.php" class="btn btn-sm btn-outline-secondary">Lägg till kund</a>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <input class="form-control" type="search" name="search" placeholder="Sök kunder..." aria-label="Sök" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            
                            <div class="form-group mr-2">
                                <select class="form-control" name="status">
                                    <option value="">Alla status</option>
                                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Aktiva</option>
                                    <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inaktiva</option>
                                    <option value="lead" <?php echo $status === 'lead' ? 'selected' : ''; ?>>Leads</option>
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
                                <th>Företag</th>
                                <th>Kontaktperson</th>
                                <th>E-post</th>
                                <th>Telefon</th>
                                <th>Ort</th>
                                <th>Status</th>
                                <th>Tilldelad till</th>
                                <th>Åtgärder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($customers)): ?>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($customer['company_name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['contact_person']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo format_phone($customer['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['city']); ?></td>
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
                                        <td>
                                            <?php if ($customer['assigned_first_name'] && $customer['assigned_last_name']): ?>
                                                <?php echo htmlspecialchars($customer['assigned_first_name'] . ' ' . $customer['assigned_last_name']); ?>
                                            <?php else: ?>
                                                Ej tilldelad
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="view.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                            <a href="edit.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Inga kunder hittades</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p>Totalt antal kunder: <?php echo count($customers); ?></p>
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