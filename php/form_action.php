<?php
//TODO: USE REGISTRATION NUMBER AS USERNAME AND REMOVE REGNO FOR BOTH LECTURER AND STUDENT
// require_once $_SERVER['DOCUMENT_ROOT'] . '/MyAttendanceSys/config.php';
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Utils.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$lecr = new Lecturer($db);
$util = new Utils();
$errMessage;
$goMessage;

// if (!($user->isLoggedIn())) {
//     header("Location: " . SERVER_ROOT . "/index.php");
// }

include ROOT_PATH . '/php/include/header.php';
echo "<title>AMS Registration</title>";
include ROOT_PATH . '/php/include/content.php';

if (isset($_POST['submit_course'])) {
    if (isset($_POST['course_code']) && isset($_POST['course_name'])) {
        $courseCode = $_POST['course_code'];
        $courseName = $_POST['course_name'];
        if ($lecr->createCourse($courseCode, $courseName)) {
            $goMessage = "Course Created Successfully";
        } else {
            $errMessage = "Course Creation Failed";
        }
    } else {
        $errMessage = "Course Validation Faild";
    }
}

if (isset($_POST['submit_class'])) {
    if (isset($_POST['course_id']) && isset($_POST['lecr_id']) && isset($_POST['class_date']) && isset($_POST['start_time']) && isset($_POST['end_time'])) {
        $courseId = $_POST['course_id'];
        $lecrId = $_POST['lecr_id'];
        $classDate = $_POST['class_date'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        if ($lecr->createClass($lecrId, $courseId, $classDate, $startTime, $endTime)) {
            $goMessage = "Class Created Successfully";
        } else {
            $errMessage = "Class Creation Failed";
        }
    } else {
        $errMessage = "Class Validation Faild";
    }
}

if (isset($_POST['register'])) {
    //to Store userData
    $userData = array();
    $username = '';
    $password = '';
    $user_role = '';
    $user_status = null;

    if (isset($_POST['username']) && !empty($_POST['username'])) {
        $username = $_POST['username'];
    }

    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = $_POST['password'];
    }

    if (isset($_POST['user_role']) && !empty($_POST['user_role'])) {
        $user_role = $_POST['user_role'];
    }
    if (isset($_POST['user_status'])) {
        $user_status = $_POST['user_status'];
    }

    // To lecture data --> userData
    if ($user_role == 0 || $user_role == 1 || $user_role == 2) {
        if (isset($_POST['lecr_nic']) && !empty($_POST['lecr_nic'])) {
            $userData['lecr_nic'] = $_POST['lecr_nic'];
        }
        if (isset($_POST['lecr_name']) && !empty($_POST['lecr_name'])) {
            $userData['lecr_name'] = $_POST['lecr_name'];
        }
        if (isset($_POST['lecr_email']) && !empty($_POST['lecr_email'])) {
            $userData['lecr_email'] = $_POST['lecr_email'];
        }
        if (isset($_POST['lecr_mobile']) && !empty($_POST['lecr_mobile'])) {
            $userData['lecr_mobile'] = $_POST['lecr_mobile'];
            //TODO: validate for number characters !! too much fixing for one go
        }
        if (isset($_POST['lecr_address'])) {
            $userData['lecr_address'] = $_POST['lecr_address'];
        }
        if (isset($_POST['lecr_gender'])) {
            $userData['lecr_gender'] = $_POST['lecr_gender'];
        }
        if (isset($_FILES["lecr_profile_pic"])) {
            //TODO: get and upload photo
            $picLocation = $util->storeProfilePic(ROOT_PATH . '/res/profiles/lecturer/', 'lecr_profile_pic', (isset($_POST['lecr_nic'])) ? $userData['lecr_nic'] : '');
            if ($picLocation != null && $picLocation) {
                $userData['lecr_profile_pic'] = SERVER_ROOT . "/res/profiles/lecturer/" . basename($picLocation);
            } else {
                $userData['lecr_profile_pic'] = SERVER_ROOT . '/res/profiles/lecturer/default.png';
                $message = "Error Uploading Profile Picture";
            }
        }

        // To Student data --> userData

    } else if ($user_role == 3) {
        if (isset($_POST['std_index']) && !empty($_POST['std_index'])) {
            $userData['std_index'] = $_POST['std_index'];
        }
        if (isset($_POST['std_regno']) && !empty($_POST['std_regno'])) {
            $userData['std_regno'] = $_POST['std_regno'];
        }
        if (isset($_POST['std_fullname']) && !empty($_POST['std_fullname'])) {
            $userData['std_fullname'] = $_POST['std_fullname'];
        }
        if (isset($_POST['std_gender'])) {
            $userData['std_gender'] = $_POST['std_gender'];
        }
        if (isset($_POST['std_address'])) {
            $userData['std_address'] = $_POST['std_address'];
        }
        if (isset($_POST['std_batchno'])) {
            $userData['std_batchno'] = $_POST['std_batchno'];
        }
        if (isset($_POST['dgree_program'])) {
            $userData['dgree_program'] = $_POST['dgree_program'];
        }
        if (isset($_POST['std_subjectcomb'])) {
            $userData['std_subjectcomb'] = $_POST['std_subjectcomb'];
        }
        if (isset($_POST['std_nic']) && !empty($_POST['std_nic'])) {
            $userData['std_nic'] = $_POST['std_nic'];
        }
        if (isset($_POST['std_dob'])) {
            $userData['std_dob'] = $_POST['std_dob'];
        }
        if (isset($_POST['date_admission'])) {
            $userData['date_admission'] = $_POST['date_admission'];
        }
        if (isset($_POST['current_address'])) {
            $userData['current_address'] = $_POST['current_address'];
        }
        if (isset($_POST['permanent_address'])) {
            $userData['permanent_address'] = $_POST['permanent_address'];
        }
        if (isset($_POST['mobile_tp_no']) && !empty($_POST['mobile_tp_no'])) {
            $userData['mobile_tp_no'] = $_POST['mobile_tp_no'];
        }
        if (isset($_POST['home_tp_no'])) {
            $userData['home_tp_no'] = $_POST['home_tp_no'];
        }
        if (isset($_POST['std_email'])) {
            $userData['std_email'] = $_POST['std_email'];
        }
        if (isset($_POST['current_level'])) {
            $userData['current_level'] = $_POST['current_level'];
        }
        if (isset($_FILES["std_profile_pic"]) && $_FILES["std_profile_pic"]["error"] == 0) {
            //TODO: get and upload photo
            $picLocation = $util->storeProfilePic(ROOT_PATH . '/res/profiles/student/', 'std_profile_pic', $_POST['std_nic']);
            if ($picLocation != null && $picLocation) {
                $userData['std_profile_pic'] = SERVER_ROOT . "/res/profiles/student/" . basename($picLocation);

            } else {
                echo "<script>alert('Picture error')</script>"; // for debugging only
                $userData['std_profile_pic'] = SERVER_ROOT . '/res/profiles/lecturer/default.png';
                $message = "Error Uploading Profile Picture";
            }
        }
    }

    if ($user->registerUser($username, $password, $user_role, $userData, $user_status)) {
        $message = "User Registration successfull";
    } else {
        $message = "User Registration Failed";
    }

}

if ($message) {
    echo $util->setMessage($message, 'alert', 'warning');
    echo "<script>alert();</script>";
}

echo "<script>window.location.href = " . SERVER_ROOT . "'/index.php';</script>";

include ROOT_PATH . '/php/include/footer.php';
?>