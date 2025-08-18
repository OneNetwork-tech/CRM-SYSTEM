<?php
// Document search

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Document.php';

// Check if user is logged in
require_login();

$query = '';
$documents = [];

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $query = sanitize_input($_GET['query']);
    
    // Search for documents
    $docObj = new Document();
    $documents = $docObj->search($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Documents - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Search Documents</h1>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2 flex-grow-1">
                                <input class="form-control w-100" type="search" name="query" placeholder="Search documents by name or customer" aria-label="Search" value="<?php echo htmlspecialchars($query); ?>" required>
                            </div>
                            <button class="btn btn-outline-success" type="submit">Search</button>
                            <a href="index.php" class="btn btn-outline-secondary ml-2">Clear</a>
                        </form>
                    </div>
                </div>

                <?php if (isset($_GET['query'])): ?>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($documents)): ?>
                                    <?php foreach ($documents as $document): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($document['name']); ?></td>
                                            <td>
                                                <?php if ($document['company_name']): ?>
                                                    <a href="../customers/view.php?id=<?php echo $document['customer_id']; ?>">
                                                        <?php echo htmlspecialchars($document['company_name']); ?>
                                                    </a>
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
                                            <td>
                                                <?php 
                                                // Format file size
                                                $bytes = $document['file_size'];
                                                if ($bytes >= 1073741824) {
                                                    echo number_format($bytes / 1073741824, 2) . ' GB';
                                                } elseif ($bytes >= 1048576) {
                                                    echo number_format($bytes / 1048576, 2) . ' MB';
                                                } elseif ($bytes >= 1024) {
                                                    echo number_format($bytes / 1024, 2) . ' KB';
                                                } else {
                                                    echo $bytes . ' bytes';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($document['uploaded_first_name'] . ' ' . $document['uploaded_last_name']); ?>
                                            </td>
                                            <td><?php echo format_date($document['created_at']); ?></td>
                                            <td>
                                                <a href="download.php?id=<?php echo $document['id']; ?>" class="btn btn-sm btn-outline-primary">Download</a>
                                                <a href="view.php?id=<?php echo $document['id']; ?>" class="btn btn-sm btn-outline-secondary">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No documents found matching your search criteria</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4>Search Documents</h4>
                        <p>Use the search box above to find documents by name or customer.</p>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/app.js"></script>
</body>
</html>