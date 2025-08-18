<?php
// Contacts list

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Contact.php';

// Check if user is logged in
require_login();

// Get filter parameters
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Get contacts
$contactObj = new Contact();
if ($search) {
    $contacts = $contactObj->search($search);
} else {
    $contacts = $contactObj->getAll();
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakter - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Kontakter</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="add.php" class="btn btn-sm btn-outline-secondary">Lägg till kontakt</a>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group mr-2">
                                <input class="form-control" type="search" name="search" placeholder="Sök kontakter..." aria-label="Sök" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            
                            <button class="btn btn-outline-success mr-2" type="submit">Sök</button>
                            <a href="index.php" class="btn btn-outline-secondary">Rensa</a>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Namn</th>
                                <th>Företag</th>
                                <th>Position</th>
                                <th>E-post</th>
                                <th>Telefon</th>
                                <th>Primär</th>
                                <th>Åtgärder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($contacts)): ?>
                                <?php foreach ($contacts as $contact): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></td>
                                        <td>
                                            <?php if ($contact['company_name']): ?>
                                                <a href="../customers/view.php?id=<?php echo $contact['customer_id']; ?>">
                                                    <?php echo htmlspecialchars($contact['company_name']); ?>
                                                </a>
                                            <?php else: ?>
                                                Ej tilldelad
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($contact['position']); ?></td>
                                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                        <td><?php echo format_phone($contact['phone']); ?></td>
                                        <td>
                                            <?php if ($contact['is_primary']): ?>
                                                <span class="badge badge-success">Ja</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="view.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                            <a href="edit.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-secondary">Redigera</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Inga kontakter hittades</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p>Totalt antal kontakter: <?php echo count($contacts); ?></p>
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