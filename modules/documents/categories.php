<?php
// Document categories and statistics

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Document.php';

// Check if user is logged in
require_login();

// Get document statistics
$docObj = new Document();
$stats = $docObj->getDocumentStats();

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
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentkategorier - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Dokumentkategorier</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="upload.php" class="btn btn-sm btn-outline-secondary">Ladda upp dokument</a>
                            <a href="index.php" class="btn btn-sm btn-outline-secondary">Alla dokument</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card-deck mb-4">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $stats['total_documents']; ?></h5>
                                    <p class="card-text">Totalt antal dokument</p>
                                </div>
                            </div>
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo format_file_size($stats['total_size']); ?></h5>
                                    <p class="card-text">Total lagringsutrymme</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Filtypsdistribution</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Filtyp</th>
                                                <th>Antal dokument</th>
                                                <th>Procent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>PDF-dokument</td>
                                                <td><?php echo $stats['pdf_count']; ?></td>
                                                <td>
                                                    <?php 
                                                    $pdfPercent = $stats['total_documents'] > 0 ? round(($stats['pdf_count'] / $stats['total_documents']) * 100, 1) : 0;
                                                    echo $pdfPercent . '%';
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Bilder</td>
                                                <td><?php echo $stats['image_count']; ?></td>
                                                <td>
                                                    <?php 
                                                    $imagePercent = $stats['total_documents'] > 0 ? round(($stats['image_count'] / $stats['total_documents']) * 100, 1) : 0;
                                                    echo $imagePercent . '%';
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Word-dokument</td>
                                                <td><?php echo $stats['word_count']; ?></td>
                                                <td>
                                                    <?php 
                                                    $wordPercent = $stats['total_documents'] > 0 ? round(($stats['word_count'] / $stats['total_documents']) * 100, 1) : 0;
                                                    echo $wordPercent . '%';
                                                    ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Lagringsstatistik</h5>
                            </div>
                            <div class="card-body">
                                <p>Översikt över dokumentlagring i systemet:</p>
                                <ul>
                                    <li>Totalt antal dokument: <?php echo $stats['total_documents']; ?></li>
                                    <li>Totalt lagringsutrymme använt: <?php echo format_file_size($stats['total_size']); ?></li>
                                    <li>Genomsnittlig filstorlek: <?php echo $stats['total_documents'] > 0 ? format_file_size($stats['total_size'] / $stats['total_documents']) : '0 bytes'; ?></li>
                                </ul>
                                
                                <h5>Fördelning efter filtyp</h5>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $pdfPercent; ?>%" aria-valuenow="<?php echo $pdfPercent; ?>" aria-valuemin="0" aria-valuemax="100">
                                        PDF (<?php echo $pdfPercent; ?>%)
                                    </div>
                                </div>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $imagePercent; ?>%" aria-valuenow="<?php echo $imagePercent; ?>" aria-valuemin="0" aria-valuemax="100">
                                        Bilder (<?php echo $imagePercent; ?>%)
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $wordPercent; ?>%" aria-valuenow="<?php echo $wordPercent; ?>" aria-valuemin="0" aria-valuemax="100">
                                        Word (<?php echo $wordPercent; ?>%)
                                    </div>
                                </div>
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