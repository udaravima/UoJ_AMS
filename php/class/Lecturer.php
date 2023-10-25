<?php
class Lecturer
{
    // Data Tables
    private $user = 'uoj_user';
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
        $query = "INSERT INTO {$this->class}(lecr_id, course_id, class_date, start_time, end_time) VALUES(?,?,?,?,?)";
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
        $query = "INSERT INTO {$this->course}(course_code, course_name) VALUES(?,?)";
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
        $query = "DELETE FROM {$this->course} WHERE course_id = ?";
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
        $query = "DELETE FROM {$this->class} WHERE class_id = ?";
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
        $query = "UPDATE {$this->class} SET class_date = ?, start_time = ?, end_time = ? WHERE class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssi', $classData['class_date'], $classData['start_time'], $classData['end_time'], $classId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateCourse($courseId, $courseCode, $courseName)
    {
        $query = "UPDATE {$this->course} SET course_code = ?, course_name = ? WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssi', $courseCode, $courseName, $courseId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function derollLecturerToCourse($lecrId, $courseId)
    {
        $query = "DELETE FROM {$this->lecrCourse} WHERE lecr_id = ? AND course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $lecrId, $courseId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function derollStudentToCourse($stdId, $courseId)
    {
        $query = "DELETE FROM {$this->stdCourse} WHERE std_id = ? AND course_id = ?";
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
        $query = "SELECT * FROM {$this->lecrCourse} INNER JOIN {$this->course} ON {$this->lecrCourse}.course_id = {$this->course}.course_id WHERE {$this->lecrCourse}.lecr_id = ?";
        if (isset($order['search'])) {
            $query .= " AND course_id LIKE '%" . $order['search'] . "%'";
        }
        if (isset($order['column'])) {
            $query .= " ORDER BY " . $order['column'] . " " . $order['order'];
        }
        if (isset($order['offset']) && $order['offset'] != -1) {
            $query .= " LIMIT " . $order['limit'] . " OFFSET " . $order['offset'];
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $lecrId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }

    public function getStudentCourseList($stdId, $order = array())
    {
        $query = "SELECT * FROM {$this->stdCourse} INNER JOIN {$this->course} ON {$this->stdCourse}.course_id = {$this->course}.course_id WHERE {$this->stdCourse}.std_id = ?";
        if (isset($order['search'])) {
            $query .= " AND course_id LIKE '%" . $order['search'] . "%'";
        }
        if (isset($order['column'])) {
            $query .= " ORDER BY " . $order['column'] . " " . $order['order'];
        }
        if (isset($order['offset']) && $order['offset'] != -1) {
            $query .= " LIMIT " . $order['limit'] . " OFFSET " . $order['offset'];
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $stdId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }

    public function getStudentsFromCourseId($courseId, $order = array())
    {
        $query = "SELECT {$this->user}.username , {$this->std}.std_fullname, {$this->std}.std_index , {$this->stdCourse}.std_id  FROM {$this->stdCourse} INNER JOIN {$this->std} ON {$this->stdCourse}.std_id = {$this->std}.std_id INNER JOIN {$this->user} ON {$this->std}.user_id = {$this->user}.user_id WHERE {$this->stdCourse}.course_id = ?";
        if (isset($order["column"])) {
            $query .= " ORDER BY " . $order['column'] . " " . $order['order'];
        }
        if (isset($order['offset']) && $order['offset'] != -1) {
            $query .= " LIMIT " . $order['limit'] . " OFFSET " . $order['offset'];
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $courseId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }

    public function getLecturersFromCourseId($courseId, $order = array())
    {
        $query = "SELECT {$this->user}.username , {$this->lecr}.lecr_name , {$this->lecrCourse}.lecr_id FROM {$this->lecrCourse} INNER JOIN {$this->lecr} ON {$this->lecrCourse}.lecr_id = {$this->lecr}.lecr_id INNER JOIN {$this->user} ON {$this->lecr}.user_id = {$this->user}.user_id WHERE {$this->lecrCourse}.course_id = ?";
        if (isset($order["column"])) {
            $query .= " ORDER BY " . $order['column'] . " " . $order['order'];
        }
        if (isset($order['offset']) && $order['offset'] != -1) {
            $query .= " LIMIT " . $order['limit'] . " OFFSET " . $order['offset'];
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $courseId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }

    public function enrollLectureToCourse($lecrId, $courseId)
    {
        if ($this->isCourseExist($courseId) && !($this->isLectureEnrolledToCourse($lecrId, $courseId))) {
            $query = "INSERT INTO {$this->lecrCourse}(lecr_id, course_id) VALUES(?,?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('ii', $lecrId, $courseId);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function enrollStudentToCourse($stdId, $courseId)
    {
        if ($this->isCourseExist($courseId) && !($this->isStudentEnrolledToCourse($stdId, $courseId))) {
            $query = "INSERT INTO {$this->stdCourse}(std_id, course_id) VALUES(?,?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('ii', $stdId, $courseId);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    // TODO: fix Attendance status
    public function markAttendance($stdId, $classId, $attendTime, $status)
    {
        $query = "INSERT INTO {$this->stdClass}(std_id, class_id, attend_time, attendance_status) VALUES(?,?,?,?)";
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
        $query = "UPDATE {$this->stdClass} SET attend_time = ?, attendance_status = ? WHERE std_id = ? AND class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssii', $attendTime, $status, $stdId, $ClassId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getCourseList($order)
    {
        $query = "SELECT * FROM {$this->course} ";

        if (isset($order['search'])) {
            $query .= "WHERE course_code LIKE ? OR course_name LIKE ?";
            $search = "%" . $order['search'] . "%";
        }
        if (isset($order['column'])) {
            $query .= " ORDER BY " . $order['column'] . " " . $order['order'];
        }
        if (isset($order['offset']) && $order['offset'] != -1) {
            $query .= " LIMIT " . $order['limit'] . " OFFSET " . $order['offset'];
        }
        $stmt = $this->conn->prepare($query);
        if (isset($search)) {
            $stmt->bind_param('ss', $search, $search);
        }
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }

    // retrieve count of classes for a course
    public function retrieveTotalClassCount($courseId)
    {
        $query = "SELECT COUNT(*) FROM {$this->class} WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $courseId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }

    public function retrieveTotalStudentCount($courseId)
    {
        $query = "SELECT COUNT(*) FROM {$this->stdCourse} WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $courseId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }

    // How many classes for the course
    public function retrieveTotalAttendanceCount($courseId)
    {
        $query = "SELECT COUNT(*) FROM {$this->stdClass} WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $courseId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }

    // How many students for the paticular class
    public function retrieveTotalAttendanceCountByClass($classId)
    {
        $query = "SELECT COUNT(*) FROM {$this->stdClass} WHERE class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $classId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }
    // How many classes student joined
    public function retrieveTotalAttendanceCountByStudent($stdId)
    {
        $query = "SELECT COUNT(*) FROM {$this->stdClass} WHERE std_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $stdId);
        if ($stmt->execute()) {
            return $stmt->get_result();
        } else {
            return false;
        }
    }

    // public function isPresentForTheClass($stdId, $classId)
    // {
    //     $query = "SELECT COUNT(*) FROM {$this->stdClass} WHERE std_id = ? AND class_id = ?";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bind_param('ii', $stdId, $classId);
    //     if ($stmt->execute()) {
    //         return $stmt->get_result();
    //     } else {
    //         return false;
    //     }
    // }

    public function isCourseExist($courseId)
    {
        $query = "SELECT * FROM {$this->course} WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $courseId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function retrieveCourseDetails($courseId)
    {
        $query = "SELECT * FROM {$this->course} WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $courseId);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_assoc();
        } else {
            return false;
        }
    }

    public function isClassExist($classId)
    {
        $query = "SELECT * FROM {$this->class} WHERE class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $classId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function retrieveClassDetails($classId)
    {
        $query = "SELECT * FROM {$this->class} WHERE class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $classId);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_assoc();
        } else {
            return false;
        }
    }

    public function isLectureEnrolledToCourse($lecrId, $courseId)
    {
        $query = "SELECT * FROM {$this->lecrCourse} WHERE lecr_id = ? AND course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $lecrId, $courseId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function isStudentEnrolledToCourse($stdId, $courseId)
    {
        $query = "SELECT * FROM {$this->stdCourse} WHERE std_id = ? AND course_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $stdId, $courseId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function isStudentEnrolledToClass($stdId, $classId)
    {
        $query = "SELECT * FROM {$this->stdClass} WHERE std_id = ? AND class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $stdId, $classId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function isCourseCodeAvailable($courseCode)
    {
        $query = "SELECT * FROM {$this->course} WHERE course_code = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $courseCode);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return false;
            } else {
                return true;
            }
        }
    }
}
// List of Functions available
//     public function createClass($lecrId, $courseId, $date, $sTime, $endTime)
//     public function createCourse($courseCode, $courseName)
//     public function deleteCourse($courseId)
//     public function deleteClass($classId)
//     public function updateClassInfo($classId, $classData)
//     public function updateCourse($courseId, $courseCode, $courseName)
//     public function derollLecturerCourse($lecrId, $courseId)
//     public function derollStudentCourse($stdId, $courseId)
//     public function getLecturerCourseList($lecrId, $order)
//     public function getStudentCourseList($stdId, $order = array())
//     public function enrollLectureCourse($lecrId, $courseId)
//     public function enrollStudentCourse($stdId, $courseId)
//     public function markAttendance($stdId, $classId, $attendTime, $status)
//     public function editAttendance($stdId, $ClassId, $attendTime, $status)
//     public function getCourseList($order)
//     public function retrieveTotalClassCount($courseId)
//     public function retrieveTotalStudentCount($courseId)
//     public function retrieveTotalAttendanceCount($courseId)
//     public function retrieveTotalAttendanceCountByClass($classId)
//     public function retrieveTotalAttendanceCountByStudent($stdId)
//     public function isPresentForTheClass($stdId, $classId)
//     public function isCourseExist($courseId)
//     public function retrieveCourseDetails($courseId)
//     public function isClassExist($classId)
//     public function retrieveClassDetails($classId)
//     public function isLectureEnrolledToCourse($lecrId, $courseId)
//     public function isStudentEnrolledToCourse($stdId, $courseId)
//     public function isStudentEnrolledToClass($stdId, $classId)
//     public function isCourseCodeAvailable($courseCode)
