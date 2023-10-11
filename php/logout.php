<?php
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);

session_start();
$user->setUserLock(false);
header("Location: " . SERVER_ROOT . "/index.php");
$_SESSION['user_id'] = '';
session_destroy();
header("Location: " . SERVER_ROOT . "/index.php");
?>