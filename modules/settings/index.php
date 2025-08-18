<?php
// Settings main page

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Setting.php';

// Check if user is logged in and is admin
require_login();
check_admin();

// Get current user
$current_user = get_current_user_data();

// Get settings object
$settingObj = new Setting();

$error = '';
$success = '';

// Get current settings
$settings = $settingObj->getSystemSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $data = [
        'company_name' => sanitize_input($_POST['company_name']),
        'company_address' => sanitize_input($_POST['company_address']),
        'company_city' => sanitize_input($_POST['company_city']),
        'company_postal_code' => sanitize_input($_POST['company_postal_code']),
        'company_country' => sanitize_input($_POST['company_country']),
        'company_phone' => sanitize_input($_POST['company_phone']),
        'company_email' => sanitize_input($_POST['company_email']),
        'company_organization_number' => sanitize_input($_POST['company_organization_number']),
        'currency' => sanitize_input($_POST['currency']),
        'timezone' => sanitize_input($_POST['timezone']),
        'date_format' => sanitize_input($_POST['date_format']),
        'time_format' => sanitize_input($_POST['time_format']),
        'language' => sanitize_input($_POST['language']),
        'week_start' => sanitize_input($_POST['week_start']),
        'max_upload_size' => intval($_POST['max_upload_size']),
    ];
    
    // Basic validation
    if (empty($data['company_name'])) {
        $error = 'Företagsnamn är obligatoriskt';
    } elseif (!empty($data['company_email']) && !filter_var($data['company_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Ogiltig e-postadress';
    } else {
        // Update settings
        $updated = $settingObj->updateSystemSettings($data);
        
        if ($updated) {
            $success = 'Inställningar uppdaterade';
            // Refresh settings
            $settings = $settingObj->getSystemSettings();
        } else {
            $error = 'Kunde inte uppdatera inställningar. Försök igen.';
        }
    }
}

// Available timezones
$timezones = [
    'Europe/Stockholm' => 'Sverige (Stockholm)',
    'Europe/Oslo' => 'Norge (Oslo)',
    'Europe/Copenhagen' => 'Danmark (Köpenhamn)',
    'Europe/Helsinki' => 'Finland (Helsingfors)',
];

// Available currencies
$currencies = [
    'SEK' => 'SEK (Svensk krona)',
    'NOK' => 'NOK (Norsk krona)',
    'DKK' => 'DKK (Dansk krona)',
    'EUR' => 'EUR (Euro)',
];

// Available languages
$languages = [
    'sv' => 'Svenska',
    'en' => 'Engelska',
];

// Available date formats
$date_formats = [
    'Y-m-d' => date('Y-m-d') . ' (ÅÅÅÅ-MM-DD)',
    'd/m/Y' => date('d/m/Y') . ' (DD/MM/ÅÅÅÅ)',
    'd-m-Y' => date('d-m-Y') . ' (DD-MM-ÅÅÅÅ)',
];

// Available time formats
$time_formats = [
    'H:i:s' => date('H:i:s') . ' (24-timmars)',
    'h:i:s A' => date('h:i:s A') . ' (12-timmars)',
];

// Week start options
$week_starts = [
    '0' => 'Söndag',
    '1' => 'Måndag',
    '2' => 'Tisdag',
    '3' => 'Onsdag',
    '4' => 'Torsdag',
    '5' => 'Fredag',
    '6' => 'Lördag',
];
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inställningar - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Systeminställningar</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="company-tab" data-toggle="tab" href="#company" role="tab">Företag</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="system-tab" data-toggle="tab" href="#system" role="tab">System</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab">E-post</a>
                            </li>
                        </ul>
                        
                        <form method="POST" class="mt-4">
                            <div class="tab-content" id="settingsTabsContent">
                                <!-- Company Settings Tab -->
                                <div class="tab-pane fade show active" id="company" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_name">Företagsnamn *</label>
                                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                                       value="<?php echo htmlspecialchars($settings['company_name']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="company_organization_number">Organisationsnummer</label>
                                                <input type="text" class="form-control" id="company_organization_number" name="company_organization_number" 
                                                       value="<?php echo htmlspecialchars($settings['company_organization_number']); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="company_address">Adress</label>
                                                <input type="text" class="form-control" id="company_address" name="company_address" 
                                                       value="<?php echo htmlspecialchars($settings['company_address']); ?>">
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="company_postal_code">Postnummer</label>
                                                        <input type="text" class="form-control" id="company_postal_code" name="company_postal_code" 
                                                               value="<?php echo htmlspecialchars($settings['company_postal_code']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="company_city">Ort</label>
                                                        <input type="text" class="form-control" id="company_city" name="company_city" 
                                                               value="<?php echo htmlspecialchars($settings['company_city']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_country">Land</label>
                                                <select class="form-control" id="company_country" name="company_country">
                                                    <option value="SE" <?php echo $settings['company_country'] == 'SE' ? 'selected' : ''; ?>>Sverige</option>
                                                    <option value="NO" <?php echo $settings['company_country'] == 'NO' ? 'selected' : ''; ?>>Norge</option>
                                                    <option value="DK" <?php echo $settings['company_country'] == 'DK' ? 'selected' : ''; ?>>Danmark</option>
                                                    <option value="FI" <?php echo $settings['company_country'] == 'FI' ? 'selected' : ''; ?>>Finland</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="company_phone">Telefon</label>
                                                <input type="text" class="form-control" id="company_phone" name="company_phone" 
                                                       value="<?php echo htmlspecialchars($settings['company_phone']); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="company_email">E-post</label>
                                                <input type="email" class="form-control" id="company_email" name="company_email" 
                                                       value="<?php echo htmlspecialchars($settings['company_email']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- System Settings Tab -->
                                <div class="tab-pane fade" id="system" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="currency">Valuta</label>
                                                <select class="form-control" id="currency" name="currency">
                                                    <?php foreach ($currencies as $code => $name): ?>
                                                        <option value="<?php echo $code; ?>" <?php echo $settings['currency'] == $code ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($name); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="timezone">Tidszon</label>
                                                <select class="form-control" id="timezone" name="timezone">
                                                    <?php foreach ($timezones as $tz => $name): ?>
                                                        <option value="<?php echo $tz; ?>" <?php echo $settings['timezone'] == $tz ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($name); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="language">Språk</label>
                                                <select class="form-control" id="language" name="language">
                                                    <?php foreach ($languages as $code => $name): ?>
                                                        <option value="<?php echo $code; ?>" <?php echo $settings['language'] == $code ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($name); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date_format">Datumformat</label>
                                                <select class="form-control" id="date_format" name="date_format">
                                                    <?php foreach ($date_formats as $format => $example): ?>
                                                        <option value="<?php echo $format; ?>" <?php echo $settings['date_format'] == $format ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($example); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="time_format">Tidsformat</label>
                                                <select class="form-control" id="time_format" name="time_format">
                                                    <?php foreach ($time_formats as $format => $example): ?>
                                                        <option value="<?php echo $format; ?>" <?php echo $settings['time_format'] == $format ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($example); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="week_start">Veckans start</label>
                                                <select class="form-control" id="week_start" name="week_start">
                                                    <?php foreach ($week_starts as $value => $name): ?>
                                                        <option value="<?php echo $value; ?>" <?php echo $settings['week_start'] == $value ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($name); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="max_upload_size">Max filstorlek (bytes)</label>
                                                <input type="number" class="form-control" id="max_upload_size" name="max_upload_size" 
                                                       value="<?php echo htmlspecialchars($settings['max_upload_size']); ?>">
                                                <small class="form-text text-muted">
                                                    Nuvarande: <?php echo format_file_size($settings['max_upload_size']); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Email Settings Tab -->
                                <div class="tab-pane fade" id="email" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="smtp_host">SMTP-server</label>
                                                <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                                       value="<?php echo htmlspecialchars($settingObj->get('smtp_host') ?? ''); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="smtp_port">SMTP-port</label>
                                                <input type="text" class="form-control" id="smtp_port" name="smtp_port" 
                                                       value="<?php echo htmlspecialchars($settingObj->get('smtp_port') ?? '587'); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="smtp_username">SMTP-användarnamn</label>
                                                <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                                       value="<?php echo htmlspecialchars($settingObj->get('smtp_username') ?? ''); ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="smtp_password">SMTP-lösenord</label>
                                                <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                                       value="<?php echo htmlspecialchars($settingObj->get('smtp_password') ?? ''); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="smtp_encryption">Kryptering</label>
                                                <select class="form-control" id="smtp_encryption" name="smtp_encryption">
                                                    <option value="tls" <?php echo ($settingObj->get('smtp_encryption') ?? 'tls') == 'tls' ? 'selected' : ''; ?>>TLS</option>
                                                    <option value="ssl" <?php echo ($settingObj->get('smtp_encryption') ?? 'tls') == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                                    <option value="" <?php echo ($settingObj->get('smtp_encryption') ?? 'tls') == '' ? 'selected' : ''; ?>>Ingen</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="email_from">Avsändaradress</label>
                                                <input type="email" class="form-control" id="email_from" name="email_from" 
                                                       value="<?php echo htmlspecialchars($settingObj->get('email_from') ?? ''); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="email_from_name">Avsändarnamn</label>
                                                <input type="text" class="form-control" id="email_from_name" name="email_from_name" 
                                                       value="<?php echo htmlspecialchars($settingObj->get('email_from_name') ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Spara inställningar</button>
                        </form>
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