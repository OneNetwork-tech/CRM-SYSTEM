<?php
// Lead report

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Report.php';

// Check if user is logged in
require_login();

// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Get lead report data
$reportObj = new Report();
$leads = $reportObj->getLeadReport($start_date, $end_date);

// Filter by status if specified
if ($status) {
    $leads = array_filter($leads, function($lead) use ($status) {
        return $lead['status'] == $status;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Report - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Lead Report</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">Print Report</button>
                            <button class="btn btn-sm btn-outline-secondary">Export to PDF</button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <label for="start_date" class="mr-2">From:</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                            </div>
                            
                            <div class="form-group mr-2">
                                <label for="end_date" class="mr-2">To:</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                            </div>
                            
                            <div class="form-group mr-2">
                                <label for="status" class="mr-2">Status:</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="new" <?php echo ($status == 'new') ? 'selected' : ''; ?>>New</option>
                                    <option value="contacted" <?php echo ($status == 'contacted') ? 'selected' : ''; ?>>Contacted</option>
                                    <option value="qualified" <?php echo ($status == 'qualified') ? 'selected' : ''; ?>>Qualified</option>
                                    <option value="lost" <?php echo ($status == 'lost') ? 'selected' : ''; ?>>Lost</option>
                                    <option value="converted" <?php echo ($status == 'converted') ? 'selected' : ''; ?>>Converted</option>
                                </select>
                            </div>
                            
                            <button class="btn btn-outline-success mr-2" type="submit">Filter</button>
                            <a href="leads.php" class="btn btn-outline-secondary">Clear</a>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Source</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($leads)): ?>
                                <?php foreach ($leads as $lead): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($lead['company_name']); ?></td>
                                        <td><?php echo htmlspecialchars($lead['email']); ?></td>
                                        <td><?php echo htmlspecialchars($lead['phone']); ?></td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                <?php echo ucfirst(str_replace('_', ' ', $lead['source'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $lead['status'] == 'converted' ? 'success' : 
                                                    ($lead['status'] == 'lost' ? 'danger' : 
                                                    ($lead['status'] == 'qualified' ? 'primary' : 
                                                    ($lead['status'] == 'contacted' ? 'warning' : 'info'))); ?>">
                                                <?php echo ucfirst($lead['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($lead['assigned_first_name'] && $lead['assigned_last_name']): ?>
                                                <?php echo htmlspecialchars($lead['assigned_first_name'] . ' ' . $lead['assigned_last_name']); ?>
                                            <?php else: ?>
                                                Unassigned
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo format_date($lead['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No leads found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p>Total leads: <?php echo count($leads); ?></p>
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