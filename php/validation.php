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
            // echo json_encode(['user_photo' => $userDetails['user_photo']]);
            if ($userDetails['user_role'] == 0 || $userDetails['user_role'] == 1 || $userDetails['user_role'] == 2) {
                $userCourses = $lecr->getLecturerCourseList($userId, array());
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
                $userCourses = $lecr->getStudentCourseList($userId, array());
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

} else if (isset($_POST['cid'])) {
    $courseId = $_POST['cid'];
    if ($user->isLoggedIn() && ($user->isLecturer() || $user->isAdmin())) {
        if ($lecr->isCourseExist($courseId)) {
            $courseDetails = $lecr->retrieveCourseDetails($courseId);
            $response = [
                'error' => false,
                'course_code' => $courseDetails['course_code'],
                'course_name' => $courseDetails['course_name'],
                'course_id' => $courseDetails['course_id']
            ];
            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Course Id not found']);
        }

    } else {
        echo json_encode(['error' => 'Invalid Privillages']);
    }

} else if (isset($_POST['clid'])) {
    $classId = $_POST['clid'];
    if ($user->isLoggedIn() && ($user->isLecturer() || $user->isAdmin())) {
        if ($lecr->isClassExist($classId)) {
            $classDetails = $lecr->retrieveClassDetails($classId);
            $response = [
                'error' => false,
                'class_id' => $classDetails['class_id'],
                'class_name' => $classDetails['class_name'],
                'class_type' => $classDetails['class_type'],
                'class_date' => $classDetails['class_date'],
                'class_time' => $classDetails['class_time'],
                'class_duration' => $classDetails['class_duration'],
                'class_venue' => $classDetails['class_venue'],
                'class_status' => $classDetails['class_status'],
                'course_id' => $classDetails['course_id']
            ];
            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Class Id not found']);
        }

    } else {
        echo json_encode(['error' => 'Invalid Privillages']);
    }

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
else if (isset($_POST['cids'])) {
    $search = $_POST['cids'];
    $order['search'] = $search;
    $courses = $lecr->getCourseList($order);
    $response = [
        'error' => false,
        'courses' => $courses->fetch_all()
    ];
    echo json_encode($response);
} else {
    header("Location: " . SERVER_ROOT . "/index.php");
}
?>