<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' . APP_NAME : APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php"><?php echo APP_NAME; ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="modules/customers/">Kunder</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="modules/contacts/">Kontakter</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="modules/leads/">Leads</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="modules/communications/">Kommunikation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="modules/tasks/">Uppgifter</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="modules/calendar/">Kalender</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="modules/documents/">Dokument</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="modules/reports/">Rapporter</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="profile.php">Profil</a>
                            <a class="dropdown-item" href="settings.php">Inställningar</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">Logga ut</a>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>