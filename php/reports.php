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
                            <label for="course_id_class" class="form-label">Select Course</label>
                            <select class="form-select" id="course_id_class" required>
                                <option value="">Select Course First...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="class_id_report" class="form-label">Select Class</label>
                            <select class="form-select" id="class_id_report" name="class_id" required disabled>
                                <option value="">Select course first...</option>
                            </select>
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
                            <label for="student_id_report" class="form-label">Student</label>
                            <select class="form-select" id="student_id_report" name="student_id" required>
                                <option value="">Select Student...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="course_id_filter" class="form-label">Course (Optional)</label>
                            <select class="form-select" id="course_id_filter" name="course_id">
                                <option value="">All Courses</option>
                            </select>
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
                            <label for="course_id_summary" class="form-label">Course</label>
                            <select class="form-select" id="course_id_summary" name="course_id" required>
                                <option value="">Select Course...</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info w-100 text-white">Generate PDF</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const serverRoot = '<?php echo SERVER_ROOT; ?>';

    // Load all courses for all dropdowns
    function loadCourses() {
        fetch(serverRoot + '/php/api/report_data.php?action=get_courses')
            .then(response => response.json())
            .then(data => {
                const courseSelects = ['course_id_class', 'course_id_filter', 'course_id_summary'];
                courseSelects.forEach(selectId => {
                    const select = document.getElementById(selectId);
                    data.forEach(course => {
                        const option = new Option(course.text, course.id);
                        select.add(option);
                    });
                });
            })
            .catch(error => console.error('Error loading courses:', error));
    }

    // Load all students
    function loadStudents() {
        fetch(serverRoot + '/php/api/report_data.php?action=get_students')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('student_id_report');
                data.forEach(student => {
                    const option = new Option(student.text, student.id);
                    select.add(option);
                });
            })
            .catch(error => console.error('Error loading students:', error));
    }

    // Load classes when course is selected (for class attendance report)
    document.getElementById('course_id_class').addEventListener('change', function() {
        const courseId = this.value;
        const classSelect = document.getElementById('class_id_report');

        // Clear and disable class select
        classSelect.innerHTML = '<option value="">Select Class...</option>';
        classSelect.disabled = !courseId;

        if (courseId) {
            fetch(serverRoot + '/php/api/report_data.php?action=get_classes&course_id=' + courseId)
                .then(response => response.json())
                .then(data => {
                    data.forEach(classItem => {
                        const option = new Option(classItem.text, classItem.id);
                        classSelect.add(option);
                    });
                })
                .catch(error => console.error('Error loading classes:', error));
        }
    });

    // Initialize
    loadCourses();
    loadStudents();
});
</script>

<?php
include_once ROOT_PATH . '/php/include/footer.php';
?>