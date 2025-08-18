<?php
// Ladda upp nytt dokument

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Document.php';
require_once '../../classes/Customer.php';

// Kontrollera om användare är inloggad
require_login();

// Hämta nuvarande användare
$current_user = get_current_user_data();

// Kontrollera om customer_id skickas med i URL
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

// Hämta kunder för dropdown
$customerObj = new Customer();
$customers = $customerObj->getAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processa filuppladdning
    if (!isset($_FILES['document']) || $_FILES['document']['error'] === UPLOAD_ERR_NO_FILE) {
        $error = 'Vänligen välj en fil att ladda upp';
    } else {
        $file = $_FILES['document'];
        
        // Validera fil
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Fel vid uppladdning av fil. Försök igen';
        } else {
            // Kontrollera filtyp
            $allowedTypes = [
                'application/pdf', // PDF
                'image/jpeg', // JPEG-bild
                'image/png', // PNG-bild
                'application/msword', // Word .doc
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // Word .docx
            ];
            
            if (!in_array($file['type'], $allowedTypes)) {
                $error = 'Ogiltig filtyp. Endast PDF, JPEG, PNG och Word-dokument är tillåtna';
            } elseif ($file['size'] > MAX_UPLOAD_SIZE) {
                $error = 'Filen är för stor. Maximal filstorlek är ' . format_file_size(MAX_UPLOAD_SIZE);
            } else {
                // Skapa dokumentdata
                $data = [
                    'customer_id' => !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null,
                    'name' => sanitize_input($_POST['name']),
                    'uploaded_by' => $current_user['id']
                ];
                
                // Skapa unikt filnamn
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $unique_file_name = uniqid() . '.' . $file_extension;
                
                // Bestäm upload-katalog
                $upload_dir = '../../assets/uploads/documents/';
                if (strpos($file['type'], 'image') === 0) {
                    $upload_dir = '../../assets/uploads/documents/images/';
                }
                
                // Skapa katalog om den inte existerar
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_path = $upload_dir . $unique_file_name;
                
                // Flytta uppladdad fil
                if (move_uploaded_file($file['tmp_name'], $file_path)) {
                    // Spara dokumentinformation i databasen
                    $docObj = new Document();
                    $data['file_path'] = str_replace('../../', '', $file_path);
                    $data['file_type'] = $file['type'];
                    $data['file_size'] = $file['size'];
                    
                    $docId = $docObj->create($data);
                    
                    if ($docId) {
                        $success = 'Dokumentet har laddats upp';
                        // Omdirigera efter kort paus
                        header("refresh:2;url=view.php?id=$docId");
                    } else {
                        // Radera uppladdad fil om databassparning misslyckas
                        unlink($file_path);
                        $error = 'Kunde inte spara dokumentinformation. Försök igen';
                    }
                } else {
                    $error = 'Kunde inte flytta uppladdad fil. Kontrollera mappbehörigheter';
                }
            }
        }
    }
}

// Standardvärden
$document = [
    'customer_id' => $customer_id,
    'name' => ''
];

/**
 * Formaterar filstorlek till läsbar text
 */
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
    <title>Ladda upp dokument - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../../includes/sidebar.php'; ?>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Ladda upp dokument</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_id">Kund</label>
                                <select class="form-control" id="customer_id" name="customer_id">
                                    <option value="">Välj kund</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>" <?php echo $document['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($customer['company_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="name">Dokumentnamn</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($document['name']); ?>">
                                <small class="form-text text-muted">Om inget namn anges kommer filnamnet att användas</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="document">Välj dokument *</label>
                                <input type="file" class="form-control-file" id="document" name="document" required>
                                <small class="form-text text-muted">
                                    Tillåtna filtyper: PDF, JPEG, PNG, Word-dokument<br>
                                    Maximal filstorlek: <?php echo format_file_size(MAX_UPLOAD_SIZE); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Ladda upp dokument</button>
                    <a href="index.php" class="btn btn-secondary">Avbryt</a>
                </form>
            </main>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/app.js"></script>
    
    <script>
    // Uppdatera filnamn i etikett när fil väljs
    $('#document').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.form-text').html('Vald fil: ' + fileName);
    });
    </script>
</body>
</html>