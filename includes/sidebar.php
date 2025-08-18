<?php
// Navigation sidebar

// Check if we're in a subdirectory to adjust the path to assets
$depth = substr_count($_SERVER['REQUEST_URI'], '/') - 2;
$basePath = str_repeat('../', max(0, $depth));
?>

<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <span data-feather="home"></span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="modules/customers/">
                    <span data-feather="users"></span>
                    Kunder
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="modules/contacts/">
                    <span data-feather="user"></span>
                    Kontakter
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="modules/leads/">
                    <span data-feather="target"></span>
                    Leads
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="modules/communications/">
                    <span data-feather="message-square"></span>
                    Kommunikation
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="modules/tasks/">
                    <span data-feather="check-circle"></span>
                    Uppgifter
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="modules/calendar/">
                    <span data-feather="calendar"></span>
                    Kalender
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="modules/documents/">
                    <span data-feather="folder"></span>
                    Dokument
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="modules/reports/">
                    <span data-feather="bar-chart-2"></span>
                    Rapporter
                </a>
            </li>
        </ul>
    </div>
</nav>