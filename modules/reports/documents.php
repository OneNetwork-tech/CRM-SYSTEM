<?php
// Document report

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

// Get document report data
$reportObj = new Report();
$documents = $reportObj->getDocumentReport($start_date, $end_date, $type);

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
    <title>Document Report - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Document Report</h1>
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
                                    <option value="application/pdf" <?php echo ($type == 'application/pdf') ? 'selected' : ''; ?>>PDF</option>
                                    <option value="image/jpeg" <?php echo ($type == 'image/jpeg') ? 'selected' : ''; ?>>JPEG</option>
                                    <option value="image/png" <?php echo ($type == 'image/png') ? 'selected' : ''; ?>>PNG</option>
                                    <option value="application/msword" <?php echo ($type == 'application/msword') ? 'selected' : ''; ?>>Word Document</option>
                                    <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document" <?php echo ($type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') ? 'selected' : ''; ?>>Word Document (DOCX)</option>
                                </select>
                            </div>
                            
                            <button class="btn btn-outline-success mr-2" type="submit">Filter</button>
                            <a href="documents.php" class="btn btn-outline-secondary">Clear</a>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Customer</th>
                                <th>File Type</th>
                                <th>Size</th>
                                <th>Uploaded By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($documents)): ?>
                                <?php foreach ($documents as $document): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($document['name']); ?></td>
                                        <td>
                                            <?php if ($document['company_name']): ?>
                                                <?php echo htmlspecialchars($document['company_name']); ?>
                                            <?php else: ?>
                                                Not assigned
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                <?php 
                                                $fileType = explode('/', $document['file_type'])[1] ?? $document['file_type'];
                                                echo strtoupper($fileType);
                                                ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatFileSize($document['file_size']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($document['uploaded_first_name'] . ' ' . $document['uploaded_last_name']); ?>
                                        </td>
                                        <td><?php echo format_date($document['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No documents found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p>Total documents: <?php echo count($documents); ?></p>
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