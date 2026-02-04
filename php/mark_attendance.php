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


if (!($user->isLoggedIn()) || $_SESSION['user_role'] > 2 || !isset($_POST['class-id'])) {
    header("Location: " . SERVER_ROOT . "/index.php");
    exit();
}

include_once ROOT_PATH . '/php/include/header.php';
echo "<title>AMS Lecturer</title>";
include_once ROOT_PATH . '/php/include/content.php';
$activeDash = 1;
include_once ROOT_PATH . '/php/include/nav.php';
include_once ROOT_PATH . '/php/include/modal_form.php';

$classId = intval($_POST['class-id']);
try {
    $class = $lecr->retrieveClassDetails($classId);
} catch (Exception $e) {
    echo "<script>
    documnet.addEventListener('DOMContentLoaded', function() {
        sendMessage('Error loading class info', 'danger');
        window.location.href = '" . SERVER_ROOT . "/php/lecturer_dashboard.php';
    });
    </script>";
    header("Location: " . SERVER_ROOT . "/php/lecturer_dashboard.php");
}

if ($user->isAdmin() || $user->isLecturer()) {
    echo "<div class='container mt-3'>
            <button class='btn btn-group btn-success' data-bs-toggle='modal' data-bs-target='#add_class' data-class-id='" . $classId . "'>Edit Class</button>
        </div>";
}
?>
<div class="container-md mt-3 p-3">
    <div class="input-group mb-3">
        <input type="search" class="form-control" placeholder="Search User" aria-label="Search Course"
            aria-describedby="course-addon" id="search-user-for-course" data-bs-toggle="dropdown"
            data-bs-target="#course-assigned-user" name="search-user-for-course">
        <div class="input-group-append">
            <button class="btn btn-secondary rounded btn-group" type="button" id="user-selectAll"
                onclick="selectAllUsers(this, 'student-list[]')">* select</button>
            <button class="btn btn-warning rounded btn-group" type="button" id="addStudents"
                onclick="updateStudent(this)">Update</button>
        </div>
        <div class="dropdown-menu" aria-labelledby="users-addon-course" style="overflow:hidden auto; max-height:245px;"
            id="course-assigned-user">
            <!-- users goes here -->
            <!-- wait -->
        </div>
    </div>
</div>
<div class="container-md mt-3 p-3">
    <table class="table table-hover border shadow">
        <h3 id="class-title"></h3>
        <h6><?php echo $class['class_date'] . " " . $class['start_time'] . " - " . $class['end_time']; ?></h6>
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
            <?php
            try {
                $studentList = $lecr->getStudentListByClassId($classId)->fetch_all();
            } catch (Exception $e) {
                echo "<script>
                sendMessage('Error loading class info', 'danger');
            </script>";
            }
            $i = 1;
            foreach ($studentList as $student) {
                echo "<tr id='std-" . $student[3] . "'>";
                echo "<td>" . $i++ . "</td>";
                echo "<td>" . $student[0] . "</td>";
                echo "<td>" . $student[1] . "</td>";
                // echo "<td>" . $student[4] . "</td>";
                echo "
                <td>
                    <div class='form-check form-check-inline'>
                        <input type='radio' class='form-check-input' id='std-" . $student[3] . "-present' name='std-" . $student[3] . "' value='1' " . (($student[4] == 1) ? "checked" : "") . " disabled>
                        <label class='form-check-label' for='std-" . $student[3] . "-present'><span class='badge text-bg-success'>Present</span></label>
                    </div>
                    <div class='form-check form-check-inline'>
                        <input type='radio' class='form-check-input' id='std-" . $student[3] . "-late' name='std-" . $student[3] . "' value='1' " . (($student[4] == 2) ? "checked" : "") . " disabled>
                        <label class='form-check-label' for='std-" . $student[3] . "-late'><span class='badge text-bg-warning'>Late</span></label>
                    </div>
                    <div class='form-check form-check-inline'>
                        <input type='radio' class='form-check-input' id='std-" . $student[3] . "-absent' name='std-" . $student[3] . "' value='1' " . (($student[4] == 0) ? "checked" : "") . " disabled>
                        <label class='form-check-label' for='std-" . $student[3] . "-absent'><span class='badge text-bg-danger'>Absent</span></label>
                    </div>
                </td>";
                echo "<td><button class='btn btn-success' onclick='markAttendace(this)' data-std-id='" . $student[3] . "' data-user-role='3' data-user-name='" . $student[1] . "' data-user-index='" . $student[2] . "' data-user-reg='" . $student[0] . "' data-attendance-status='" . $student[4] . "'>Mark</button>
                            <button class='btn btn-danger' id='remove-" . $student[3] . "' onclick='removeUserFromCourse(this)' data-std-id='" . $student[3] . "' data-user-role='3' data-user-name='" . $student[1] . "' data-user-index='" . $student[2] . "' data-user-reg='" . $student[0] . "'>Remove</button>
                            </td>";
                echo "</tr>";
                //<button class='btn btn-warning' onclick='editAttendance(this)' data-std-id='" . $student[3] . "' data-user-role='3' data-user-name='" . $student[1] . "' data-user-index='" . $student[2] . "' data-user-reg='" . $student[0] . "'>Edit</button>
            }

            ?>
        </tbody>
    </table>
