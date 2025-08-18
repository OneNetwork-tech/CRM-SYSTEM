<?php
// User management settings

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/User.php';

// Check if user is logged in and is admin
require_login();
check_admin();

// Get user object
$userObj = new User();

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        // Add new user
        $data = [
            'first_name' => sanitize_input($_POST['first_name']),
            'last_name' => sanitize_input($_POST['last_name']),
            'email' => sanitize_input($_POST['email']),
            'password' => $_POST['password'],
            'role' => sanitize_input($_POST['role']),
        ];
        
        // Basic validation
        if (empty($data['first_name']) || empty($data['last_name'])) {
            $error = 'Förnamn och efternamn är obligatoriska';
        } elseif (empty($data['email'])) {
            $error = 'E-post är obligatorisk';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Ogiltig e-postadress';
        } elseif (empty($data['password']) || strlen($data['password']) < 6) {
            $error = 'Lösenord måste vara minst 6 tecken';
        } else {
            // Check if email already exists
            if ($userObj->findByEmail($data['email'])) {
                $error = 'E-postadressen är redan registrerad';
            } else {
                // Create user
                $userId = $userObj->create($data);
                
                if ($userId) {
                    $success = 'Användare skapad';
                } else {
                    $error = 'Kunde inte skapa användare. Försök igen.';
                }
            }
        }
    } elseif (isset($_POST['edit_user'])) {
        // Edit user
        $user_id = intval($_POST['user_id']);
        $data = [
            'first_name' => sanitize_input($_POST['first_name']),
            'last_name' => sanitize_input($_POST['last_name']),
            'email' => sanitize_input($_POST['email']),
            'role' => sanitize_input($_POST['role']),
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
                $success = 'Användare uppdaterad';
            } else {
                $error = 'Kunde inte uppdatera användare. Försök igen.';
            }
        }
    } elseif (isset($_POST['delete_user'])) {
        // Delete user
        $user_id = intval($_POST['user_id']);
        
        // Prevent deleting oneself
        if ($user_id == $_SESSION['user_id']) {
            $error = 'Du kan inte ta bort dig själv';
        } else {
            $deleted = $userObj->delete($user_id);
            
            if ($deleted) {
                $success = 'Användare borttagen';
            } else {
                $error = 'Kunde inte ta bort användare. Försök igen.';
            }
        }
    }
}

// Get all users
$users = $userObj->getAll();
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Användare - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Användare</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#addUserModal">
                            Lägg till användare
                        </button>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Namn</th>
                                <th>E-post</th>
                                <th>Roll</th>
                                <th>Skapad</th>
                                <th>Åtgärder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $user['role'] == 'admin' ? 'danger' : 'secondary'; ?>">
                                                <?php echo $user['role'] == 'admin' ? 'Administratör' : 'Användare'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo format_date($user['created_at']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name']); ?>', '<?php echo htmlspecialchars($user['last_name']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo $user['role']; ?>')">
                                                Redigera
                                            </button>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Är du säker på att du vill ta bort denna användare?')">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <input type="hidden" name="delete_user" value="1">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Ta bort</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Inga användare hittades</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="add_user" value="1">
                    <div class="modal-header">
                        <h5 class="modal-title">Lägg till användare</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="add_first_name">Förnamn *</label>
                            <input type="text" class="form-control" id="add_first_name" name="first_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_last_name">Efternamn *</label>
                            <input type="text" class="form-control" id="add_last_name" name="last_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_email">E-post *</label>
                            <input type="email" class="form-control" id="add_email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_password">Lösenord *</label>
                            <input type="password" class="form-control" id="add_password" name="password" required>
                            <small class="form-text text-muted">Minst 6 tecken</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_role">Roll</label>
                            <select class="form-control" id="add_role" name="role">
                                <option value="user">Användare</option>
                                <option value="admin">Administratör</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Avbryt</button>
                        <button type="submit" class="btn btn-primary">Lägg till användare</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="edit_user" value="1">
                    <input type="hidden" id="edit_user_id" name="user_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Redigera användare</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_first_name">Förnamn *</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_last_name">Efternamn *</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_email">E-post *</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_role">Roll</label>
                            <select class="form-control" id="edit_role" name="role">
                                <option value="user">Användare</option>
                                <option value="admin">Administratör</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Avbryt</button>
                        <button type="submit" class="btn btn-primary">Uppdatera användare</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/app.js"></script>
    <script>
        function editUser(id, firstName, lastName, email, role) {
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_first_name').value = firstName;
            document.getElementById('edit_last_name').value = lastName;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
            $('#editUserModal').modal('show');
        }
    </script>
</body>
</html>