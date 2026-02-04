<?php
/**
 * Report Class
 * Handles data retrieval for generating reports
 */
class Report
{
    private $conn;
    private $userTable = 'uoj_user';
    private $lecrTable = 'uoj_lecturer';
    private $stdTable = 'uoj_student';
    private $courseTable = 'uoj_course';
    private $classTable = 'uoj_class';
    private $stdClassTable = 'uoj_student_class';
    private $stdCourseTable = 'uoj_student_course';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Get class attendance report data
     * Including student details and their status for a specific class
     */
    public function getClassAttendanceReport($classId)
    {
        // Get Class Details
        $classQuery = "SELECT c.*, co.course_code, co.course_name, l.lecr_name 
                       FROM {$this->classTable} c
                       INNER JOIN {$this->courseTable} co ON c.course_id = co.course_id
                       LEFT JOIN {$this->lecrTable} l ON c.lecr_id = l.lecr_id
                       WHERE c.class_id = ?";
        $stmt = $this->conn->prepare($classQuery);
        $stmt->bind_param('i', $classId);
        $stmt->execute();
        $classResult = $stmt->get_result();

        if ($classResult->num_rows == 0) {
            return false;
        }
        $classData = $classResult->fetch_assoc();

        // Get Attendance Data
        $attQuery = "SELECT s.std_index, s.std_fullname, sc.attendance_status, sc.attend_time
                     FROM {$this->stdClassTable} sc
                     INNER JOIN {$this->stdTable} s ON sc.std_id = s.std_id
                     WHERE sc.class_id = ?
                     ORDER BY s.std_index ASC";
        $stmt = $this->conn->prepare($attQuery);
        $stmt->bind_param('i', $classId);
        $stmt->execute();
        $attResult = $stmt->get_result();

        $attendanceData = [];
        $summary = ['present' => 0, 'absent' => 0, 'late' => 0, 'total' => 0];

        while ($row = $attResult->fetch_assoc()) {
            $attendanceData[] = $row;
            if ($row['attendance_status'] == 1)
                $summary['present']++;
            elseif ($row['attendance_status'] == 2)
                $summary['late']++;
            else
                $summary['absent']++;
        }
        $summary['total'] = count($attendanceData);

        return [
            'info' => $classData,
            'attendance' => $attendanceData,
            'summary' => $summary
        ];
    }

    /**
     * Get student attendance report for a specific course or overall
     */
    public function getStudentAttendanceReport($studentId, $courseId = null)
    {
        // Get Student Details
        $stdQuery = "SELECT * FROM {$this->stdTable} WHERE std_id = ?";
        $stmt = $this->conn->prepare($stdQuery);
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        $stdResult = $stmt->get_result();

        if ($stdResult->num_rows == 0)
            return false;
        $studentData = $stdResult->fetch_assoc();

        // Query Builder for Attendance
        $query = "SELECT c.class_date, c.start_time, c.end_time, co.course_code, co.course_name, sc.attendance_status
                  FROM {$this->stdClassTable} sc
                  INNER JOIN {$this->classTable} c ON sc.class_id = c.class_id
                  INNER JOIN {$this->courseTable} co ON c.course_id = co.course_id
                  WHERE sc.std_id = ?";

        $params = [$studentId];
        $types = 'i';

        if ($courseId) {
            $query .= " AND c.course_id = ?";
            $params[] = $courseId;
            $types .= 'i';
        }

        $query .= " ORDER BY c.class_date DESC, c.start_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $records = [];
        $summary = ['present' => 0, 'late' => 0, 'absent' => 0, 'total' => 0];

        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
            if ($row['attendance_status'] == 1)
                $summary['present']++;
            elseif ($row['attendance_status'] == 2)
                $summary['late']++;
            else
                $summary['absent']++;
        }
        $summary['total'] = count($records);
        $summary['percentage'] = $summary['total'] > 0 ?
            round((($summary['present'] + $summary['late']) / $summary['total']) * 100, 2) : 0;

        return [
            'student' => $studentData,
            'records' => $records,
            'summary' => $summary
        ];
    }

    /**
     * Get Course Overview Report
     */
    public function getCourseAttendanceReport($courseId)
    {
        // Get Course Details
        $cQuery = "SELECT * FROM {$this->courseTable} WHERE course_id = ?";
        $stmt = $this->conn->prepare($cQuery);
        $stmt->bind_param('i', $courseId);
        $stmt->execute();
        $cResult = $stmt->get_result();

        if ($cResult->num_rows == 0)
            return false;
        $courseData = $cResult->fetch_assoc();

        // Get All Classes for Course
        $classQuery = "SELECT class_id, class_date, start_time, end_time FROM {$this->classTable} 
                       WHERE course_id = ? ORDER BY class_date DESC";
        $stmt = $this->conn->prepare($classQuery);
        $stmt->bind_param('i', $courseId);
        $stmt->execute();
        $classResult = $stmt->get_result();

        $classes = [];
        $totalClasses = 0;

        while ($row = $classResult->fetch_assoc()) {
            // Get attendance stats for each class
            $stats = $this->getClassStats($row['class_id']);
            $row['stats'] = $stats;
            $classes[] = $row;
            $totalClasses++;
        }

        return [
            'course' => $courseData,
            'classes' => $classes,
            'total_classes' => $totalClasses
        ];
    }

    private function getClassStats($classId)
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as late
                  FROM {$this->stdClassTable} WHERE class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $classId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
