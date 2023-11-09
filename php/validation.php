<?php
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';
include_once ROOT_PATH . '/php/class/Utils.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$lecr = new Lecturer($conn);
$utils = new Utils();

header('Content-Type: application/json');

// check username availability
if (isset($_POST['username'])) {
    $username = strtolower($_POST['username']);
    $usernameAvailable = $user->isUsernameAvailable($username);
    if ($usernameAvailable) {
        echo json_encode(['available' => true]);
    } else {
        echo json_encode(['available' => false]);
    }

    // retrieve user data
} else if (isset($_POST['uid'])) {
    $userId = $_POST['uid'];
    if ($user->isLoggedIn() && ($_SESSION["user_id"] == $userId || $user->isAdmin())) {
        if ($user->isUserIdExist($userId)) {
            $userDetails = $user->retrieveUserDetails($userId);
            $response = [
                'error' => false,
                'username' => $userDetails['username'],
                'user_role' => $userDetails['user_role'],
                'user_status' => $userDetails['user_status']
            ];
            if ($userDetails['user_role'] == 0 || $userDetails['user_role'] == 1 || $userDetails['user_role'] == 2) {
                $lecr_id = $user->getLectureIdByUserId($userId);
                $userCourses = $lecr->getLecturerCourseList($lecr_id, array());
                $response['lecr_name'] = $userDetails['lecr_name'];
                $response['lecr_id'] = $userDetails['lecr_id'];
                $response['lecr_mobile'] = $userDetails['lecr_mobile'];
                $response['lecr_email'] = $userDetails['lecr_email'];
                $response['lecr_address'] = $userDetails['lecr_address'];
                $response['lecr_nic'] = $userDetails['lecr_nic'];
                $response['lecr_gender'] = $userDetails['lecr_gender'];
                $response['lecr_profile_pic'] = $userDetails['lecr_profile_pic'];
                $response['courses'] = $userCourses->fetch_all();
            } else if ($userDetails['user_role'] == 3) {
                $std_id = $user->getStudentIdByUserId($userId);
                $userCourses = $lecr->getStudentCourseList($std_id, array());
                $response['std_index'] = $userDetails['std_index'];
                $response['std_shortname'] = $utils->processNameInitials($userDetails['std_fullname']);
                $response['std_fullname'] = $userDetails['std_fullname'];
                $response['mobile_tp_no'] = $userDetails['mobile_tp_no'];
                $response['home_tp_no'] = $userDetails['home_tp_no'];
                $response['std_email'] = $userDetails['std_email'];
                $response['current_address'] = $userDetails['current_address'];
                $response['permanent_address'] = $userDetails['permanent_address'];
                $response['std_nic'] = $userDetails['std_nic'];
                $response['std_dob'] = $userDetails['std_dob'];
                $response['std_gender'] = $userDetails['std_gender'];
                $response['std_profile_pic'] = $userDetails['std_profile_pic'];
                $response['std_batchno'] = $userDetails['std_batchno'];
                $response['std_id'] = $userDetails['std_id'];
                $response['date_admission'] = $userDetails['date_admission'];
                $response['current_level'] = $userDetails['current_level'];
                $response['courses'] = $userCourses->fetch_all();
            }

            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'User Id not found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid Privillages']);
    }

    // retrieve course data of a paticular course by logged in user
} else if (isset($_POST['cid'])) {
    $courseId = $_POST['cid'];
    if ($user->isLoggedIn()) {

        if ($lecr->isCourseExist($courseId)) {
            $courseDetails = $lecr->retrieveCourseDetails($courseId);
            $Instructors = $lecr->getInstructorForClass($courseId);
            $response = [
                'error' => false,
                'course_code' => $courseDetails['course_code'],
                'course_name' => $courseDetails['course_name'],
                'course_id' => $courseDetails['course_id'],
                'instructors' => $Instructors->fetch_all()
            ];
            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Course Id not found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid Privillages']);
    }

    // retrieve class data of a paticular class by logged in user
} else if (isset($_POST['clid'])) {
    $classId = $_POST['clid'];
    if ($user->isLoggedIn()) {
        if ($lecr->isClassExist($classId)) {
            if ($classDetails = $lecr->retrieveClassDetails($classId)) {
                $response = [
                    'error' => false,
                    'course_id' => $classDetails['course_id'],
                    'class_date' => $classDetails['class_date'],
                    'start_time' => $classDetails['start_time'],
                    'end_time' => $classDetails['end_time'],
                ];
                if ($courseDetails = $lecr->retrieveCourseDetails($classDetails['course_id'])) {
                    $response['class_name'] = $courseDetails['course_code'] . ' - ' . $courseDetails['course_name'];
                } else {
                    $response['class_name'] = 'Course not found';
                }
                if ($Instructors = $lecr->getInstructorForClass($classId)) {
                    $response['instructors'] = $Instructors->fetch_all();
                } else {
                    $response['instructors'] = [];
                }
                echo json_encode($response);
            } else {
                echo json_encode(['error' => 'Class retrieval failed']);
            }
        } else {
            echo json_encode(['error' => 'Class Id not found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid Privillages']);
        exit();
    }

    // checking whether the course code is available
} else if (isset($_POST['course_code'])) {
    if ($lecr->isCourseCodeAvailable($_POST['course_code'])) {
        echo json_encode(['available' => true]);
    } else {
        echo json_encode(['available' => false]);
    }
}

//  else if (isset($_POST['searchCourse'])) {
//     $searchCourse = $_POST['searchCourse'];
//     $order = array();
//     $itemsPerPage = 10; //10 items per page
//     $currentPage = isset($_POST['pageC']) ? $_POST['pageC'] : 1;
//     $order['offset'] = ($currentPage - 1) * $itemsPerPage;
//     $order['limit'] = $itemsPerPage;
//     $totalCount = $lecr->countRecords('uoj_course', $searchCourse);
//     $totalPages = ceil($totalCount / $itemsPerPage);
//     $courses = $lecr->getCourseList($order, $searchCourse);
//     $i = 1;
//     $response = [
//         'error' => false,
//         'courses' => $courses->fetch_all(),
//         'totalPages' => $totalPages,
//         'currentPage' => $currentPage
//     ];
//     echo json_encode($response);

// } else if (isset($_POST['searchClass'])) {
//     $searchClass = $_POST['searchClass'];
//     $order = array();
//     $itemsPerPage = 10; //10 items per page
//     $currentPage = isset($_POST['pageC']) ? $_POST['pageC'] : 1;
//     $order['offset'] = ($currentPage - 1) * $itemsPerPage;
//     $order['limit'] = $itemsPerPage;
//     $totalCount = $lecr->countRecords('uoj_class', $searchClass);
//     $totalPages = ceil($totalCount / $itemsPerPage);
//     $classes = $lecr->getClassList($order, $searchClass);
//     $i = 1;
//     $response = [
//         'error' => false,
//         'classes' => $classes->fetch_all(),
//         'totalPages' => $totalPages,
//         'currentPage' => $currentPage
//     ];
//     echo json_encode($response);

// } else if (isset($_POST['searchUser'])) {
//     $searchUser = $_POST['searchUser'];
//     $order = array();
//     $itemsPerPage = 10; //10 items per page
//     $currentPage = isset($_POST['pageU']) ? $_POST['pageU'] : 1;
//     $order['offset'] = ($currentPage - 1) * $itemsPerPage;
//     $order['limit'] = $itemsPerPage;
//     $totalCount = $user->countRecords('uoj_user', $searchUser);
//     $totalPages = ceil($totalCount / $itemsPerPage);
//     $users = $user->getUserList($order, $searchUser);
//     $i = 1;
//     $response = [
//         'error' => false,
//         'users' => $users->fetch_all(),
//         'totalPages' => $totalPages,
//         'currentPage' => $currentPage
//     ];
//     echo json_encode($response);

// } else if (isset($_POST['searchLecturer'])) {
//     $searchUser = $_POST['searchLecturer'];
//     $order = array();
//     $itemsPerPage = 10; //10 items per page
//     $currentPage = isset($_POST['pageU']) ? $_POST['pageU'] : 1;
//     $order['offset'] = ($currentPage - 1) * $itemsPerPage;
//     $order['limit'] = $itemsPerPage;
//     $totalCount = $user->countRecords('uoj_user', $searchUser);
//     $totalPages = ceil($totalCount / $itemsPerPage);
//     $users = $user->getLecturerList($order, $searchUser);
//     $i = 1;
//     $response = [
//         'error' => false,
//         'users' => $users->fetch_all(),
//         'totalPages' => $totalPages,
//         'currentPage' => $currentPage
//     ];
//     echo json_encode($response);

// } else if (isset($_POST['searchStudent'])) {
//     $searchUser = $_POST['
//     searchStudent'];
//     $order = array();
//     $itemsPerPage = 10; //10 items per page
//     $currentPage = isset($_POST['pageU']) ? $_POST['pageU'] : 1;
//     $order['offset'] = ($currentPage - 1) * $itemsPerPage;
//     $order['limit'] = $itemsPerPage;
//     $order[''] = $itemsPerPage;
//     $totalCount = $user->countRecords('uoj_user', $searchUser);
//     $totalPages = ceil($totalCount / $itemsPerPage);
//     $users = $user->getStudentList($order, $searchUser);
//     $i = 1;
//     $response = [
//         'error' => false,
//         'users' => $users->fetch_all(),
//         'totalPages' => $totalPages,
//         'currentPage' => $currentPage
//     ];
//     echo json_encode($response);

// } 
//retireving tables and data for paticular course
else if (isset($_POST['courseId'])) {
    if ($user->isAdmin()) {
        $courseId = $_POST['courseId'];
        $response = [];
        $errors = [];

        $courseInfo = $lecr->retrieveCourseDetails($courseId);
        $response['courseCode'] = $courseInfo['course_code'];
        $response['courseName'] = $courseInfo['course_name'];

        try {
            $studentRecs = $lecr->getStudentsFromCourseId($courseId);
            $response['students'] = $studentRecs->fetch_all();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $errors[] = "Error in retrieving students";
        }

        try {
            $lecturerRecs = $lecr->getLecturersFromCourseId($courseId);
            $response['lecturers'] = $lecturerRecs->fetch_all();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $errors[] = "Error in retrieving lecturers";
        }

        $response['errors'] = $errors;
        $response['error'] = false;
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Invalid Privillages']);
        exit();
    }
}

// making a course search by logged in user
else if (isset($_POST['cids'])) {
    if ($user->isLoggedIn()) {
        $search = $_POST['cids'];
        $order['search'] = $search;
        $courses = $lecr->getCourseList($order);
        $response = [
            'error' => false,
            'courses' => $courses->fetch_all()
        ];
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Invalid Privillages']);
        exit();
    }


    // Update User Course list by admin 
} else if (isset($_POST['userid']) && $user->isAdmin()) {
    $errors = [];
    $messages = [];
    $addCourseList = (isset($_POST['addCourseList'])) ? $_POST['addCourseList'] : array();
    $removeCourseList = (isset($_POST['removeCourseList'])) ? $_POST['removeCourseList'] : array();
    $userId = $_POST['userid'];
    $userRole = $user->retrieveUserRole($userId);

    if ($userRole == 0 || $userRole == 1 || $userRole == 2) {
        $lecr_id = $user->getLectureIdByUserId($userId);
        foreach ($addCourseList as $courseId) {
            if ($lecr->isCourseExist($courseId)) {
                if ($lecr->enrollLectureToCourse($lecr_id, $courseId)) {
                    $messages[] = "Course Id: " . $courseId . " added successfully";
                } else {
                    $errors[] = "Course Id: " . $courseId . " already exist";
                }
            } else {
                $errors[] = "Course Id: " . $courseId . " not found";
            }
        }
        foreach ($removeCourseList as $courseId) {
            if ($lecr->isCourseExist($courseId)) {
                if ($lecr->derollLecturerToCourse($lecr_id, $courseId)) {
                    $messages[] = "Course Id: " . $courseId . " removed successfully";
                } else {
                    $errors[] = "Course Id: " . $courseId . " not found";
                }
            } else {
                $errors[] = "Course Id: " . $courseId . " not found";
            }
        }
    } else if ($userRole == 3) {
        $std_id = $user->getStudentIdByUserId($userId);
        foreach ($addCourseList as $courseId) {
            if ($lecr->isCourseExist($courseId)) {
                if ($lecr->enrollStudentToCourse($std_id, $courseId)) {
                    $messages[] = "Course Id: " . $courseId . " added successfully";
                } else {
                    $errors[] = "Course Id: " . $courseId . " already exist";
                }
            } else {
                $errors[] = "Course Id: " . $courseId . " not found";
            }
        }
        foreach ($removeCourseList as $courseId) {
            if ($lecr->isCourseExist($courseId)) {
                if ($lecr->derollStudentToCourse($std_id, $courseId)) {
                    $messages[] = "Course Id: " . $courseId . " removed successfully";
                } else {
                    $errors[] = "Course Id: " . $courseId . " not found";
                }
            } else {
                $errors[] = "Course Id: " . $courseId . " not found";
            }
        }
    }
    $response = [
        'error' => false,
        'messages' => $messages,
        'errors' => $errors,
        'success' => true
    ];

    echo json_encode($response);

    // Update User Course list by admin
} else if (isset($_POST["userSearch"]) && $user->isAdmin()) {
    $errors = [];
    $messages = [];
    $search = $_POST["userSearch"];
    $order = array();
    $response = [];
    $order['search'] = $search;
    try {
        $lectures = $user->getLecturerTable($order);
        $response['lecturers'] = $lectures->fetch_all();
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        $errors[] = "Error in retrieving lecturers";
        $response['lecturers'] = [];
    }
    try {
        $students = $user->getStudentTable($order);
        $response['students'] = $students->fetch_all();
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        $errors[] = "Error in retrieving students";
        $response['students'] = [];
    }
    $response['errors'] = $errors;
    $response['error'] = false;
    echo json_encode($response);
    //add user to course by admin
} else if (isset($_POST['courseUserId'])) {
    $errors = [];
    $messages = [];
    $courseId = $_POST['courseUserId'];
    $addStudentList = (isset($_POST['addStudentList'])) ? $_POST['addStudentList'] : array();
    $removeStudentList = (isset($_POST['removeStudentList'])) ? $_POST['removeStudentList'] : array();
    $addLectureList = (isset($_POST['addLectureList'])) ? $_POST['addLectureList'] : array();
    $removeLectureList = (isset($_POST['removeLectureList'])) ? $_POST['removeLectureList'] : array();
    if ($lecr->isCourseIdExist($courseId)) {
        foreach ($addStudentList as $studentId) {
            if ($user->isStudentIdExist($studentId)) {
                if ($lecr->enrollStudentToCourse($studentId, $courseId)) {
                    $messages[] = "Student Id: " . $studentId . " added successfully";
                } else {
                    $errors[] = "Student Id: " . $studentId . " already exist";
                }
            } else {
                $errors[] = "Student Id: " . $studentId . " not found";
            }
        }
        foreach ($removeStudentList as $studentId) {
            if ($user->isStudentIdExist($studentId)) {
                if ($lecr->derollStudentToCourse($studentId, $courseId)) {
                    $messages[] = "Student Id: " . $studentId . " removed successfully";
                } else {
                    $errors[] = "Student Id: " . $studentId . " not found";
                }
            } else {
                $errors[] = "Student Id: " . $studentId . " not found";
            }
        }
        foreach ($addLectureList as $lectureId) {
            if ($user->isLectureIdExist($lectureId)) {
                if ($lecr->enrollLectureToCourse($lectureId, $courseId)) {
                    $messages[] = "Lecture Id: " . $lectureId . " added successfully";
                } else {
                    $errors[] = "Lecture Id: " . $lectureId . " already exist";
                }
            } else {
                $errors[] = "Lecture Id: " . $lectureId . " not found";
            }
        }
        foreach ($removeLectureList as $lectureId) {
            if ($user->isLectureIdExist($lectureId)) {
                if ($lecr->derollLecturerToCourse($lectureId, $courseId)) {
                    $messages[] = "Lecture Id: " . $lectureId . " removed successfully";
                } else {
                    $errors[] = "Lecture Id: " . $lectureId . " not found";
                }
            } else {
                $errors[] = "Lecture Id: " . $lectureId . " not found";
            }
        }
    } else {
        $errors[] = "Course Id: " . $courseId . " not found";
    }
    $response = [
        'error' => false,
        'messages' => $messages,
        'errors' => $errors,
        'success' => true
    ];
    echo json_encode($response);
} else {
    header("Location: " . SERVER_ROOT . "/index.php");
    exit();
}
// } else {
//     header("Location: " . SERVER_ROOT . "/index.php");
// }
