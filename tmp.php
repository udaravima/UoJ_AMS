<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/MyAttendanceSys/config.php';
require_once 'config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

function test($string = 'default')
{
    echo $string . "<br>";
}
test();
echo "<br>";
echo "<br>";

$intV = 15;
$charV = '15';
$strV = "15";

echo $intV == $charV;
echo "<br>";
echo $intV == $strV;
echo "<br>";
echo $charV == $strV;
echo "<br>";
echo $intV === $charV;
echo "<br>";
echo $intV === $strV;
echo "<br>";
echo $charV === $strV;
echo "<br>";
echo $intV + 1;
echo "<br>";
echo $charV + 1;
echo "<br>";
echo $strV + 1;
echo "<br>";

echo $charV - 1;

echo "<br>";

$userOrder = array();
$userOrder['search'] = 'some';
$result = $user->getLecturerTable($userOrder);
// echo $result;
while ($row = $result->fetch_assoc()) {
    print_r($row);
    echo "<br>";
}

// echo $db;
echo "<br><br>" . ROOT_PATH . "<br>";
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

?>