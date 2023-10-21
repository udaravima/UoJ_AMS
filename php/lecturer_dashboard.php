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


<?php
include ROOT_PATH . '/php/include/footer.php';
?>