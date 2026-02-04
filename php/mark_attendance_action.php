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

if (!($user->isLoggedIn()) || $_SESSION['user_role'] > 2) {
    echo json_encode(['error' => true, 'errors' => ['Unauthorized access']]);
    exit();
}

if (isset($_POST["userSearch"]) && $user->isAdmin()) {
    $courseId = $_POST["courseId"];
    $errors = [];
    $messages = [];
    $order = [];
    $response = [];
    $order['search'] = $_POST["userSearch"];
    try {
        $students = $lecr->getStudentsFromCourseId($courseId, $order);
        $response['students'] = $students->fetch_all();
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        $errors[] = "Error in retrieving students";
        $response['students'] = [];
    }
    $response['errors'] = $errors;
    $response['error'] = false;
    echo json_encode($response);
    //add user to course by admin

} else if (isset($_POST['addStudentsToClassList']) || isset($_POST['removeStudentsFromClassList'])) {
    $classId = $_POST['classId'];
    $studentsToAdd = isset($_POST['addStudentsToClassList']) ? $_POST['addStudentsToClassList'] : [];
    $studentsToRemove = isset($_POST['removeStudentsFromClassList']) ? $_POST['removeStudentsFromClassList'] : [];
    $errors = [];
    $messages = [];
    $response = [];
    foreach ($studentsToAdd as $student) {
        try {
            $lecr->markAttendance($student, $classId, "", 0);
            $messages[] = "Student " . $student . " added to class";
        } catch (Exception $e) {
            $errors[] = "Error in adding student to class";
        }
    }
    foreach ($studentsToRemove as $student) {
        try {
            $lecr->removeStudentFromClass($student, $classId);
            $messages[] = "Student " . $student . " removed from class";
        } catch (Exception $e) {
            $errors[] = "Error in removing student from class";
        }
    }
    $response['errors'] = $errors;
    $response['messages'] = $messages;
    $response['error'] = false;
    echo json_encode($response);
} else if (isset($_POST['attendanceStatus']) && isset($_POST['currentTimeString'])) {
    $std_id = $_POST['stdId'];
    $class_id = $_POST['classId'];
    $attendanceStatus = $_POST['attendanceStatus'];
    $currentTimeString = $_POST['currentTimeString'];
    $errors = [];
    $messages = [];
    $response = [];
    try {
        $lecr->editAttendance($std_id, $class_id, $currentTimeString, $attendanceStatus);
        $messages[] = "Attendance marked";
    } catch (Exception $e) {
        $errors[] = "Error in marking attendance";
    }
    $response['errors'] = $errors;
    $response['messages'] = $messages;
    $response['error'] = false;
    echo json_encode($response);
} else {
    $response['error'] = true;
    $response['errors'] = ["Unauthorized access"];
    echo json_encode($response);
}
