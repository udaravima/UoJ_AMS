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

$errors = [];
$goMessage = [];

// if (!($user->isLoggedIn())) {
//     header("Location: " . SERVER_ROOT . "/index.php");
// }

// ---------- course Registration ---------------
if (isset($_POST['submit_course'])) {

    if (isset($_POST['course_code']) && isset($_POST['course_name'])) {
        $courseCode = $_POST['course_code'];
        $courseName = $_POST['course_name'];
        if ($lecr->createCourse($courseCode, $courseName)) {
            $goMessage[] = "Course Created Successfully";
        } else {
            $errors[] = "Course Creation Failed";
        }
    } else {
        $errors[] = "Course Validation Faild";
    }
}

// ----------- class Registration ------------ 
else if (isset($_POST['submit_class'])) {
    if (isset($_POST['course_id']) && isset($_POST['lecr_id']) && isset($_POST['class_date']) && isset($_POST['start_time']) && isset($_POST['end_time'])) {
        $courseId = $_POST['course_id'];
        $lecrId = $_POST['lecr_id'];
        $classDate = $_POST['class_date'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        if ($lecr->createClass($lecrId, $courseId, $classDate, $startTime, $endTime)) {
            $goMessage[] = "Class Created Successfully";
        } else {
            $errors[] = "Class Creation Failed";
        }
    } else {
        $errors[] = "Class Validation Faild";
    }
}

// User Registration Form Data Processing 
else if (isset($_POST['register'])) {
    //to Store userData
    $userData = array();
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_role = $_POST['user_role'];
    if ($user->isUsernameAvailable($username)) {
        //Username
        if (empty($username) || !preg_match("/^[a-zA-Z0-9]{5,}$/", $username)) {
            $errors[] = "username is required and should contain only alphanumeric values and minimum 5 characters.";
        }
        // password
        if (empty($password)) {
            $errors[] = "Password is required.";
        }
        // user_role
        if (!empty($_POST['user_role'] && preg_match("/^[0-4]{1}$/", $_POST['user_role']))) {
            if ($_POST['user_role'] == 0) {
                if ($user->isAdmin()) {
                    $user_role = $_POST['user_role'];
                } else {
                    $errors[] = "User Operation not permited!";
                }
            } else {
                $user_role = $_POST['user_role'];
            }
        } else {
            $errors[] = "User Role format invalid or empty.";
        }
        //user_status
        if (!empty($_POST['user_status']) && $user->isAdmin()) {
            if (preg_match("/^[0-4]{1}$/", $_POST['user_status'])) {
                $user_status = $_POST['user_status'];
            } else {
                $errors[] = "Status format invalid!";
            }
        } else {
            $user_status = null;
        }

        // --------------- To lecture data --> userData -------------------------
        if ($user_role == 0 || $user_role == 1 || $user_role == 2) {
            if (!empty($_POST['lecr_nic']) && preg_match("/^\d{9}(V|v)?$|^(\d{12})$/", $_POST['lecr_nic'])) {
                $userData['lecr_nic'] = $_POST['lecr_nic'];
            } else {
                $errors[] = "NIC is required and should contain only 9 digits and V or 12 digits.";
            }
            if (!empty($_POST['lecr_name']) && preg_match("/^[a-zA-Z ]*$/", $_POST['lecr_name'])) {
                $userData['lecr_name'] = $_POST['lecr_name'];
            } else {
                $errors[] = "Name is required and should contain only letters and whitespaces.";
            }
            if (!empty($_POST['lecr_email']) && filter_var($_POST['lecr_email'], FILTER_VALIDATE_EMAIL)) {
                $userData['lecr_email'] = $_POST['lecr_email'];
            } else {
                $errors[] = "Email is required and should be valid.";
            }
            if (!empty($_POST['lecr_mobile']) && preg_match("/^\d{10}$/", $_POST['lecr_mobile'])) {
                $userData['lecr_mobile'] = $_POST['lecr_mobile'];
            } else {
                $errors[] = "Mobile number is required and should contain only 10 digits.";
            }
            if (!empty($_POST['lecr_address'])) {
                if (preg_match("/^[a-zA-Z0-9\/, ]+$/", $_POST['lecr_address'])) {
                    $userData['lecr_address'] = $_POST['lecr_address'];
                } else {
                    $errors[] = "Address is required and should contain only letters, numbers, spaces and / or ,";
                }
            }
            if (!empty($_POST['lecr_gender'])) {
                if (preg_match("/^[0-2]{1}$/", $_POST['lecr_gender'])) {
                    $userData['lecr_gender'] = $_POST['lecr_gender'];
                } else {
                    $errors[] = "Gender Invalid!";
                }
            }
            if (is_uploaded_file($_FILES["lecr_profile_pic"]["tmp_name"]) && !empty($_POST['lecr_nic'])) {
                //TODO: get and upload photo
                $picLocation = $util->storeProfilePic(ROOT_PATH . '/res/profiles/lecturer/', 'lecr_profile_pic', $_POST['lecr_nic']);
                if ($picLocation != null && $picLocation) {
                    $userData['lecr_profile_pic'] = SERVER_ROOT . "/res/profiles/lecturer/" . basename($picLocation);
                } else {
                    $userData['lecr_profile_pic'] = SERVER_ROOT . '/res/profiles/lecturer/default.png';
                    $errors[] = "Error Uploading Profile Picture";
                }
            }

            //  ----------------------------- To Student data --> userData --------------------------------
        } else if ($user_role == 3) {
            if (!empty($_POST['std_index']) && preg_match("/^S\s\d{5}$/", $_POST['std_index'])) {
                $userData['std_index'] = $_POST['std_index'];
            } else {
                $errors[] = "Index is not set or Invalid Index number format.";
            }

            if (!empty($_POST['std_fullname'])) {
                if (preg_match("/^[A-Za-z ]*$/", $_POST['std_fullname'])) {
                    $userData['std_fullname'] = $_POST['std_fullname'];
                } else {
                    $errors[] = "Name should contain only letters and whitespaces.";
                }
            }
            if (!empty($_POST['std_gender'])) {
                if (preg_match("/^[0-2]{1}$/", $_POST['std_gender'])) {
                    $userData['std_gender'] = $_POST['std_gender'];
                } else {
                    $errors[] = "Gender format invalid";
                }
            }
            if (!empty($_POST['std_address'])) {
                if (preg_match("/^[a-zA-Z0-9\/, ]+$/", $_POST['std_address'])) {
                    $userData['std_address'] = $_POST['std_address'];
                } else {
                    $errors[] = "Address is required and should contain only letters, numbers, spaces and / or ,";
                }
            }
            if (!empty($_POST['std_batchno'])) {
                $userData['std_batchno'] = $_POST['std_batchno'];
            }
            if (!empty($_POST['std_nic']) && preg_match("/^\d{9}(V|v)?$|^(\d{12})$/", $_POST['std_nic'])) {
                $userData['std_nic'] = $_POST['std_nic'];
            } else {
                $errors[] = "NIC is required and should contain only 9 digits and V or 12 digits.";
            }

            if (!empty($_POST['std_dob'])) {
                if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $_POST['std_dob'])) {
                    $userData['std_dob'] = $_POST['std_dob'];
                } else {
                    $errors[] = "Date of Birth format invalid";
                }
            }
            if (!empty($_POST['date_admission'])) {
                if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $_POST['date_addmission'])) {
                    $userData['date_admission'] = $_POST['date_admission'];
                } else {
                    $errors[] = "Date of Admission format invalid";
                }
            }
            if (!empty($_POST['current_address'])) {
                if (preg_match("/^[a-zA-Z0-9\/, ]+$/", $_POST['current_address'])) {
                    $userData['current_address'] = $_POST['current_address'];
                } else {
                    $errors[] = "Address should contain only letters, numbers, spaces and / or ,";
                }
            }
            if (!empty($_POST['permanent_address'])) {
                if (preg_match("/^[a-zA-Z0-9\/, ]+$/", $_POST['permanent_address'])) {
                    $userData['permanent_address'] = $_POST['permanent_address'];
                } else {
                    $errors[] = "Address should contain only letters, numbers, spaces and / or ,";
                }
            }
            if (!empty($_POST['mobile_tp_no']) && preg_match("/^\d{10}$/", $_POST['mobile_tp_no'])) {
                $userData['mobile_tp_no'] = $_POST['mobile_tp_no'];
            } else {
                $errors[] = "Mobile number is required and should contain only 10 digits.";
            }
            if (!empty($_POST['home_tp_no'])) {
                if (preg_match("/^\d{10}$/", $_POST['home_tp_no'])) {
                    $userData['home_tp_no'] = $_POST['home_tp_no'];
                } else {
                    $errors[] = "Home number is required and should contain only 10 digits.";
                }
            }
            if (!empty($_POST['std_email']) && filter_var($_POST['std_email'], FILTER_VALIDATE_EMAIL)) {
                $userData['std_email'] = $_POST['std_email'];
            } else {
                $errors[] = "Email is required and should be valid.";
            }
            if (!empty($_POST['current_level'])) {
                $userData['current_level'] = $_POST['current_level'];
            }
            // if (isset($_FILES["std_profile_pic"]) && $_FILES["std_profile_pic"]["error"] == 0) {
            if (is_uploaded_file($_FILES["std_profile_pic"]["tmp_name"]) && !empty($_POST['std_nic'])) {
                //TODO: get and upload photo
                $picLocation = $util->storeProfilePic(ROOT_PATH . '/res/profiles/student/', 'std_profile_pic', $_POST['std_nic']);
                if ($picLocation != null && $picLocation) {
                    $userData['std_profile_pic'] = SERVER_ROOT . "/res/profiles/student/" . basename($picLocation);

                } else {
                    $userData['std_profile_pic'] = SERVER_ROOT . '/res/profiles/lecturer/default.png';
                    $errors[] = "Error Uploading Profile Picture";
                }
            }
        }
        if (empty($errors)) {
            if ($user->registerUser($username, $password, $user_role, $userData, $user_status)) {
                $goMessage[] = "User Registration successfull";
            } else {
                $errors[] = "User Registration Failed";
            }
        }
    } else {
        $errors[] = "Username already exists";
    }

} else {
    header("Location: " . SERVER_ROOT . "/index.php");
}

