<?php
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';
include_once ROOT_PATH . '/php/class/Utils.php';

$utils = new Utils();
$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$lecr = new Lecturer($conn);

// Check if user is logged in and is an instructor (role 2)
if (!($user->isLoggedIn()) || $_SESSION['user_role'] != 2) {
    header("Location: " . SERVER_ROOT . "/index.php");
    exit();
}

?>
<?php
include_once ROOT_PATH . '/php/include/header.php';
echo "<title>AMS Instructor</title>";
include_once ROOT_PATH . '/php/include/content.php';
$activeDash = 1;
include_once ROOT_PATH . '/php/include/nav.php';
include_once ROOT_PATH . '/php/include/modal_form.php';
?>

<div class="container-md mt-5 p-3">
    <!-- Instructor Specific Actions if any -->
    <h2 class="mb-4">Instructor Dashboard</h2>
</div>

<div class="container-sm mt-3" id="class_data">
    <?php
    $order = array();
    $itemsPerPage = 10;
    $currentPage = isset($_GET['pageC']) ? $_GET['pageC'] : 1;
    $order['offset'] = ($currentPage - 1) * $itemsPerPage;
    $order['limit'] = $itemsPerPage;

    // Using a custom method to count assigned classes might be needed if pagination is strictly required, 
    // but for now, we'll list all or paginate the result of getClassesForInstructor if we implement a counter.
    // simpler approach: fetch all and paginate in PHP or just show recent ones.
    // For now, let's just list the classes from getClassesForInstructor without complex pagination count 
    // or implement a count method later if needed.
    ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Assigned Classes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Course</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch classes for the instructor
                        // We can pass empty start/end dates to get all, or current month? 
                        // Let's get all for now, or recent/upcoming.
                        // Ideally checking for "upcoming" or "recent" is better.
                        // Let's just use the method we added.
                        $classes = $lecr->getClassesForInstructor($_SESSION["lecr_id"]);

                        if ($classes && $classes->num_rows > 0) {
                            $i = 1;
                            while ($class = $classes->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $i++ . "</td>";
                                echo "<td>" . $class['class_date'] . "</td>";
                                echo "<td>" . substr($class['start_time'], 0, 5) . " - " . substr($class['end_time'], 0, 5) . "</td>";
                                echo "<td>" . $class['course_code'] . " - " . $class['course_name'] . "</td>";
                                echo "<td>
                                        <form action='" . SERVER_ROOT . "/php/mark_attendance.php' method='POST'>
                                            <input type='hidden' name='class-id' value='" . $class['class_id'] . "'>
                                            <button type='submit' class='btn btn-sm btn-primary'>Mark Attendance</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No classes assigned.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="container mt-3">
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Schedule</h6>
        </div>
        <div class="card-body">
            <div id="class-calendar-instructor"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('class-calendar-instructor');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            navLinks: true,
            selectable: false, // Instructors can't create classes via calendar select
            editable: false,   // Instructors can't drag/drop
            dayMaxEvents: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: {
                url: '<?php echo SERVER_ROOT; ?>/php/calendar_setup.php',
                method: 'POST',
                extraParams: {
                    lecr_id: <?php echo $_SESSION["lecr_id"]; ?>,
                },
                failure: function () {
                    // console.log('Error loading calendar events!');
                },
                color: 'cyan',
                textColor: 'black'
            },
            eventClick: function (info) {
                var class_id = info.event.id;
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo SERVER_ROOT; ?>/php/mark_attendance.php';
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'class-id';
                input.value = class_id;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
        calendar.render();
    });
</script>

<?php
include ROOT_PATH . '/php/include/footer.php';
?>