<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/NFC.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';

$database = new Database();
$db = $database->getConnection();

$nfc = new NFC($db);
$lecr = new Lecturer($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nfc_uid) && !empty($data->class_id)) {

    // 1. Validate NFC
    $student = $nfc->getStudentByNFC($data->nfc_uid);

    if ($student) {
        $stdId = $student['std_id'];
        $classId = $data->class_id;
        $attendTime = date('H:i:s');

        // 2. Mark Attendance (Status 1 = Present)
        // Check if class exists and is active/valid time? 
        // For now, assume device handles timing or Lecturer::markAttendance updates timestamp

        if ($lecr->markAttendance($stdId, $classId, $attendTime, 1)) {
            http_response_code(200);
            echo json_encode(array(
                "status" => "success",
                "message" => "Attendance marked for " . $student['std_fullname']
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
