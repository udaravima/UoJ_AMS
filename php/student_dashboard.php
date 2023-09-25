<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/MyAttendanceSys/config.php';
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Utils.php';
$utils = new Utils();
$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);

if (!($user->isLoggedIn()) || $_SESSION['user_role'] > 3) {
    header("Location: " . SERVER_ROOT . "/index.php");
}

?>
<?php
include ROOT_PATH . '/php/include/header.php';
echo "<title>AMS Student</title>";
include ROOT_PATH . '/php/include/content.php';

$activeDash = 3;
include ROOT_PATH . '/php/include/nav.php';
include ROOT_PATH . '/php/include/footer.php';
?>