<?php
// Delete document

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

// Check if confirmation is provided
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Get document details before deleting
    $docObj = new Document();
    $document = $docObj->findById($doc_id);
    
    if (!$document) {
        header('Location: index.php');
        exit;
    }
    
    // Delete the file from the server
    $file_path = '../../' . $document['file_path'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    
    // Delete the document record from the database
    $deleted = $docObj->delete($doc_id);
    
    if ($deleted) {
        // Redirect to document list with success message
        $_SESSION['message'] = 'Document deleted successfully';
        header('Location: index.php');
        exit;
    } else {
        // Redirect to document list with error message
        $_SESSION['error'] = 'Failed to delete document';
        header("Location: view.php?id=$doc_id");
        exit;
    }
} else {
    // Show confirmation page
    $docObj = new Document();
    $document = $docObj->findById($doc_id);
    
    if (!$document) {
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Document - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Delete Document</h1>
                </div>

                <div class="alert alert-warning">
                    <h4>Confirm Deletion</h4>
                    <p>Are you sure you want to delete the document <strong><?php echo htmlspecialchars($document['name']); ?></strong>?</p>
                    <p>This action cannot be undone and will permanently remove the file from the server.</p>
                    
                    <a href="delete.php?id=<?php echo $doc_id; ?>&confirm=yes" class="btn btn-danger">Yes, Delete Document</a>
                    <a href="view.php?id=<?php echo $doc_id; ?>" class="btn btn-secondary">Cancel</a>
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