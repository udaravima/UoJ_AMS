<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-API-Key");

require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/NFC.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';

$database = new Database();
$db = $database->getConnection();

$nfc = new NFC($db);
$lecr = new Lecturer($db);

// API Key Authentication
$headers = getallheaders();
$apiKey = $headers['X-API-Key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? null;

// Define API key from environment variable or default (CHANGE IN PRODUCTION!)
define('NFC_API_KEY', getenv('NFC_API_KEY') ?: 'CHANGE_ME_IN_PRODUCTION');

if ($apiKey !== NFC_API_KEY) {
    http_response_code(401);
    echo json_encode(array("status" => "error", "message" => "Unauthorized: Invalid API Key"));
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nfc_uid) && !empty($data->class_id)) {

    // 1. Validate NFC
    $student = $nfc->getStudentByNFC($data->nfc_uid);

    if ($student) {
        $stdId = $student['std_id'];
        $classId = $data->class_id;
        $attendTime = date('H:i:s');

        // 2. Calculate attendance status based on class time (server-side)
        $attendanceStatus = $lecr->calculateAttendanceStatus($classId, $attendTime);

        if ($lecr->markAttendance($stdId, $classId, $attendTime, $attendanceStatus)) {
            $statusText = ['Absent', 'Present', 'Late'][$attendanceStatus];
            http_response_code(200);
            echo json_encode(array(
                "status" => "success",
                "message" => "Attendance marked as " . $statusText . " for " . $student['std_fullname'],
                "attendance_status" => $attendanceStatus
            ));
        } else {
            http_response_code(503);
            echo json_encode(array("status" => "error", "message" => "Unable to mark attendance."));
        }
    } else {
        http_response_code(404);
        echo json_encode(array("status" => "error", "message" => "Card not registered."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("status" => "error", "message" => "Incomplete data. Provide nfc_uid and class_id."));
}
