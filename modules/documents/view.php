<?php
// View document details

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Document.php';

// Check if user is logged in
require_login();

// Get document ID from URL
$doc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$doc_id) {
    header('Location: index.php');
    exit;
}

// Get document details
$docObj = new Document();
$document = $docObj->findById($doc_id);

if (!$document) {
    header('Location: index.php');
    exit;
}

// Function to format file size
function format_file_size($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Function to get file type icon
function get_file_icon($file_type) {
    if (strpos($file_type, 'pdf') !== false) {
        return 'pdf';
    } elseif (strpos($file_type, 'image') !== false) {
        return 'image';
    } elseif (strpos($file_type, 'word') !== false) {
        return 'word';
    } else {
        return 'file';
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($document['name']); ?> - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2"><?php echo htmlspecialchars($document['name']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="download.php?id=<?php echo $document['id']; ?>" class="btn btn-sm btn-outline-secondary">Ladda ner</a>
                            <a href="delete.php?id=<?php echo $document['id']; ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Är du säker på att du vill ta bort detta dokument?')">Ta bort</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Dokumentdetaljer</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Namn:</th>
                                        <td><?php echo htmlspecialchars($document['name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Filtyp:</th>
                                        <td>
                                            <span class="badge badge-secondary">
                                                <?php 
                                                $fileType = explode('/', $document['file_type'])[1] ?? $document['file_type'];
                                                echo strtoupper($fileType);
                                                ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Filstorlek:</th>
                                        <td><?php echo format_file_size($document['file_size']); ?></td>
                                    </tr>
                                    <?php if ($document['company_name']): ?>
                                    <tr>
                                        <th>Kund:</th>
                                        <td>
                                            <a href="../customers/view.php?id=<?php echo $document['customer_id']; ?>">
                                                <?php echo htmlspecialchars($document['company_name']); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Uppladdad av:</th>
                                        <td><?php echo htmlspecialchars($document['uploaded_first_name'] . ' ' . $document['uploaded_last_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Uppladdad:</th>
                                        <td><?php echo format_datetime($document['created_at']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Förhandsgranskning</h5>
                            </div>
                            <div class="card-body text-center">
                                <?php if (strpos($document['file_type'], 'image') !== false): ?>
                                    <img src="download.php?id=<?php echo $document['id']; ?>" class="img-fluid" alt="Document preview">
                                <?php elseif (strpos($document['file_type'], 'pdf') !== false): ?>
                                    <p>Förhandsgranskning av PDF-filer stöds inte. <a href="download.php?id=<?php echo $document['id']; ?>">Ladda ner</a> för att visa.</p>
                                <?php else: ?>
                                    <p>Förhandsgranskning stöds inte för denna filtyp. <a href="download.php?id=<?php echo $document['id']; ?>">Ladda ner</a> för att visa.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Dokumentåtgärder</h5>
                            </div>
                            <div class="card-body">
                                <a href="download.php?id=<?php echo $document['id']; ?>" class="btn btn-primary btn-block">Ladda ner dokument</a>
                                <a href="upload.php" class="btn btn-secondary btn-block">Ladda upp nytt dokument</a>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Relaterad information</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($document['customer_id']): ?>
                                    <a href="../customers/view.php?id=<?php echo $document['customer_id']; ?>" class="btn btn-outline-primary btn-block">Visa kund</a>
                                <?php endif; ?>
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