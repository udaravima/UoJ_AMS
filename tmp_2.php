<?php
include_once 'config.php';
include_once ROOT_PATH . '/php/include/header.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';
include_once ROOT_PATH . '/php/class/Utils.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$lecr = new Lecturer($conn);
$utils = new Utils();
?>
<title>Temporary</title>
<?php
include_once ROOT_PATH . '/php/include/content.php';
?>
<h1>Temporary</h1>

<div class="container mt-5">
    <h1>Temp testing</h1>
    <input type="search" class="form-control" placeholder="Search User" aria-label="Search Course" aria-describedby="course-addon" id="search-user-for-course" data-bs-toggle="dropdown" data-id="#tempo" name="search-user-for-course">
    <select class="selectpicker" multiple aria-label="Default select example" id="tempo">
        <optgroup label="Hellow 1">
            <option value="1">One</option>
            <option value="2">Two</option>
        </optgroup>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
        <option value="4">Four</option>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
        <option value="4">Four</option>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
        <option value="4">Four</option>
    </select>
</div>

<div class="container mt-5">
    dropdown
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown button
        </button>
        <ul class="dropdown-menu inner" style="overflow:hidden auto; max-height:245px;" aria-labelledby="dropdownMenuButton1">
            <li class="dropdown-header">Dropdown header</li>
            <li><a class="dropdown-item selected" href="#"><span class="bs-ok-default check-mark"></span><span class="text">Helo</span></a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li class="dropdown-divider">Hello</li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
        </ul>
    </div>
</div>
<div class="container mt-5 ">
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-target="#dd1" aria-expanded="false">DropDown</button>
        <div class="dropdown-menu" id="dd1">
            <div class="dropdown-header">Student</div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" value="1" name="selCourse[]" id="cour1">
                <label for="cour1" class="form-check-label">Hellow</label>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" value="1" name="selCourse[]" id="cour1">
                <label for="cour1" class="form-check-label">Hellow</label>
            </div>
            <div class="dropdown-divider"></div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" value="1" name="selCourse[]" id="cour1">
                <label for="cour1" class="form-check-label">Hellow</label>
            </div>
        </div>
    </div>
</div>
<div class="container mt-5">
    <!-- create a example table bootstrap -->
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Occupation</th>
            </tr>
        </thead>
        <tbody>
            <tr data-bs-toggle="collapse" data-bs-target="#accordion1" class="accordion-toggle">
                <td>John</td>
                <td>25</td>
                <td>Teacher</td>
            </tr>
            <tr>
                <td colspan="3" class="hiddenRow accordion-item">
                    <div class="accordion-body collapse" id="accordion1">
                        <p>John likes to read books and play chess.</p>
                    </div>
                </td>
            </tr>
            <tr data-bs-toggle="collapse" data-bs-target="#accordion2" class="accordion-toggle">
                <td>Mary</td>
                <td>30</td>
                <td>Doctor</td>
            </tr>
            <tr>
                <td colspan="3" class="hiddenRow accordion-item">
                    <div class="accordion-body collapse" id="accordion2">
                        <p>Mary enjoys traveling and learning new languages.</p>
                    </div>
                </td>
            </tr>
            <tr data-bs-toggle="collapse" data-bs-target="#accordion3" class="accordion-toggle">
                <td>Bob</td>
                <td>35</td>
                <td>Engineer</td>
            </tr>
            <tr>
                <td colspan="3" class="hiddenRow accordion-item">
                    <div class="accordion-body collapse" id="accordion3">
                        <p>Bob likes to build things and solve problems.</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="container">
    <div class="" id="lecturer-class-calendar">
    </div>
</div>

<div class="containe">
    <?php
    $classes = $lecr->getClassByLecturer(1);
    // var_dump($classes);
    // foreach ($classes as $class) {
    //     $event = [];
    //     $event['id'] = $class[0];
    //     $event['title'] = $class[6] . ' - ' . $class[7];
    //     $event['start'] = $class[4];
    //     $event['end'] = $class[5];
    //     // $classStudentCount = $lecr->retrieveTotalAttendanceTotalCountByClass($class[0]);
    //     // $classPresentCount = $lecr->retrieveTotalAttendancePresentCountByClass($class[0]);
    //     // if ($classPresentCount / $classStudentCount < 0.5) {
    //     //     $event['color'] = '#ff0000';
    //     // } else {
    //     //     $event['color'] = '#00ff00';
    //     // }
    //     // $event['color'] = $utils->getRandomColor();
    //     $events[] = $event;
    //     // print_r($class);
    // }
    $events = [];
    while ($class = $classes->fetch_assoc()) {
        $event = [];
        $event['id'] = $class['class_id'];
        $event['title'] = $class['course_code'] . ' - ' . $class['course_name'];
        $event['start'] = $class['class_date'] . "T" . $class['start_time'];
        $event['end'] = $class['class_date'] . "T" . $class['end_time'];
        // $classStudentCount = $lecr->retrieveTotalAttendanceTotalCountByClass($class['class_id']);
        // $classPresentCount = $lecr->retrieveTotalAttendancePresentCountByClass($class['class_id']);
        // if ($classPresentCount / $classStudentCount < 0.5) {
        //     $event['color'] = '#ff0000';
        // } else {
        //     $event['color'] = '#00ff00';
        // }
        $events[] = $event;
    }
    print_r($events);

    ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('lecturer-class-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
        });
        calendar.render();
    });
</script>
<?php
include_once ROOT_PATH . '/php/include/footer.php';
