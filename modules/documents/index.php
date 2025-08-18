<?php
// Document listing

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Document.php';
require_once '../../classes/Customer.php';

// Check if user is logged in
require_login();

// Get all documents
$docObj = new Document();
$documents = $docObj->getAll();

// Check if we're filtering by customer
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
if ($customer_id) {
    $documents = $docObj->getByCustomerId($customer_id);
    
    // Get customer info for breadcrumb
    $customerObj = new Customer();
    $customer = $customerObj->findById($customer_id);
}

// Filter by file type if specified
$file_type = isset($_GET['file_type']) ? $_GET['file_type'] : '';
if ($file_type) {
    $documents = $docObj->getByFileType($file_type);
}

// Get document stats for dashboard
$stats = $docObj->getDocumentStats();

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
    <title>Dokument - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Dokument</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="upload.php" class="btn btn-sm btn-outline-secondary">Ladda upp dokument</a>
                            <a href="categories.php" class="btn btn-sm btn-outlin

                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <input class="form-control" type="search" name="search" placeholder="Sök dokument..." aria-label="Sök" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            
                            <div class="form-group mr-2">
                                <select class="form-control" name="customer_id">
                                    <option value="">Alla kunder</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>" <?php echo $customer_id == $customer['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($customer['company_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group mr-2">
                                <select class="form-control" name="file_type">
                                    <option value="">Alla filtyper</option>
                                    <option value="pdf" <?php echo $file_type === 'pdf' ? 'selected' : ''; ?>>PDF</option>
                                    <option value="image" <?php echo $file_type === 'image' ? 'selected' : ''; ?>>Bilder</option>
                                    <option value="word" <?php echo $file_type === 'word' ? 'selected' : ''; ?>>Word-dokument</option>
                                </select>
                            </div>
                            
                            <button class="btn btn-outline-success mr-2" type="submit">Filtrera</button>
                            <a href="index.php" class="btn btn-outline-secondary">Rensa</a>
                        </form>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card-deck">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $stats['total_documents']; ?></h5>
                                    <p class="card-text">Total Documents</p>
                                </div>
                            </div>
                            <div class="card text-white bg-info">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo formatFileSize($stats['total_size']); ?></h5>
                                    <p class="card-text">Total Size</p>
                                </div>
                            </div>
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $stats['pdf_count']; ?></h5>
                                    <p class="card-text">PDF Documents</p>
                                </div>
                            </div>
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $stats['image_count']; ?></h5>
                                    <p class="card-text">Images</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Namn</th>
                                <th>Kund</th>
                                <th>Filtyp</th>
                                <th>Storlek</th>
                                <th>Uppladdad av</th>
                                <th>Datum</th>
                                <th>Åtgärder</th>
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
                                                Ej tilldelad
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
                                        <td>
                                            <a href="view.php?id=<?php echo $document['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                            <a href="download.php?id=<?php echo $document['id']; ?>" class="btn btn-sm btn-outline-secondary">Ladda ner</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Inga dokument hittades</td>
                                </tr>
                            <?php endif; ?>
                        
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p>Totalt antal dokument: <?php echo count($documents); ?></p>
                </div>
                        </tbody>
                    </table>
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