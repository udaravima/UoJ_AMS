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

if (!($user->isLoggedIn()) || !($user->isAdmin())) {
    header("Location: " . SERVER_ROOT . "/index.php");
}
?>

<?php
include_once ROOT_PATH . '/php/include/header.php';
echo "<title>AMS Admin</title>";
include_once ROOT_PATH . '/php/include/content.php';
$activeDash = 0;
include_once ROOT_PATH . '/php/include/nav.php';
?>

<div class="container-md mt-5 p-3">
    <div class="btn-group col-1">
        <button type="button" class="btn btn-success my-3" data-bs-toggle="modal" data-bs-target="#reg_user">+ Add
            User</button>
    </div>
    <div class="dropdown">
        <form action="" class="input-group">
            
        </form>
    </div>

</div>

<!-- Lecture Table  -->
<div class="container-sm mt-3" id="lecr_data">
    <h1>Lecturer Records</h1>
    <?php
    $order = array();
    $itemsPerPage = 10;
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $order['offset'] = ($currentPage - 1) * $itemsPerPage;
    $order['limit'] = $itemsPerPage;
    $totalCount = $user->countRecords('uoj_lecturer');
    $totalPages = ceil($totalCount / $itemsPerPage);
    ?>
    <table class="table table-striped table-hover border shadow table-responsive-md" id="lecture_data">
        <thead>
            <tr>
                <th>#</th>
                <th>Reg No</th>
                <th>Name</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>photo</th>
                <th class="visually-hidden">userID</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $lecturers = $user->getLecturerTable($order);
            $i = 1;
            while ($lecturer = $lecturers->fetch_assoc()) {
                $status = $user->getStatusStr(intval($lecturer['user_status']));
                $role = $user->getRoleStr(intval($lecturer['user_role']));
                $photo = (($lecturer['lecr_profile_pic'] == null) ? $user->getDefaultProfilePic() : $lecturer['lecr_profile_pic']);
                echo "<tr data-bs-toggle='modal' data-bs-target='#fetch-user-details' data-user-id='" . $lecturer['user_id'] . "'>";
                // echo "<tr>";
                echo "<td>" . $i++ . "</td>";
                echo "<td>" . $lecturer['username'] . "</td>";
                // echo "<td>" . $lecturer['lecr_name'] . "</td>";
                echo "<td>" . $utils->processNameInitials($lecturer['lecr_name']) . "</td>";
                echo "<td>" . $lecturer['lecr_mobile'] . "</td>";
                echo "<td>" . $lecturer['lecr_email'] . "</td>";
                echo "<td>" . $role . "</td>";
                echo "<td>" . $status . "</td>";
                echo "<td><img class='rounded-circle' src='" . $photo . "' width='50px' height='50px'></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo SERVER_ROOT; ?>/php/admin_dashboard.php?page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Student Table -->
<div class="container-sm mt-3" id="std_data">
    <?php
    $order = array();
    $itemsPerPage = 10;
    $currentPage = isset($_GET['pageS']) ? $_GET['pageS'] : 1;
    $order['offset'] = ($currentPage - 1) * $itemsPerPage;
    $order['limit'] = $itemsPerPage;
    $totalCount = $user->countRecords('uoj_student');
    $totalPages = ceil($totalCount / $itemsPerPage);
    ?>
    <table class="table table-striped table-hover border shadow" id="student_data">
        <h1>Student Records</h1>
        <thead>
            <tr>
                <th>#</th>
                <th>Reg No</th>
                <th>Name</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Status</th>
                <th>Photo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $students = $user->getStudentTable($order);
            $i = 1;
            while ($student = $students->fetch_assoc()) {
                $status = $user->getStatusStr(intval($student['user_status']));
                $photo = (($student['std_profile_pic'] == null) ? $user->getDefaultProfilePic() : $student['std_profile_pic']);
                echo "<tr data-bs-toggle='modal' data-bs-target='#fetch-user-details' data-user-id='" . $student['user_id'] . "'>";
                // echo "<tr>";
                echo "<td>" . $i++ . "</td>";
                echo "<td>" . $student['username'] . "</td>";
                // echo "<td>" . $student['std_fullname'] . "</td>";
                echo "<td>" . $utils->processNameInitials($student['std_fullname']) . "</td>";
                echo "<td>" . $student['mobile_tp_no'] . "</td>";
                echo "<td>" . $student['std_email'] . "</td>";
                echo "<td>" . $status . "</td>";
                echo "<td><img class='rounded-circle' src='" . $photo . "' width='50px' height='50px'></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo SERVER_ROOT; ?>/php/admin_dashboard.php?pageS=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<div class="container-md mt-3 p-3">
    <div class="btn-group">
        <button type="button" class="btn btn-success my-3" data-bs-toggle="modal" data-bs-target="#add_course">+ Add
            Course</button>
    </div>
</div>

<!-- Get Courses -->
<div class="container-sm mt-3" id="course_data">
    <?php
    $order = array();
    $itemsPerPage = 10; //10 items per page
    $currentPage = isset($_GET['pageC']) ? $_GET['pageC'] : 1;
    $order['offset'] = ($currentPage - 1) * $itemsPerPage;
    $order['limit'] = $itemsPerPage;
    $totalCount = $user->countRecords('uoj_course');
    $totalPages = ceil($totalCount / $itemsPerPage);
    ?>

    <table class="table table-striped table-hover border shadow " id="courses_data">
        <h1>Courses</h1>
        <thead>
            <tr>
                <th>#</th>
                <th>Course Code</th>
                <th>Course Name</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $courses = $lecr->getCourseList($order);
            $i = 1;
            while ($course = $courses->fetch_assoc()) {
                echo "<tr data-bs-toggle='modal' data-bs-target='#course-info-card' data-course-id='" . $course['course_id'] . "'>";
                // echo "<tr data-bs-toggle='modal' data-bs-target='#add_course' data-course-id='" . $course['course_id'] . "'>";
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
                        href="<?php echo SERVER_ROOT; ?>/php/admin_dashboard.php?pageC=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- User Info modal with detail card -->

<?php
include_once ROOT_PATH . '/php/include/dspCard.php';
include_once ROOT_PATH . '/php/include/modal_form.php';
include_once ROOT_PATH . '/php/include/footer.php';
?>