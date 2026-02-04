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

if (!($user->isLoggedIn()) || !($user->isAdmin() || $user->isLecturer() || $user->isInstructor())) {
    header("Location: " . SERVER_ROOT . "/index.php");
    exit();
}

include_once ROOT_PATH . '/php/include/header.php';
echo "<title>Reports & Analytics</title>";
include_once ROOT_PATH . '/php/include/content.php';
$activeDash = 4; // Reports navigation index
include_once ROOT_PATH . '/php/include/nav.php';
include_once ROOT_PATH . '/php/include/modal_form.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Reports & Analytics</h2>

    <div class="row">
        <!-- Class Attendance Report -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Class Attendance</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Generate a detailed attendance report for a specific class session.</p>
                    <form action="generate_report.php" method="post" target="_blank">
                        <input type="hidden" name="report_type" value="class_attendance">
                        <div class="mb-3">
                            <label for="class_id_report" class="form-label">Select Class ID</label>
                            <input type="number" class="form-control" id="class_id_report" name="class_id" required
                                placeholder="Enter Class ID">
                            <!-- Ideally this should be a dropdown search -->
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Generate PDF</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Student Attendance Report -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Student Report</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">View attendance history for a specific student.</p>
                    <form action="generate_report.php" method="post" target="_blank">
                        <input type="hidden" name="report_type" value="student_attendance">
                        <div class="mb-3">
                            <label for="student_id_report" class="form-label">Student ID</label>
                            <input type="number" class="form-control" id="student_id_report" name="student_id" required
                                placeholder="Enter Student ID">
                        </div>
                        <div class="mb-3">
                            <label for="course_id_filter" class="form-label">Course ID (Optional)</label>
                            <input type="number" class="form-control" id="course_id_filter" name="course_id"
                                placeholder="Filter by Course">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Generate PDF</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Course Summary Report -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Course Summary</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Overview of attendance statistics for a course.</p>
                    <form action="generate_report.php" method="post" target="_blank">
                        <input type="hidden" name="report_type" value="course_summary">
                        <div class="mb-3">
                            <label for="course_id_summary" class="form-label">Course ID</label>
                            <input type="number" class="form-control" id="course_id_summary" name="course_id" required
                                placeholder="Enter Course ID">
                        </div>
                        <button type="submit" class="btn btn-info w-100 text-white">Generate PDF</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once ROOT_PATH . '/php/include/footer.php';
?>