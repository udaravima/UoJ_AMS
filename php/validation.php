<?php
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$lecr = new Lecturer($conn);

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

                $response['lecr_name'] = $userDetails['lecr_name'];
                $response['lecr_id'] = $userDetails['lecr_id'];
                $response['lecr_mobile'] = $userDetails['lecr_mobile'];
                $response['lecr_email'] = $userDetails['lecr_email'];
                $response['lecr_address'] = $userDetails['lecr_address'];
                $response['lecr_nic'] = $userDetails['lecr_nic'];
                $response['lecr_gender'] = $userDetails['lecr_gender'];
                $response['lecr_profile_pic'] = $userDetails['lecr_profile_pic'];

            } else if ($userDetails['user_role'] == 3) {
                $response['std_index'] = $userDetails['std_index'];
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
            }

            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'User Id not found']);
        }

    } else {
        echo json_encode(['error' => 'Invalid Privillages']);
    }

} else {
    echo json_encode(['error' => 'Invalid Request']);
}

// } else {
//     header("Location: " . SERVER_ROOT . "/index.php");
// }
?>