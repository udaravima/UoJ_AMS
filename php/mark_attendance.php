<?php
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';
include_once ROOT_PATH . '/php/class/Utils.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$lecr = new Lecturer($conn);
$utils = new Utils();


if (!($user->isLoggedIn()) || $_SESSION['user_role'] > 2) {
    header("Location: " . SERVER_ROOT . "/index.php");
}

include_once ROOT_PATH . '/php/include/header.php';
echo "<title>AMS Lecturer</title>";
include_once ROOT_PATH . '/php/include/content.php';
$activeDash = 1;
include_once ROOT_PATH . '/php/include/nav.php';
// include_once ROOT_PATH . '/php/include/modal_form.php';

// if (isset($_POST["class-id"])) {
//     $classId = $_POST["class-id"];
//     $class = $lecr->retrieveClassDetails($classId)->fetch_assoc();
?>

<div class="container-md mt-5 p-3">
    <div class="input-group mb-3">
        <input type="search" class="form-control" placeholder="Search User" aria-label="Search Course" aria-describedby="course-addon" id="search-user-for-course" data-bs-toggle="dropdown" data-bs-target="#course-assigned-user" name="search-user-for-course">
        <div class="input-group-append">
            <button class="btn btn-secondary rounded btn-group" type="button" id="user-selectAll" onclick="selectAllUsers(this, 'student-list[]')">* select</button>
            <button class="btn btn-success rounded btn-group" type="button" id="addStudents" onclick="addToTable(this)">Add</button>
        </div>
        <div class="dropdown-menu" aria-labelledby="users-addon-course" style="overflow:hidden auto; max-height:245px;" id="course-assigned-user">
            <!-- users goes here -->
            <!-- wait -->
        </div>
    </div>
</div>
<div class="container-md mt-5 p-3">
    <table class="table table-hover border shadow">
        <h3 id="class-title"></h3>
        <thead>
            <tr>
                <th>#</th>
                <th>Reg No</th>
                <th>Name</th>
                <th>Attendance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="class-attendance">
            <!-- attendance goes here -->
            <!-- wait -->
        </tbody>
    </table>
</div>
<script>
    <?php
    if (isset($_POST['class-id'])) {
        echo "let classId = " . $_POST['class-id'] . ";";
        $class = $lecr->retrieveClassDetails($_POST['class-id'])->fetch_assoc();
        echo "let courseId = " . $class['course_id'] . ";";
        echo "let courseCode = '" . $class['course_code'] . "';";
        echo "let courseName = '" . $class['course_name'] . "';";
    } else {
        echo "let classId = -1;";
        echo "let courseId = -1;";
        // exit();
    }
    ?>
    console.log(classId);

    document.addEventListener('DOMContentLoaded', function() {
        if (classId != -1) {
            document.getElementById('class-title').innerHTML = courseCode + " - " + courseName;
            // $.ajax({
            //     method: 'POST',
            //     url: '<?php //echo SERVER_ROOT; 
                            ?>/php/validation.php',
            //     data: {
            //         classId: classId
            //     },
            //     dataType: 'json',
            //     success: function(response) {
            //         let classAttendance = $('#class-attendance');
            //         classAttendance.empty();
            //         let i = 1;
            //         (response.attendance).forEach(attendance => {
            //             let data = $(document.createElement("tr"));
            //             data.append($(document.createElement("td")).text(i++));
            //             data.append($(document.createElement("td")).text(attendance[0]));
            //             data.append($(document.createElement("td")).text(attendance[1]));
            //             data.append($(document.createElement("td")).text(attendance[2]));
            //             classAttendance.append(data);
            //         });
            //     },
            //     error: function() {
            //         sendMessage('Error on loading attendance', 'danger');
            //     }
            // })
        } else {
            // sendMessage('No class selected', 'warning');
            window.location.href = '<?php echo SERVER_ROOT; ?>/php/lecturer_dashboard.php';
            // document.getElementById('class-title').innerHTML = "No class selected";
        }
    });

    document.getElementById('search-user-for-course').addEventListener('keyup', function(event) {
        let userSearch = document.getElementById('search-user-for-course').value;
        if (userSearch.length > 3) {
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/mark_attendance_action.php',
                data: {
                    userSearch: userSearch,
                    courseId: courseId
                },
                dataType: 'json',
                success: function(response) {
                    let userListCourse = $('#course-assigned-user');
                    userListCourse.empty();
                    userListCourse.append($(document.createElement("div")).addClass("dropdown-header").text("Student"));
                    (response.students).forEach(student => {
                        let data = $(document.createElement("div")).addClass("form-check").addClass("dropdown-item");
                        data.append($(document.createElement("input")).addClass("form-check-input").attr("value", student[0]).attr("type", "checkbox").attr("id", "filterStdCheckbox" + student[0]).attr("name", "student-list[]").attr("data-user-role", 3).attr("data-user-name", student[2]).attr("data-user-index", student[1]).attr("data-user-reg", student[18]).attr("onchange", "addUserToCourse(this)"));
                        data.append($(document.createElement("label")).addClass("form-check-label").attr("for", "filterStdCheckbox" + student[0]).text(student[1] + " - " + student[18]));
                        userListCourse.append(data);
                    });
                    userListCourse.append($(document.createElement("div")).addClass("dropdown-divider"));
                    $('#course-assigned-user').selectpicker('refresh');

                },
                error: function() {
                    sendMessage('Error on loading users', 'danger');
                }
            })
        }
    });

    function addUserToCourse(user) {
        let userId = user.value;
        let userName = user.getAttribute('data-user-name');
        let userIndex = user.getAttribute('data-user-index');
        let userReg = user.getAttribute('data-user-reg');
        let userRole = user.getAttribute('data-user-role');
        let userRow = $(document.createElement("tr")).attr("id", "user-" + userId);
        userRow.append($(document.createElement("td")).text(userIndex));
        userRow.append($(document.createElement("td")).text(userReg));
        userRow.append($(document.createElement("td")).text(userName));
        userRow.append($(document.createElement("td")).text("0"));
        userRow.append($(document.createElement("td")).append($(document.createElement("button")).addClass("btn btn-danger").attr("onclick", "removeUserFromCourse(this)").attr("data-user-id", userId).attr("data-user-role", userRole).attr("data-user-name", userName).attr("data-user-index", userIndex).attr("data-user-reg", userReg).text("Remove")));
        $('#class-attendance').append(userRow);
    
    }
</script>


<?php
include ROOT_PATH . '/php/include/footer.php';
?>