include ROOT_PATH . '/php/include/header.php';
echo "<title>AMS Registration</title>";
include ROOT_PATH . '/php/include/content.php';

?>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#amsForm">
    Launch static backdrop modal
</button>

<!-- Modal -->
<div class="modal fade" id="amsForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="amsFormLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="amsFormLabel">AMS System</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php

                if (!empty($goMessage)) {
                    echo "
                    <div class='alert alert-success'>
                        <p>
                            <ul>";
                    foreach ($goMessage as $msg) {
                        echo "<li>$msg</li>";
                    }
                    echo "
                            </ul>
                        </p>
                    </div>";
                }

                if (!empty($errors)) {
                    echo "
                    <div class='alert alert-danger'>
                        <p>
                            <ul>";
                    foreach ($errors as $error) {
                        echo "<li>$error</li>";
                    }
                    echo "
                            </ul>
                        </p>
                    </div>";
                } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var loginModal = new bootstrap.Modal(document.getElementById('amsForm'));
        loginModal.show();
    });
    // Returning to login
    var amsForm = document.getElementById('amsForm');
    amsForm.addEventListener('hidden.bs.modal', function () {
        window.location.href = '<?php echo SERVER_ROOT; ?>/index.php';
    });
</script>

<?php
include ROOT_PATH . '/php/include/footer.php';
?>