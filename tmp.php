<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/MyAttendanceSys/config.php';
include_once ROOT_PATH . '/php/config/Database.php';


$database = new Database();
$db = $database->getConnection();
// echo $db;
echo "<br>" . ROOT_PATH . "<br>";
$password = "admin123@";
$salt = bin2hex(random_bytes(16));
$hash = password_hash($password . $salt, PASSWORD_BCRYPT);
echo $hash . "<br>";
echo $salt . "<br>";
if (password_verify($password . $salt, $hash)) {
    echo "<br>true";
} else {
    echo "<br>false";
}
echo "<br>" . __DIR__;
echo "<br>";
$userArray = array();
$userArray['username'] = "admin";
echo $userArray['username'] . " " . $userArray['password']. "Hello";
?>