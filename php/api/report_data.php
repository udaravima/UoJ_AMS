<?php
require_once '../../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$lecr = new Lecturer($conn);

header('Content-Type: application/json');

if (!($user->isLoggedIn()) || !($user->isAdmin() || $user->isLecturer() || $user->isInstructor())) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_courses':
        $query = "SELECT course_id, course_code, course_name FROM uoj_course ORDER BY course_code";
        $result = $conn->query($query);
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = [
                'id' => $row['course_id'],
                'text' => $row['course_code'] . ' - ' . $row['course_name']
            ];
        }
        echo json_encode($courses);
        break;

    case 'get_students':
        $courseId = $_GET['course_id'] ?? null;

        if ($courseId) {
            // Get students enrolled in a specific course
            $query = "SELECT DISTINCT s.std_id, s.std_index, s.std_fullname
                      FROM uoj_student s
                      INNER JOIN uoj_student_course sc ON s.std_id = sc.std_id
                      WHERE sc.course_id = ?
                      ORDER BY s.std_index";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $courseId);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            // Get all students
            $query = "SELECT std_id, std_index, std_fullname FROM uoj_student ORDER BY std_index";
            $result = $conn->query($query);
        }

        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'id' => $row['std_id'],
                'text' => $row['std_index'] . ' - ' . $row['std_fullname']
            ];
        }
        echo json_encode($students);
        break;

    case 'get_classes':
        $courseId = $_GET['course_id'] ?? null;

        if ($courseId) {
            $query = "SELECT c.class_id, c.class_date, c.start_time, c.end_time,
                             co.course_code, co.course_name
                      FROM uoj_class c
                      INNER JOIN uoj_course co ON c.course_id = co.course_id
                      WHERE c.course_id = ?
                      ORDER BY c.class_date DESC, c.start_time DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $courseId);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $query = "SELECT c.class_id, c.class_date, c.start_time, c.end_time,
                             co.course_code, co.course_name
                      FROM uoj_class c
                      INNER JOIN uoj_course co ON c.course_id = co.course_id
                      ORDER BY c.class_date DESC, c.start_time DESC
                      LIMIT 100";
            $result = $conn->query($query);
        }

        $classes = [];
        while ($row = $result->fetch_assoc()) {
            $classes[] = [
                'id' => $row['class_id'],
                'text' => $row['class_date'] . ' | ' .
                         substr($row['start_time'], 0, 5) . '-' . substr($row['end_time'], 0, 5) . ' | ' .
                         $row['course_code']
            ];
        }
        echo json_encode($classes);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
