<?php
class Lecturer
{
    // Data Tables
    private $std = 'uoj_student';
    private $lecr = 'uoj_lecturer';
    private $course = 'uoj_course';
    private $lecrCourse = 'uoj_lecturer_course';
    private $class = 'uoj_class';
    private $stdCourse = 'uoj_student_course';
    private $stdClass = 'uoj_student_class';
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createClass($lecrId, $courseId, $date, $sTime, $endTime)
    {
        $query = "INSERT INTO $this->class(lecr_id, course_id, class_date, start_time, end_time) VALUES(?,?,?,?,?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iisss', $lecrId, $courseId, $date, $sTime, $endTime);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function createCourse($courseCode, $courseName)
    {
        $query = "INSERT INTO $this->course(course_code, course_name) VALUES(?,?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $courseCode, $courseName);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteCourse($courseId)
    {
        $query = "DELETE FROM $this->course WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $courseId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteClass($classId)
    {
        $query = "DELETE FROM $this->class WHERE class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $classId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateClassInfo($classId, $classData)
    {
        $query = "UPDATE $this->class SET class_date = ?, start_time = ?, end_time = ? WHERE class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssi', $classData['class_date'], $classData['start_time'], $classData['end_time'], $classId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateCourse($courseId, $courseData)
    {
        $query = "UPDATE $this->course SET course_code = ?, course_name = ? WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssi', $courseData['course_code'], $courseData['course_name'], $courseId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function derollLecturerCourse($lecrId, $courseId)
    {
        $query = "DELETE FROM $this->lecrCourse WHERE lecr_id = ? AND course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $lecrId, $courseId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function derollStudentCourse($stdId, $courseId)
    {
        $query = "DELETE FROM $this->stdCourse WHERE std_id = ? AND course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $stdId, $courseId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getLecturerCourseList($lecrId, $order)
    {
        $query = "SELECT * FROM $this->lecrCourse WHERE lecr_id = ?";
        if ($order['search']) {
            $query .= " AND course_id LIKE '%" . $order['search'] . "%'";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $lecrId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }

    }

    public function getStudentCourseList($stdId)
    {
        $query = "SELECT * FROM $this->stdCourse WHERE std_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $stdId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        }
    }

    public function enrollLectureCourse($lecrId, $courseId)
    {
        $query = "INSERT INTO $this->lecrCourse(lecr_id, course_id) VALUES(?,?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $lecrId, $courseId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function enrollStudentCourse($stdId, $courseId)
    {
        $query = "INSERT INTO $this->stdCourse(std_id, course_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $stdId, $courseId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    // TODO: fix Attendance status
    public function markAttendance($stdId, $classId, $attendTime, $status)
    {
        $query = "INSERT INTO $this->stdClass(std_id, class_id, attend_time, attendance_status) VALUES(?,?,?,?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iiss', $stdId, $classId, $attendTime, $status);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function editAttendance($stdId, $ClassId, $attendTime, $status)
    {
        $query = "UPDATE $this->stdClass SET attend_time = ?, attendance_status = ? WHERE std_id = ? AND class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssii', $attendTime, $status, $stdId, $ClassId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getCourseList($order = array())
    {
        $query = "SELECT * FROM $this->course ";
        if (isset($order['search'])) {
            $query .= "WHERE course_code LIKE '%" . $order['search'] . "%' OR course_name LIKE '%" . $order['search'] . "%' ";
        }
        if (isset($order['column'])) {
            $query .= "ORDER BY " . $order['column'] . " " . $order['order'] . " ";
        }
        if ($order['offset'] != -1) {
            $query .= "LIMIT " . $order['limit'] . " OFFSET " . $order['offset'];
        }
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }

    public function retrieveTotalClassCount($courseId){
        $query = "SELECT COUNT(*) FROM $this->class WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $courseId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }

}

?>