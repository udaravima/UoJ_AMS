<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/MyAttendanceSys/config.php';
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
    <div class="btn-group">
        <button type="button" class="btn btn-success my-3 align-content-end justify-content-end ms-auto"
            data-bs-toggle="modal" data-bs-target="#reg_user">+ Add User</button>
    </div>
</div>

<!-- Lecture Table  -->
<div class="container-sm mt-5" id="lecr_data">
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
                <th>Name</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Photo</th>
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
                echo "<tr>";
                echo "<td>" . $i++ . "</td>";
                echo "<td>" . $lecturer['lecr_name'] . "</td>";
                echo "<td>" . $lecturer['lecr_mobile'] . "</td>";
                echo "<td>" . $lecturer['lecr_email'] . "</td>";
                echo "<td>" . $role . "</td>";
                echo "<td>" . $status . "</td>";
                echo "<td><img class='rounded-circle' src='" . ROOT_PATH . $photo . "' width='50px' height='50px'></td>";
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
                        href="<?php echo SERVER_ROOT; ?>/php/admin_dashboard.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Student Table -->
<div class="container-sm mt-5" id="std_data">
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
                echo "<tr>";
                echo "<td>" . $i++ . "</td>";
                echo "<td>" . $student['std_fullname'] . "</td>";
                echo "<td>" . $student['mobile_tp_no'] . "</td>";
                echo "<td>" . $student['std_email'] . "</td>";
                echo "<td>" . $status . "</td>";
                echo "<td><img class='rounded-circle' src='" . ROOT_PATH . $photo . "' width='50px' height='50px'></td>";
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
                        href="<?php echo SERVER_ROOT; ?>/php/admin_dashboard.php?pageS=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Get Courses -->


<?php
include_once ROOT_PATH . '/php/include/modal_form.php';
include_once ROOT_PATH . '/php/include/footer.php';
?>