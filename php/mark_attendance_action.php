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

if (isset($_POST["userSearch"]) && $user->isAdmin()) {
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
} else {
    $response['error'] = true;
    $response['errors'] = ["Unauthorized access"];
    echo json_encode($response);
}
