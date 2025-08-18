<?php
// Projects calendar view

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Project.php';

// Check if user is logged in
require_login();

// Get current user
$current_user = get_current_user_data();

// Get filter parameters
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Create timestamp for first day of month
$timestamp = mktime(0, 0, 0, $month, 1, $year);

// Get number of days in month
$daysInMonth = date('t', $timestamp);

// Get day of week for first day (0 = Sunday, 6 = Saturday)
$firstDayOfWeek = date('w', $timestamp);

// Get projects for this month
$projectObj = new Project();
$start_date = date('Y-m-01', $timestamp);
$end_date = date('Y-m-t', $timestamp);
$projects = $projectObj->getProjectsByDateRange($start_date, $end_date);

// Group projects by date for easier display
$projectsByDate = [];
foreach ($projects as $project) {
    // Add start date projects
    if ($project['start_date']) {
        $startDate = date('Y-m-d', strtotime($project['start_date']));
        if (!isset($projectsByDate[$startDate])) {
            $projectsByDate[$startDate] = [];
        }
        $projectsByDate[$startDate][] = $project;
    }
    
    // Add end date projects (if different from start date)
    if ($project['end_date'] && $project['end_date'] != $project['start_date']) {
        $endDate = date('Y-m-d', strtotime($project['end_date']));
        if (!isset($projectsByDate[$endDate])) {
            $projectsByDate[$endDate] = [];
        }
        $projectsByDate[$endDate][] = $project;
    }
}

// Previous and next month links
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

// Priority options
$priority_options = [
    'low' => 'Låg',
    'medium' => 'Medium',
    'high' => 'Hög'
];

// Status options
$status_options = [
    'planned' => 'Planerad',
    'in_progress' => 'Pågående',
    'on_hold' => 'Pausad',
    'completed' => 'Slutförd',
    'cancelled' => 'Avbruten'
];
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projektkalender - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background-color: #dee2e6;
            border: 1px solid #dee2e6;
        }
        
        .calendar-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 10px 0;
            font-weight: bold;
        }
        
        .calendar-day {
            background-color: white;
            min-height: 100px;
            padding: 5px;
            position: relative;
        }
        
        .calendar-day.other-month {
            background-color: #f8f9fa;
            color: #6c757d;
        }
        
        .calendar-day-number {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .calendar-project {
            background-color: #007bff;
            color: white;
            padding: 2px 5px;
            margin-bottom: 2px;
            border-radius: 3px;
            font-size: 0.8em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .calendar-project.high {
            background-color: #dc3545;
        }
        
        .calendar-project.medium {
            background-color: #ffc107;
            color: #212529;
        }
        
        .calendar-project.completed {
            background-color: #28a745;
            text-decoration: line-through;
        }
        
        .calendar-project.on_hold {
            background-color: #6c757d;
        }
        
        .today {
            background-color: #e6f7ff !important;
            border: 1px solid #007bff;
        }
        
        .start-date::before {
            content: "▶ ";
        }
        
        .end-date::after {
            content: " ◀";
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
                    <h1 class="h2">Projektkalender</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="index.php" class="btn btn-sm btn-outline-secondary">Lista</a>
                            <a href="add.php" class="btn btn-sm btn-outline-secondary">Lägg till projekt</a>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12 text-center">
                        <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="btn btn-outline-primary">&laquo; Föregående</a>
                        <h3 class="d-inline mx-3"><?php echo strftime('%B %Y', $timestamp); ?></h3>
                        <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="btn btn-outline-primary">Nästa &raquo;</a>
                    </div>
                </div>

                <div class="calendar">
                    <div class="calendar-header">Söndag</div>
                    <div class="calendar-header">Måndag</div>
                    <div class="calendar-header">Tisdag</div>
                    <div class="calendar-header">Onsdag</div>
                    <div class="calendar-header">Torsdag</div>
                    <div class="calendar-header">Fredag</div>
                    <div class="calendar-header">Lördag</div>
                    
                    <?php
                    // Display empty cells for days before the first day of the month
                    for ($i = 0; $i < $firstDayOfWeek; $i++) {
                        echo '<div class="calendar-day other-month"></div>';
                    }
                    
                    // Display days of the month
                    $today = date('Y-m-d');
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $date = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
                        $isToday = ($date == $today) ? 'today' : '';
                        $dayProjects = isset($projectsByDate[$date]) ? $projectsByDate[$date] : [];
                        
                        echo '<div class="calendar-day ' . $isToday . '">';
                        echo '<div class="calendar-day-number">' . $day . '</div>';
                        
                        // Display up to 3 projects per day
                        $projectCount = 0;
                        foreach ($dayProjects as $project) {
                            if ($projectCount >= 3) {
                                echo '<div class="calendar-project">+' . (count($dayProjects) - 3) . ' fler</div>';
                                break;
                            }
                            
                            $projectClass = '';
                            if ($project['status'] == 'completed') {
                                $projectClass = 'completed';
                            } elseif ($project['status'] == 'on_hold') {
                                $projectClass = 'on_hold';
                            } elseif ($project['priority'] == 'high') {
                                $projectClass = 'high';
                            } elseif ($project['priority'] == 'medium') {
                                $projectClass = 'medium';
                            }
                            
                            // Check if this is a start or end date
                            $dateType = '';
                            if ($project['start_date'] == $date) {
                                $dateType = 'start-date';
                            } elseif ($project['end_date'] == $date) {
                                $dateType = 'end-date';
                            }
                            
                            echo '<div class="calendar-project ' . $projectClass . ' ' . $dateType . '" title="' . htmlspecialchars($project['name']) . '">';
                            echo '<a href="view.php?id=' . $project['id'] . '" class="text-white">';
                            echo htmlspecialchars(substr($project['name'], 0, 15)) . (strlen($project['name']) > 15 ? '...' : '');
                            echo '</a>';
                            echo '</div>';
                            
                            $projectCount++;
                        }
                        
                        echo '</div>';
                    }
                    
                    // Display empty cells for days after the last day of the month
                    $totalCells = $firstDayOfWeek + $daysInMonth;
                    $remainingCells = 42 - $totalCells; // 6 rows * 7 days
                    for ($i = 0; $i < $remainingCells; $i++) {
                        echo '<div class="calendar-day other-month"></div>';
                    }
                    ?>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Förklaring</h5>
                        <div class="d-flex">
                            <div class="mr-3"><span class="badge" style="background-color: #007bff;">&nbsp;</span> Låg prioritet</div>
                            <div class="mr-3"><span class="badge" style="background-color: #ffc107;">&nbsp;</span> Medium prioritet</div>
                            <div class="mr-3"><span class="badge" style="background-color: #dc3545;">&nbsp;</span> Hög prioritet</div>
                            <div class="mr-3"><span class="badge" style="background-color: #28a745;">&nbsp;</span> Slutförd</div>
                            <div class="mr-3"><span class="badge" style="background-color: #6c757d;">&nbsp;</span> Pausad</div>
                        </div>
                        <div class="d-flex mt-2">
                            <div class="mr-3"><span class="badge badge-secondary">▶ Projektnamn</span> Startdatum</div>
                            <div class="mr-3"><span class="badge badge-secondary">Projektnamn ◀</span> Slutdatum</div>
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