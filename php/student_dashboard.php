<?php
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Utils.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';
$utils = new Utils();
$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$lecr = new Lecturer($conn);


if (!($user->isLoggedIn()) || $_SESSION['user_role'] != 3) {
    header("Location: " . SERVER_ROOT . "/index.php");
    exit();
}
include ROOT_PATH . '/php/include/header.php';
echo "<title>AMS Student</title>";
include ROOT_PATH . '/php/include/content.php';
$activeDash = 3;
include ROOT_PATH . '/php/include/nav.php';
?>
<div class="container mt-3">
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center">Student Dashboard</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h4 class="text-center">Welcome <?php echo $_SESSION['user_name']; ?></h4>
        </div>
    </div>
</div>
<!-- Performance -->
<div class="container mt-3">
    <?php
    $totalPrecentage = $lecr->retrieveAttendancePresentageForStudent($_SESSION["std_id"]);
    ?>
    <div class="row">
        <div class="col-md-12">
            <h4 class="text-center <?php $tempText = "";
            if ($totalPrecentage[0] + $totalPrecentage[1] > 80) {
                echo "text-success";
            } else if ($totalPrecentage[0] + $totalPrecentage[1] > 50) {
                echo "text-warning";
            } else {
                echo "text-danger";
            }
            ?> ">Your Attendance Precentage is
                <?php echo $totalPrecentage[0] + $totalPrecentage[1] . "% ";
                echo ($totalPrecentage[0] + $totalPrecentage[1] > 80) ? " Keep it up!" : (($totalPrecentage[0] + $totalPrecentage[1] > 80) ? "Work around fast!" : "Oops..!"); ?>
            </h4></br>
            <div class="progress" style="height: 30px;">
                <div class="progress-bar bg-success <?php echo " w-" . $totalPrecentage[0]; ?> p-3">
                    <?php echo $totalPrecentage[0] + $totalPrecentage[1] . "%"; ?>
                </div>
                <div class="progress-bar bg-warning <?php echo " w-" . $totalPrecentage[1]; ?> p-3">
                    <?php echo $totalPrecentage[1] . "%"; ?>
                </div>
                <!-- <div class="progress-bar bg-danger <?php echo " w-" . 100 - $totalPrecentage[1] - $totalPrecentage[1]; ?> p-3">
                    <?php //echo 100 - $totalPrecentage[1] - $totalPrecentage[0] . "%"; 
                    ?>
                </div> -->
            </div>
        </div>

        <!-- courses -->
        <!-- TODO: add attendance precentile -->
        <div class="container-sm mt-3" id="course_data">
            <?php
            $order = array();
            $itemsPerPage = 10; //10 items per page
            $currentPage = isset($_GET['pageC']) ? $_GET['pageC'] : 1;
            $order['offset'] = ($currentPage - 1) * $itemsPerPage;
            $order['limit'] = $itemsPerPage;
            $totalCount = $user->countRecords('uoj_Student_course', 'std_id', $_SESSION["std_id"]);
            $totalPages = ceil($totalCount / $itemsPerPage);
            ?>

            <table class="table table-striped table-hover border shadow " id="courses_data">
                <h1>Assigned Courses</h1>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Attendance Precentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $courses = $lecr->getStudentCourseList($_SESSION["std_id"], $order);
                    $i = 1;
                    while ($course = $courses->fetch_assoc()) {
                        $precentage = $lecr->attendancePrecentageForCourse($_SESSION["std_id"], $course['course_id']);
                        echo "<tr data-bs-toggle='modal' data-bs-target='#add_course' data-course-id='" . $course['course_id'] . "'>";
                        echo "<td>" . $i++ . "</td>";
                        echo "<td>" . $course['course_code'] . "</td>";
                        echo "<td>" . $course['course_name'] . "</td>";
                        // echo "<td>" . $precentage[0] . " " . $precentage[1] . "</td>";
                        echo "<td><div class='progressbar' data-pss='rad' style='--pss-value:" . $precentage[0] + $precentage[1] . "'>" . $precentage[0] + $precentage[1] . "%</div></td>";
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
                                href="<?php echo SERVER_ROOT; ?>/php/student_dashboard.php?pageC=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>

        <div class="container mt-3">
            <div id="class-calandar-student">

            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var calendarEl = document.getElementById('class-calandar-student');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: {
                        url: '<?php echo SERVER_ROOT . "/php/calendar_setup.php"; ?>',
                        method: 'POST',
                        extraParams: {
                            std_id: <?php echo $_SESSION["std_id"]; ?>
                        },
                        failure: function () {
                            sendMessage('there was an error while fetching events!', 'warning');
                        },
                        color: 'yellow', // a non-ajax option
                        textColor: 'black' // a non-ajax option
                    }
                });
                calendar.render();
            });
        </script>
        <?php
        include ROOT_PATH . '/php/include/footer.php';
        ?>