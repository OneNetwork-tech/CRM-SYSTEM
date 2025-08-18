<?php
// Leads pipeline view

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Lead.php';

// Check if user is logged in
require_login();

// Get all leads grouped by status
$leadObj = new Lead();
$new_leads = $leadObj->getByStatus('new');
$contacted_leads = $leadObj->getByStatus('contacted');
$qualified_leads = $leadObj->getByStatus('qualified');
$lost_leads = $leadObj->getByStatus('lost');
$converted_leads = $leadObj->getByStatus('converted');

// Lead status options
$status_options = [
    'new' => 'Ny',
    'contacted' => 'Kontaktad',
    'qualified' => 'Kvalificerad',
    'lost' => 'Förlorad',
    'converted' => 'Konverterad'
];
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads Pipeline - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .pipeline-column {
            min-height: 500px;
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
        }
        
        .lead-card {
            background: white;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 3px solid #007bff;
        }
        
        .lead-card.contact {
            border-left-color: #ffc107;
        }
        
        .lead-card.qualified {
            border-left-color: #007bff;
        }
        
        .lead-card.lost {
            border-left-color: #dc3545;
        }
        
        .lead-card.converted {
            border-left-color: #28a745;
        }
        
        .status-header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            color: white;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../../includes/sidebar.php'; ?>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Leads Pipeline</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="index.php" class="btn btn-sm btn-outline-secondary">Lista</a>
                            <a href="add.php" class="btn btn-sm btn-outline-secondary">Lägg till lead</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="status-header bg-info">
                            <h5><?php echo $status_options['new']; ?></h5>
                            <p><?php echo count($new_leads); ?> leads</p>
                        </div>
                        <div class="pipeline-column" id="new-column">
                            <?php foreach ($new_leads as $lead): ?>
                                <div class="lead-card">
                                    <h6><?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?></h6>
                                    <p class="mb-1"><?php echo htmlspecialchars($lead['company_name']); ?></p>
                                    <p class="mb-1"><small><?php echo htmlspecialchars($lead['email']); ?></small></p>
                                    <div class="d-flex justify-content-between">
                                        <a href="view.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                        <span class="text-muted"><small><?php echo format_date($lead['created_at']); ?></small></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($new_leads)): ?>
                                <p class="text-muted text-center">Inga leads</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="status-header bg-warning">
                            <h5><?php echo $status_options['contacted']; ?></h5>
                            <p><?php echo count($contacted_leads); ?> leads</p>
                        </div>
                        <div class="pipeline-column" id="contacted-column">
                            <?php foreach ($contacted_leads as $lead): ?>
                                <div class="lead-card contact">
                                    <h6><?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?></h6>
                                    <p class="mb-1"><?php echo htmlspecialchars($lead['company_name']); ?></p>
                                    <p class="mb-1"><small><?php echo htmlspecialchars($lead['email']); ?></small></p>
                                    <div class="d-flex justify-content-between">
                                        <a href="view.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                        <span class="text-muted"><small><?php echo format_date($lead['created_at']); ?></small></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($contacted_leads)): ?>
                                <p class="text-muted text-center">Inga leads</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="status-header bg-primary">
                            <h5><?php echo $status_options['qualified']; ?></h5>
                            <p><?php echo count($qualified_leads); ?> leads</p>
                        </div>
                        <div class="pipeline-column" id="qualified-column">
                            <?php foreach ($qualified_leads as $lead): ?>
                                <div class="lead-card qualified">
                                    <h6><?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?></h6>
                                    <p class="mb-1"><?php echo htmlspecialchars($lead['company_name']); ?></p>
                                    <p class="mb-1"><small><?php echo htmlspecialchars($lead['email']); ?></small></p>
                                    <div class="d-flex justify-content-between">
                                        <a href="view.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                        <span class="text-muted"><small><?php echo format_date($lead['created_at']); ?></small></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($qualified_leads)): ?>
                                <p class="text-muted text-center">Inga leads</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="status-header bg-danger">
                            <h5><?php echo $status_options['lost']; ?></h5>
                            <p><?php echo count($lost_leads); ?> leads</p>
                        </div>
                        <div class="pipeline-column" id="lost-column">
                            <?php foreach ($lost_leads as $lead): ?>
                                <div class="lead-card lost">
                                    <h6><?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?></h6>
                                    <p class="mb-1"><?php echo htmlspecialchars($lead['company_name']); ?></p>
                                    <p class="mb-1"><small><?php echo htmlspecialchars($lead['email']); ?></small></p>
                                    <div class="d-flex justify-content-between">
                                        <a href="view.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                        <span class="text-muted"><small><?php echo format_date($lead['created_at']); ?></small></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($lost_leads)): ?>
                                <p class="text-muted text-center">Inga leads</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="status-header bg-success">
                            <h5><?php echo $status_options['converted']; ?></h5>
                            <p><?php echo count($converted_leads); ?> leads</p>
                        </div>
                        <div class="pipeline-column" id="converted-column">
                            <?php foreach ($converted_leads as $lead): ?>
                                <div class="lead-card converted">
                                    <h6><?php echo htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']); ?></h6>
                                    <p class="mb-1"><?php echo htmlspecialchars($lead['company_name']); ?></p>
                                    <p class="mb-1"><small><?php echo htmlspecialchars($lead['email']); ?></small></p>
                                    <div class="d-flex justify-content-between">
                                        <a href="view.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-primary">Visa</a>
                                        <span class="text-muted"><small><?php echo format_date($lead['created_at']); ?></small></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($converted_leads)): ?>
                                <p class="text-muted text-center">Inga leads</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Översikt</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2 text-center">
                                        <h3><?php echo count($new_leads); ?></h3>
                                        <p>Nya leads</p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <h3><?php echo count($contacted_leads); ?></h3>
                                        <p>Kontaktade</p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <h3><?php echo count($qualified_leads); ?></h3>
                                        <p>Kvalificerade</p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <h3><?php echo count($lost_leads); ?></h3>
                                        <p>Förlorade</p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <h3><?php echo count($converted_leads); ?></h3>
                                        <p>Konverterade</p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <h3><?php echo count($new_leads) + count($contacted_leads) + count($qualified_leads) + count($lost_leads) + count($converted_leads); ?></h3>
                                        <p>Totalt</p>
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