</div>
<script>
    <?php
    echo "let classId = " . $_POST['class-id'] . ";";
    echo "let courseId = " . $class['course_id'] . ";";
    echo "let courseCode = '" . $class['course_code'] . "';";
    echo "let courseName = '" . $class['course_name'] . "';";
    echo "let classStart = '" . $class['start_time'] . "';";
    echo "let classEnd = '" . $class['end_time'] . "';";
    echo "let classDate = '" . $class['class_date'] . "';";
    ?>
    let addStudentsToClassList = [];
    let removeStudentsFromClassList = [];

    document.addEventListener('DOMContentLoaded', function () {
        if (classId != -1) {
            document.getElementById('class-title').innerHTML = courseCode + " - " + courseName;
        } else {
            sendMessage('No class selected', 'warning');
            window.location.href = '<?php echo SERVER_ROOT; ?>/php/lecturer_dashboard.php';
        }
    });

    document.getElementById('search-user-for-course').addEventListener('keyup', function (event) {
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
                success: function (response) {
                    let userListCourse = $('#course-assigned-user');
                    userListCourse.empty();
                    userListCourse.append($(document.createElement("div")).addClass("dropdown-header").text("Student"));
                    (response.students).forEach(student => {
                        let data = $(document.createElement("div")).addClass("form-check").addClass("dropdown-item");
                        data.append($(document.createElement("input")).addClass("form-check-input").attr("value", student[3]).attr("type", "checkbox").attr("id", "filterStdCheckbox" + student[3]).attr("name", "student-list[]").attr("data-user-role", 3).attr("data-user-name", student[1]).attr("data-user-index", student[2]).attr("data-user-reg", student[0]).attr("onchange", "addStdToClass(this)"));
                        data.append($(document.createElement("label")).addClass("form-check-label").attr("for", "filterStdCheckbox" + student[3]).text(student[2] + " - " + student[0]));
                        userListCourse.append(data);
                    });
                    userListCourse.append($(document.createElement("div")).addClass("dropdown-divider"));
                    $('#course-assigned-user').selectpicker('refresh');

                },
                error: function () {
                    sendMessage('Error on loading users', 'danger');
                }
            })
        }
    });


    function selectAllUsers(button, selection) {
        let checkboxes = document.getElementsByName(selection);
        if (button.classList.contains('btn-secondary')) {
            for (let i = 0; i < checkboxes.length; i++) {

                checkboxes[i].checked = true;
                addStdToClass(checkboxes[i]);
            }
            button.classList.remove('btn-secondary');
            button.classList.add('btn-primary');

        } else {
            for (let i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = false;
                addStdToClass(checkboxes[i]);
            }
            button.classList.remove('btn-primary');
            button.classList.add('btn-secondary');
        }
    }

    function addStdToClass(std) {
        if (std.checked) {
            if (!addStudentsToClassList.includes(std.value)) {
                addStudentsToClassList.push(std.value);
                let table = document.getElementById('class-attendance');
            }
        } else {
            addStudentsToClassList.splice(addStudentsToClassList.indexOf(std.value), 1);
        }
    }

    function updateStudent() {
        if (addStudentsToClassList.length > 0 || removeStudentsFromClassList.length > 0) {
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/mark_attendance_action.php',
                data: {
                    addStudentsToClassList: addStudentsToClassList,
                    removeStudentsFromClassList: removeStudentsFromClassList,
                    classId: classId
                },
                dataType: 'json',
                success: function (response) {
                    if (response.error) {
                        sendMessage(response.errors, 'danger');
                    } else {
                        sendMessage(response.messages, 'success', true);
                    }
                },
                error: function () {
                    sendMessage('Error on loading users', 'danger');
                }
            })
        } else {
            sendMessage('No changes made', 'warning');
        }
    }

    function markAttendace(std) {
        if (std.dataset.attendanceStatus == 1 || std.dataset.attendanceStatus == 2) {
            sendMessage('Already marked', 'warning');
            return 0;
        }
        let currentTime = new Date();
        let currentTimeString = [
            currentTime.getHours().toString().padStart(2, '0'),
            currentTime.getMinutes().toString().padStart(2, '0'),
            currentTime.getSeconds().toString().padStart(2, '0')
        ].join(':');

        let classStartTime = new Date(currentTime.toDateString() + " " + classStart);
        let classEndTime = new Date(currentTime.toDateString() + " " + classEnd);
        let timeSpace = 10; //10 Minutes before and after
        let timeDifferece = Math.floor(Math.abs(currentTime - classStartTime) / 60000);

        if (currentTime < classStartTime && timeDifferece > timeSpace) {
            sendMessage('Class has not started yet', 'warning');
            return 0;
        } else if (currentTime > classEndTime) {
            sendMessage('Class has ended', 'warning');
            return 0;
        } else {
            let attendanceStatus = 1;
            if (currentTime > classStartTime && timeDifferece > timeSpace) {
                attendanceStatus = 2;
            }
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/mark_attendance_action.php',
                data: {
                    stdId: std.dataset.stdId,
                    classId: classId,
                    attendanceStatus: attendanceStatus,
                    currentTimeString: currentTimeString
                },
                dataType: 'json',
                success: function (response) {
                    if (response.error) {
                        sendMessage(response.errors, 'danger', true);
                    } else {
                        sendMessage(response.messages, 'success', true);
                    }
                },
                error: function () {
                    sendMessage('Error on loading users', 'danger');
                }
            })
        }

    }

    function removeUserFromCourse(std) {
        let stdId = std.dataset.stdId;
        if (removeStudentsFromClassList.includes(stdId)) {
            removeStudentsFromClassList.splice(removeStudentsFromClassList.indexOf(stdId), 1);
            $('#remove-' + stdId).removeClass('btn-warning');
            $('#remove-' + stdId).addClass('btn-danger');
            $('#remove-' + stdId).text('Remove');
            return 0;
        } else {
            removeStudentsFromClassList.push(stdId);
            $('#remove-' + stdId).removeClass('btn-danger');
            $('#remove-' + stdId).addClass('btn-warning');
            $('#remove-' + stdId).text('-Pending');
        }
    }
</script>
<?php
include ROOT_PATH . '/php/include/footer.php';
?>