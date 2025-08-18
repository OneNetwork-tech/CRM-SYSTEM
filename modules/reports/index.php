<?php
// Reports dashboard

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Report.php';

// Check if user is logged in
require_login();

// Get report statistics
$reportObj = new Report();
$customerStats = $reportObj->getCustomerStatistics();
$leadStats = $reportObj->getLeadStatistics();
$commStats = $reportObj->getCommunicationStatistics();
$taskStats = $reportObj->getTaskStatistics();
$docStats = $reportObj->getDocumentStatistics();

// Format file size for display
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    
    return $bytes;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Reports Dashboard</h1>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>System Statistics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body text-center">
                                                <h5 class="card-title"><?php echo $customerStats['total_customers']; ?></h5>
                                                <p class="card-text">Customers</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="card text-white bg-success">
                                            <div class="card-body text-center">
                                                <h5 class="card-title"><?php echo $leadStats['total_leads']; ?></h5>
                                                <p class="card-text">Leads</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="card text-white bg-info">
                                            <div class="card-body text-center">
                                                <h5 class="card-title"><?php echo $commStats['total_communications']; ?></h5>
                                                <p class="card-text">Communications</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="card text-white bg-warning">
                                            <div class="card-body text-center">
                                                <h5 class="card-title"><?php echo $taskStats['total_tasks']; ?></h5>
                                                <p class="card-text">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="card text-white bg-danger">
                                            <div class="card-body text-center">
                                                <h5 class="card-title"><?php echo $docStats['total_documents']; ?></h5>
                                                <p class="card-text">Documents</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="card text-white bg-secondary">
                                            <div class="card-body text-center">
                                                <h5 class="card-title"><?php echo formatFileSize($docStats['total_size']); ?></h5>
                                                <p class="card-text">Storage</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Customer & Lead Reports</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="customers.php">Customer Report</a>
                                        <span class="badge badge-primary badge-pill"><?php echo $customerStats['total_customers']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="leads.php">Lead Report</a>
                                        <span class="badge badge-primary badge-pill"><?php echo $leadStats['total_leads']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="sales.php">Sales Report</a>
                                        <span class="badge badge-primary badge-pill"><?php echo $leadStats['converted_leads']; ?> converted</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Activity Reports</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="communications.php">Communication Report</a>
                                        <span class="badge badge-primary badge-pill"><?php echo $commStats['total_communications']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="tasks.php">Task Report</a>
                                        <span class="badge badge-primary badge-pill"><?php echo $taskStats['total_tasks']; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Document Reports</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="documents.php">Document Report</a>
                                        <span class="badge badge-primary badge-pill"><?php echo $docStats['total_documents']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="documents.php?type=image">Image Documents</a>
                                        <span class="badge badge-primary badge-pill"><?php echo $docStats['image_count']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="documents.php?type=pdf">PDF Documents</a>
                                        <span class="badge badge-primary badge-pill"><?php echo $docStats['pdf_count']; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Export Options</h5>
                            </div>
                            <div class="card-body">
                                <p>Export reports in various formats:</p>
                                <ul>
                                    <li>PDF for printing and sharing</li>
                                    <li>Excel/CSV for data analysis</li>
                                    <li>Print-friendly versions</li>
                                </ul>
                                <p><small>Note: Export functionality will be available in the full version.</small></p>
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