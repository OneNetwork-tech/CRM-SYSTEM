<?php
// Kalendervyn

session_start();
require_once '../../config/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../classes/Calendar.php';

// Kontrollera om användaren är inloggad
require_login();

// Hämta aktuell användare
$current_user = get_current_user_data();

// Lägg till svenska kalendertyper
$event_types = [
    'event' => 'Händelse',
    'meeting' => 'Möte',
    'task' => 'Uppgift',
    'deadline' => 'Deadline'
];

// Get filter parameters
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Create timestamp for first day of month
$timestamp = mktime(0, 0, 0, $month, 1, $year);

// Get number of days in month
$daysInMonth = date('t', $timestamp);

// Get day of week for first day (0 = Sunday, 6 = Saturday)
$firstDayOfWeek = date('w', $timestamp);

// Get calendar events for this month
$calObj = new Calendar();
$start_date = date('Y-m-01', $timestamp);
$end_date = date('Y-m-t', $timestamp);
$events = $calObj->getEventsByDateRange($start_date, $end_date, $current_user['id']);

// Group events by date for easier display
$eventsByDate = [];
foreach ($events as $event) {
    $eventDate = date('Y-m-d', strtotime($event['start_datetime']));
    if (!isset($eventsByDate[$eventDate])) {
        $eventsByDate[$eventDate] = [];
    }
    $eventsByDate[$eventDate][] = $event;
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender - <?php echo APP_NAME; ?></title>
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
        
        .calendar-event {
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
        
        .calendar-event.meeting {
            background-color: #28a745;
        }
        
        .calendar-event.task {
            background-color: #ffc107;
            color: #212529;
        }
        
        .calendar-event.deadline {
            background-color: #dc3545;
        }
        
        .today {
            background-color: #e6f7ff !important;
            border: 1px solid #007bff;
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
                    <h1 class="h2">Kalender</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <a href="add.php" class="btn btn-sm btn-outline-secondary">Lägg till händelse</a>
                            <a href="list.php" class="btn btn-sm btn-outline-secondary">Lista</a>
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
                        $dayEvents = isset($eventsByDate[$date]) ? $eventsByDate[$date] : [];
                        
                        echo '<div class="calendar-day ' . $isToday . '">';
                        echo '<div class="calendar-day-number">' . $day . '</div>';
                        
                        // Display up to 3 events per day
                        $eventCount = 0;
                        foreach ($dayEvents as $event) {
                            if ($eventCount >= 3) {
                                echo '<div class="calendar-event">+' . (count($dayEvents) - 3) . ' fler</div>';
                                break;
                            }
                            
                            $eventTypeClass = '';
                            if ($event['type'] == 'meeting') {
                                $eventTypeClass = 'meeting';
                            } elseif ($event['type'] == 'task') {
                                $eventTypeClass = 'task';
                            } elseif ($event['type'] == 'deadline') {
                                $eventTypeClass = 'deadline';
                            }
                            
                            echo '<div class="calendar-event ' . $eventTypeClass . '" title="' . htmlspecialchars($event['title']) . '">';
                            echo '<a href="view.php?id=' . $event['id'] . '" class="text-white">';
                            echo htmlspecialchars(substr($event['title'], 0, 15)) . (strlen($event['title']) > 15 ? '...' : '');
                            echo '</a>';
                            echo '</div>';
                            
                            $eventCount++;
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
                            <div class="mr-3"><span class="badge" style="background-color: #007bff;">&nbsp;</span> Händelse</div>
                            <div class="mr-3"><span class="badge" style="background-color: #28a745;">&nbsp;</span> Möte</div>
                            <div class="mr-3"><span class="badge" style="background-color: #ffc107;">&nbsp;</span> Uppgift</div>
                            <div class="mr-3"><span class="badge" style="background-color: #dc3545;">&nbsp;</span> Deadline</div>
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