<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/MyAttendanceSys/config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Utils.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$util = new Utils();

include ROOT_PATH . '/php/include/header.php';
echo "<title>AMS Registration</title>";
include ROOT_PATH . '/php/include/content.php';

if (isset($_POST['register'])) {
    //to Store userData
    $userData = array();
    $username = '';
    $password = '';
    $user_role = '';
    $user_status = null;
    $validation = true;

    if (isset($_POST['username']) && !empty($_POST['username'])) {
        $username = $_POST['username'];
    } else {
        $validation = false;
    }
    if (isset($_POST['user_password']) && !empty($_POST['user_password'])) {
        $password = $_POST['user_password'];
    } else {
        $validation = false;
    }
    if (isset($_POST['user_role']) && !empty($_POST['user_role'])) {
        $user_role = intval($_POST['user_role']);
    } else {
        $validation = false;
    }
    if (isset($_POST['user_status'])) {
        $user_status = intval($_POST['user_status']);
    }

    // To lecture data --> userData
    if ($user_role == 0 || $user_role == 1 || $user_role == 2) {
        if (isset($_POST['lecr_nic']) && !empty($_POST['lecr_nic'])) {
            $userData['lecr_nic'] = $_POST['lecr_nic'];
        } else {
            $validation = false;
        }
        if (isset($_POST['lecr_name']) && !empty($_POST['lecr_name'])) {
            $userData['lecr_name'] = $_POST['lecr_name'];
        } else {
            $validation = false;
        }
        if (isset($_POST['lecr_email']) && !empty($_POST['lecr_email'])) {
            $userData['lecr_email'] = $_POST['lecr_email'];
        } else {
            $validation = false;
        }
        if (isset($_POST['lecr_mobile']) && !empty($_POST['lecr_mobile'])) {
            $userData['lecr_mobile'] = $_POST['lecr_mobile'];
        } else {
            $validation = false;
        }
        if (isset($_POST['lecr_address'])) {
            $userData['lecr_address'] = $_POST['lecr_address'];
        }
        if (isset($_POST['lecr_gender'])) {
            $userData['lecr_gender'] = intval($_POST['lecr_gender']);
        }
        if (isset($_FILES["lecr_profile_pic"])) {
            //TODO: get and upload photo
            $picLocation = $util->storeProfilePic(SERVER_ROOT . '/res/profiles/lecturer/', 'lecr_profile_pic', (isset($_POST['lecr_nic'])) ? $userData['lecr_nic'] : '');
            echo $picLocation . "<br>";
            if ($picLocation != null && $picLocation) {
                $userData['lecr_profile_pic'] = $picLocation;
                echo $userData['lecr_profile_pic'];
            } else {
                $userData['lecr_profile_pic'] = SERVER_ROOT . '/res/profiles/lecturer/default.png';
                echo $util->setMessage("Error Uploading Profile Picture", 'alert', 'alert-danger');
            }
        }
        // To Student data --> userData
    } else if ($user_role == 3) {
        if (isset($_POST['std_index']) && !empty($_POST['std_index'])) {
            $userData['std_index'] = $_POST['std_index'];
        } else {
            $validation = false;
        }
        if (isset($_POST['std_regno']) && !empty($_POST['std_regno'])) {
            $userData['std_regno'] = $_POST['std_regno'];
        } else {
            $validation = false;
        }
        if (isset($_POST['std_fullname']) && !empty($_POST['std_fullname'])) {
            $userData['std_fullname'] = $_POST['std_fullname'];
        } else {
            $validation = false;
        }
        if (isset($_POST['std_gender'])) {
            $userData['std_gender'] = intval($_POST['std_gender']);
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
        } else {
            $validation = false;
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
        } else {
            $validation = false;
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
            $picLocation = $util->storeProfilePic(SERVER_ROOT . '/res/profiles/student/', 'std_profile_pic', (isset($_POST['std_nic'])) ? $userData['std_nic'] : '');
            if ($picLocation != null && $picLocation) {
                $userData['std_profile_pic'] = $picLocation;

            } else {
                $userData['std_profile_pic'] = SERVER_ROOT . '/res/profiles/lecturer/default.png';
                echo $util->setMessage("Error Uploading Profile Picture", 'alert', 'alert-danger');
            }
        }
    }
    if (!$validation) {
        echo $util->setMessage("Error in Data Validation", 'alert', 'alert-danger');

    } else if ($user->registerUser($username, $password, $user_role, $userData, $user_status)) {
        echo $util->setMessage("User Registration successfull", 'alert', 'success');
        // if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 0) {
        //     // header("Location: /php/admin_dashboard.php");
        //     echo "<script>window.location.href = ".SERVER_ROOT."'/php/admin_dashboard.php';</script>";

        // } else {
        //     // header("Location: index.php");
        //     echo "<script>window.location.href = ".SERVER_ROOT."'/index.php';</script>";
        // }

    } else {
        echo $util->setMessage("User Registration Failed", 'alert', 'danger');
        // if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 0) {
        //     // header("Location: admin_dashboard.php");
        //     echo "<script>window.location.href = ".SERVER_ROOT."'/php/admin_dashboard.php';</script>";
        // } else {
        //     echo "<script>window.location.href = ".SERVER_ROOT."'/index.php';</script>";
        //     // header("Location: index.php");
        // }
    }
}
// if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] == 0) {
//     echo "<script>window.location.href = ".SERVER_ROOT."'/php/admin_dashboard.php';</script>";

// } else {
//     // header("Location: index.php");
//     echo "<script>window.location.href = ".SERVER_ROOT."'/index.php';</script>";
// }
include ROOT_PATH . '/php/include/footer.php';
?>