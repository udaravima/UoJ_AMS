<?php
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$lecr = new Lecturer($conn);

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $usernameAvailable = $user->isUsernameAvailable($username);
    header('Content-Type: application/json');
    if ($usernameAvailable) {
        echo json_encode(['available' => true]);
    } else {
        echo json_encode(['available' => false]);
    }
} else {
    header("Location: " . SERVER_ROOT . "/index.php");
}
//  else {
//     echo json_encode(['error' => 'Username parameter not provided']);
// }


?>