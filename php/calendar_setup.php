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

header('Content-Type: application/json');

if (isset($_POST['lecr_id'])) {
    if (($user->isAdmin() || $user->isLecturer() || $user->isInstructor()) && $_SESSION['lecr_id'] == $_POST['lecr_id']) {
        $lecr_id = $_POST['lecr_id'];
        $events = [];
        $startDate = substr($_POST['start'], 0, 10);
        $endDate = substr($_POST['end'], 0, 10);

        if ($user->isInstructor()) {
            $classes = $lecr->getClassesForInstructor($lecr_id, $startDate, $endDate);
        } else {
            $classes = $lecr->getClassByLecturer($lecr_id, $startDate, $endDate);
        }

        while ($class = $classes->fetch_assoc()) {
            $event = [];
            $event['id'] = $class['class_id'];
            $event['title'] = $class['course_code'] . ' - ' . $class['course_name'];
            $event['start'] = $class['class_date'] . "T" . $class['start_time'];
            $event['end'] = $class['class_date'] . "T" . $class['end_time'];
            $classAttendance = $lecr->classAttendancePrecentage($class['class_id']);
            if ($classAttendance < 0.5) {
                $event['color'] = '#ff0000';
            } else if ($classAttendance < 0.75) {
                $event['color'] = '#ff8000';
            } else {
                $event['color'] = '#00ff00';
            }
            $events[] = $event;
        }
        echo json_encode($events);
    } else {
        echo json_encode(array("message" => "Unauthorized access."));
    }
}
