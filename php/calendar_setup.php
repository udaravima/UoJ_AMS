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
    if (($user->isAdmin() || $user->isLecturer()) && $_SESSION['lecr_id'] == $_POST['lecr_id'] && isset($_POST['event_date'])) {
        $lecr_id = $_POST['lecr_id'];
        $event_date = $_POST['event_date'];
        $events = [];
        // $classes = $lecr->getClassByLecturer($lecr_id, $event_date);
        $classes = $lecr->getClassByLecturer($lecr_id); // Debug
        // foreach ($classes as $class) {
        //     $event = [];
        //     $event['id'] = $class['class_id'];
        //     $event['title'] = $class['course_code'] . ' - ' . $class['course_name'];
        //     $event['start'] = $class['start_time'];
        //     $event['end'] = $class['end_time'];
        //     $event['color'] = $utils->getRandomColor();
        //     $events[] = $event;
        // }
        while ($class = $classes->fetch_assoc()) {
            $event = [];
            $event['id'] = $class['class_id'];
            $event['title'] = $class['course_code'] . ' - ' . $class['course_name'];
            $event['start'] = $class['class_date'] . "T" . $class['start_time'];
            $event['end'] = $class['class_date'] . "T" . $class['end_time'];
            // $event['color'] = $utils->getRandomColor();
            // $classStudentCount = $lecr->retrieveTotalAttendanceTotalCountByClass($class['class_id']);
            // $classPresentCount = $lecr->retrieveTotalAttendancePresentCountByClass($class['class_id']);
            // if ($classPresentCount / $classStudentCount < 0.5) {
            //     $event['color'] = '#ff0000';
            // } else {
            //     $event['color'] = '#00ff00';
            // }
            $events[] = $event;
        }
        echo json_encode($events);
    } else {
        echo json_encode(array("message" => "Unauthorized access."));
    }
}
