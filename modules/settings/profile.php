<?php
// User profile settings

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/User.php';
require_once '../../classes/Setting.php';

// Check if user is logged in
require_login();

// Get current user
$current_user = get_current_user_data();
$user_id = $current_user['id'];

// Get user and setting objects
$userObj = new User();
$settingObj = new Setting();

$error = '';
$success = '';

// Get user data
$user = $userObj->findById($user_id);

// Get notification settings
$notification_settings = $settingObj->getNotificationSettings($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Process profile update
        $data = [
            'first_name' => sanitize_input($_POST['first_name']),
            'last_name' => sanitize_input($_POST['last_name']),
            'email' => sanitize_input($_POST['email']),
        ];
        
        // Basic validation
        if (empty($data['first_name']) || empty($data['last_name'])) {
            $error = 'Förnamn och efternamn är obligatoriska';
        } elseif (empty($data['email'])) {
            $error = 'E-post är obligatorisk';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Ogiltig e-postadress';
        } else {
            // Update user
            $updated = $userObj->update($user_id, $data);
            
            if ($updated !== false) {
                $success = 'Profil uppdaterad';
                // Refresh user data
                $user = $userObj->findById($user_id);
            } else {
                $error = 'Kunde inte uppdatera profil. Försök igen.';
            }
        }
    } elseif (isset($_POST['change_password'])) {
        // Process password change
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            $error = 'Nuvarande lösenord är felaktigt';
        } elseif (strlen($new_password) < 6) {
            $error = 'Lösenordet måste vara minst 6 tecken';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Lösenorden matchar inte';
        } else {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $updated = $userObj->updatePassword($user_id, $hashed_password);
            
            if ($updated) {
                $success = 'Lösenord uppdaterat';
            } else {
                $error = 'Kunde inte uppdatera lösenord. Försök igen.';
            }
        }
    } elseif (isset($_POST['update_notifications'])) {
        // Process notification settings update
        $data = [
            'notification_email' => isset($_POST['notification_email']) ? '1' : '0',
            'notification_dashboard' => isset($_POST['notification_dashboard']) ? '1' : '0',
            'notification_tasks' => isset($_POST['notification_tasks']) ? '1' : '0',
            'notification_leads' => isset($_POST['notification_leads']) ? '1' : '0',
        ];
        
        // Update notification settings
        $updated = $settingObj->updateNotificationSettings($user_id, $data);
        
        if ($updated) {
            $success = 'Notifikationsinställningar uppdaterade';
            // Refresh notification settings
            $notification_settings = $settingObj->getNotificationSettings($user_id);
        } else {
            $error = 'Kunde inte uppdatera notifikationsinställningar. Försök igen.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Min profil - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Min profil</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab">Profil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab">Lösenord</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="notifications-tab" data-toggle="tab" href="#notifications" role="tab">Notifikationer</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="profileTabsContent">
                            <!-- Profile Tab -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                                <form method="POST" class="mt-4">
                                    <input type="hidden" name="update_profile" value="1">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="first_name">Förnamn *</label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                                       value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="last_name">Efternamn *</label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                                       value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="email">E-post *</label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="role">Roll</label>
                                                <input type="text" class="form-control" id="role" name="role" 
                                                       value="<?php echo htmlspecialchars(ucfirst($user['role'])); ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Uppdatera profil</button>
                                </form>
                            </div>
                            
                            <!-- Password Tab -->
                            <div class="tab-pane fade" id="password" role="tabpanel">
                                <form method="POST" class="mt-4">
                                    <input type="hidden" name="change_password" value="1">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="current_password">Nuvarande lösenord</label>
                                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="new_password">Nytt lösenord</label>
                                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                                <small class="form-text text-muted">Minst 6 tecken</small>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="confirm_password">Bekräfta nytt lösenord</label>
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Ändra lösenord</button>
                                </form>
                            </div>
                            
                            <!-- Notifications Tab -->
                            <div class="tab-pane fade" id="notifications" role="tabpanel">
                                <form method="POST" class="mt-4">
                                    <input type="hidden" name="update_notifications" value="1">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>E-postnotifikationer</h5>
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="notification_email" name="notification_email" 
                                                       <?php echo isset($notification_settings['notification_email']) && $notification_settings['notification_email'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="notification_email">Skicka e-postnotifikationer</label>
                                            </div>
                                            
                                            <h5>Dashboard-notifikationer</h5>
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="notification_dashboard" name="notification_dashboard" 
                                                       <?php echo isset($notification_settings['notification_dashboard']) && $notification_settings['notification_dashboard'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="notification_dashboard">Visa notifikationer på dashboard</label>
                                            </div>
                                            
                                            <h5>Uppgiftsnotifikationer</h5>
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="notification_tasks" name="notification_tasks" 
                                                       <?php echo isset($notification_settings['notification_tasks']) && $notification_settings['notification_tasks'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="notification_tasks">Visa uppgiftsnotifikationer</label>
                                            </div>
                                            
                                            <h5>Lead-notifikationer</h5>
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="notification_leads" name="notification_leads" 
                                                       <?php echo isset($notification_settings['notification_leads']) && $notification_settings['notification_leads'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="notification_leads">Visa lead-notifikationer</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Uppdatera notifikationer</button>
                                </form>
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