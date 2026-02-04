<?php
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/Report.php';
include_once ROOT_PATH . '/php/class/Utils.php';

// Load Dompdf
// Adjust path based on your installation structure
if (file_exists(ROOT_PATH . '/vendor/dompdf/dompdf/autoload.inc.php')) {
    require_once ROOT_PATH . '/vendor/dompdf/dompdf/autoload.inc.php';
} elseif (file_exists(ROOT_PATH . '/vendor/dompdf/autoload.inc.php')) {
    require_once ROOT_PATH . '/vendor/dompdf/autoload.inc.php';
} else {
    die("Dompdf library not found. Please install it.");
}

use Dompdf\Dompdf;
use Dompdf\Options;

$db = new Database();
$conn = $db->getConnection();
$report = new Report($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_type'])) {

    $reportType = $_POST['report_type'];
    $html = '';
    $filename = 'report.pdf';

    // Configure Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    // CSS Styles
    $css = "
        <style>
            body { font-family: sans-serif; }
            h1, h2, h3 { color: #333; text-align: center; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .summary-box { margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; background: #fafafa; }
            .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 50px; text-align: center; font-size: 10px; color: #666; }
            .status-present { color: green; }
            .status-late { color: orange; }
            .status-absent { color: red; }
        </style>
    ";

    switch ($reportType) {
        case 'class_attendance':
            $classId = $_POST['class_id'];
            $data = $report->getClassAttendanceReport($classId);

            if (!$data)
                die("No data found for Class ID: $classId");

            $filename = "Class_Report_{$classId}.pdf";
            $html = "
                $css
                <h1>Class Attendance Report</h1>
                <div class='summary-box'>
                    <p><strong>Course:</strong> {$data['info']['course_code']} - {$data['info']['course_name']}</p>
                    <p><strong>Date:</strong> {$data['info']['class_date']}</p>
                    <p><strong>Time:</strong> {$data['info']['start_time']} - {$data['info']['end_time']}</p>
                    <p><strong>Lecturer:</strong> {$data['info']['lecr_name']}</p>
                    <hr>
                    <p><strong>Total:</strong> {$data['summary']['total']} | 
                       <strong>Present:</strong> {$data['summary']['present']} | 
                       <strong>Late:</strong> {$data['summary']['late']} | 
                       <strong>Absent:</strong> {$data['summary']['absent']}</p>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reg No</th>
                            <th>Name</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>";

            $i = 1;
            foreach ($data['attendance'] as $row) {
                $status = $row['attendance_status'] == 1 ? 'Present' : ($row['attendance_status'] == 2 ? 'Late' : 'Absent');
                $colorClass = $row['attendance_status'] == 1 ? 'status-present' : ($row['attendance_status'] == 2 ? 'status-late' : 'status-absent');
                $html .= "
                    <tr>
                        <td>" . $i++ . "</td>
                        <td>{$row['std_index']}</td>
                        <td>{$row['std_fullname']}</td>
                        <td>{$row['attend_time']}</td>
                        <td class='$colorClass'>$status</td>
                    </tr>";
            }
            $html .= "</tbody></table>";
            break;

        case 'student_attendance':
            $studentId = $_POST['student_id'];
            $courseId = !empty($_POST['course_id']) ? $_POST['course_id'] : null;
            $data = $report->getStudentAttendanceReport($studentId, $courseId);

            if (!$data)
                die("No data found for Student ID: $studentId");

            $filename = "Student_Report_{$studentId}.pdf";
            $html = "
                $css
                <h1>Student Attendance Report</h1>
                <div class='summary-box'>
                    <p><strong>Name:</strong> {$data['student']['std_fullname']}</p>
                    <p><strong>Reg No:</strong> {$data['student']['std_index']}</p>
                    <p><strong>Department:</strong> {$data['student']['current_level']}</p>
                    <hr>
                    <p><strong>Overall Attendance:</strong> {$data['summary']['percentage']}%</p>
                    <p><strong>Total Classes:</strong> {$data['summary']['total']} | 
                       <strong>Present:</strong> {$data['summary']['present']} | 
                       <strong>Late:</strong> {$data['summary']['late']} | 
                       <strong>Absent:</strong> {$data['summary']['absent']}</p>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Course</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($data['records'] as $row) {
                $status = $row['attendance_status'] == 1 ? 'Present' : ($row['attendance_status'] == 2 ? 'Late' : 'Absent');
                $colorClass = $row['attendance_status'] == 1 ? 'status-present' : ($row['attendance_status'] == 2 ? 'status-late' : 'status-absent');
                $html .= "
                    <tr>
                        <td>{$row['class_date']}</td>
                        <td>" . substr($row['start_time'], 0, 5) . " - " . substr($row['end_time'], 0, 5) . "</td>
                        <td>{$row['course_code']}</td>
                        <td class='$colorClass'>$status</td>
                    </tr>";
            }
            $html .= "</tbody></table>";
            break;

        case 'course_summary':
            $courseId = $_POST['course_id'];
            $data = $report->getCourseAttendanceReport($courseId);

            if (!$data)
                die("No data found for Course ID: $courseId");

            $filename = "Course_Report_{$courseId}.pdf";
            $html = "
                $css
                <h1>Course Summary Report</h1>
                <div class='summary-box'>
                    <p><strong>Course:</strong> {$data['course']['course_code']} - {$data['course']['course_name']}</p>
                    <p><strong>Total Classes Held:</strong> {$data['total_classes']}</p>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Class ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Total Students</th>
                            <th>Present</th>
                            <th>Late</th>
                            <th>Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($data['classes'] as $row) {
                $total = $row['stats']['total'];
                $present = $row['stats']['present'] + $row['stats']['late'];
                $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

                $html .= "
                    <tr>
                        <td>{$row['class_id']}</td>
                        <td>{$row['class_date']}</td>
                        <td>" . substr($row['start_time'], 0, 5) . " - " . substr($row['end_time'], 0, 5) . "</td>
                        <td>{$total}</td>
                        <td>{$row['stats']['present']}</td>
                        <td>{$row['stats']['late']}</td>
                        <td>{$percentage}%</td>
                    </tr>";
            }
            $html .= "</tbody></table>";
            break;

        default:
            die("Invalid Report Type");
    }

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream($filename, ["Attachment" => false]);

} else {
    echo "This script expects a POST request.";
}
