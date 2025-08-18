<?php
// Communication report

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
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Get communication report data
$reportObj = new Report();
$communications = $reportObj->getCommunicationReport($start_date, $end_date, $type);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Communication Report - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Communication Report</h1>
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
                                <label for="type" class="mr-2">Type:</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="">All Types</option>
                                    <option value="email" <?php echo ($type == 'email') ? 'selected' : ''; ?>>Email</option>
                                    <option value="call" <?php echo ($type == 'call') ? 'selected' : ''; ?>>Call</option>
                                    <option value="meeting" <?php echo ($type == 'meeting') ? 'selected' : ''; ?>>Meeting</option>
                                    <option value="note" <?php echo ($type == 'note') ? 'selected' : ''; ?>>Note</option>
                                </select>
                            </div>
                            
                            <button class="btn btn-outline-success mr-2" type="submit">Filter</button>
                            <a href="communications.php" class="btn btn-outline-secondary">Clear</a>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Customer/Contact</th>
                                <th>Direction</th>
                                <th>Status</th>
                                <th>User</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($communications)): ?>
                                <?php foreach ($communications as $communication): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $communication['type'] == 'email' ? 'primary' : 
                                                    ($communication['type'] == 'call' ? 'success' : 
                                                    ($communication['type'] == 'meeting' ? 'warning' : 'secondary')); ?>">
                                                <?php echo ucfirst($communication['type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($communication['subject']); ?></td>
                                        <td>
                                            <?php if ($communication['company_name']): ?>
                                                <?php echo htmlspecialchars($communication['company_name']); ?>
                                            <?php elseif ($communication['contact_first_name'] && $communication['contact_last_name']): ?>
                                                <?php echo htmlspecialchars($communication['contact_first_name'] . ' ' . $communication['contact_last_name']); ?>
                                            <?php else: ?>
                                                Not specified
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $communication['direction'] == 'inbound' ? 'info' : 'primary'; ?>">
                                                <?php echo ucfirst($communication['direction']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $communication['status'] == 'completed' ? 'success' : 
                                                    ($communication['status'] == 'cancelled' ? 'danger' : 'info'); ?>">
                                                <?php echo ucfirst($communication['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($communication['user_first_name'] . ' ' . $communication['user_last_name']); ?>
                                        </td>
                                        <td><?php echo format_datetime($communication['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No communications found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p>Total communications: <?php echo count($communications); ?></p>
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