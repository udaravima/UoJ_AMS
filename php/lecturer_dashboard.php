<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/MyAttendanceSys/config.php';
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

if (!($user->isLoggedIn()) || $_SESSION['user_role'] > 1) {
    header("Location: " . SERVER_ROOT . "/index.php");
    exit();
}

?>
<?php
include_once ROOT_PATH . '/php/include/header.php';
echo "<title>AMS Lecturer</title>";
include_once ROOT_PATH . '/php/include/content.php';
$activeDash = 1;
include_once ROOT_PATH . '/php/include/nav.php';
// include ROOT_PATH.'/php/include/sidebar-lecturer.php'
include_once ROOT_PATH . '/php/include/modal_form.php';
?>

<div class="container-md mt-5 p-3">
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-success my-3 " data-bs-toggle="modal" data-bs-target="#add_class">+ Add
            Class</button>
    </div>
</div>

<div class="container-sm mt-3" id="course_data">
    <?php
    $order = array();
    $itemsPerPage = 10; //10 items per page
    $currentPage = isset($_GET['pageC']) ? $_GET['pageC'] : 1;
    $order['offset'] = ($currentPage - 1) * $itemsPerPage;
    $order['limit'] = $itemsPerPage;
    $totalCount = $user->countRecords('uoj_lecturer_course', 'lecr_id', $_SESSION["lecr_id"]);
    $totalPages = ceil($totalCount / $itemsPerPage);
    ?>

    <table class="table table-striped table-hover border shadow " id="courses_data">
        <h1>Assigned Courses</h1>
        <thead>
            <tr>
                <th>#</th>
                <th>Course Code</th>
                <th>Course Name</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $courses = $lecr->getLecturerCourseList($_SESSION["lecr_id"], $order);
            $i = 1;
            while ($course = $courses->fetch_assoc()) {
                echo "<tr data-bs-toggle='modal' data-bs-target='#add_course' data-course-id='" . $course['course_id'] . "'>";
                echo "<td>" . $i++ . "</td>";
                echo "<td>" . $course['course_code'] . "</td>";
                echo "<td>" . $course['course_name'] . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                    <a class="page-link"
                        href="<?php echo SERVER_ROOT; ?>/php/lecturer_dashboard.php?pageC=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
<div class="container">
    <button data-bs-target="#add_class" data-bs-toggle="modal" data-class-id="3" name="test-button"
        class="btn btn-primary d-none" id="class-trigger">class</button>
</div>
<div class="container mt-3">
    <div id="class-calandar-lecturer">

    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('class-calandar-lecturer');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            navLinks: true,
            selectable: true,
            editable: true,
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
                    sendMessage('Error loading calendar events!', 'warning');
                },
                color: 'cyan', // a non-ajax option
                textColor: 'black' // a non-ajax option
            },
            eventClick: function (info) {
                var class_id = info.event.id;
                // var trig = document.getElementById('class-trigger');
                // trig.setAttribute('data-class-id', class_id);
                // trig.click();
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

            },
            select(info) {
                var start = info.startStr;
                $('#add_class').data('start', start);
                $('#add_class').modal('show');
            },
        });
        calendar.render();
    });
</script>

<?php
include ROOT_PATH . '/php/include/footer.php';
?>
<!-- 
    TODO:
        class consider (lecr_id, course_id, class_date, start_time, end_time) for duplicates
        fix calendar
        class attendance
        fix student cards and caleder setup

        updates
 -->