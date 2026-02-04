<?php
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Utils.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';
include_once ROOT_PATH . '/php/class/CSRF.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$lecr = new Lecturer($db);
$util = new Utils();

// Validate CSRF token for all POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::requireValidToken(false); // Don't regenerate to allow form resubmission on error
}

$errors = [];
$goMessage = [];

// ---------- course Registration ---------------
if (isset($_POST['submit_course'])) {

    if (isset($_POST['course_code']) && isset($_POST['course_name']) && $user->isAdmin()) {
        $courseCode = $_POST['course_code'];
        $courseName = $_POST['course_name'];
        if ($lecr->createCourse($courseCode, $courseName)) {
            $goMessage[] = "Course Created Successfully";
        } else {
            $errors[] = "Course Creation Failed";
        }
    } else {
        $errors[] = "Course Validation Failed";
    }
} else if (isset($_POST['updateCourse'])) {
    if (isset($_POST['course_id']) && isset($_POST['course_code']) && isset($_POST['course_name']) && $user->isAdmin()) {
        $courseId = $_POST['course_id'];
        $courseCode = $_POST['course_code'];
        $courseName = $_POST['course_name'];
        if ($lecr->updateCourse($courseId, $courseCode, $courseName)) {
            $goMessage[] = "Course Updated Successfully";
        } else {
            $errors[] = "Course Update Failed";
        }
    } else {
        $errors[] = "Course Validation Failed";
    }
} else if (isset($_POST['deleteCourse'])) {
    if (isset($_POST['course_id']) && $user->isAdmin()) {
        $courseId = $_POST['course_id'];
        if ($lecr->deleteCourse($courseId)) {
            $goMessage[] = "Course Deleted Successfully";
        } else {
            $errors[] = "Course Deletion Failed";
        }
    } else {
        $errors[] = "Invalid Privileges or Invalid Course";
    }
}

// ----------- class Registration ------------ 
else if (isset($_POST['submit_class'])) {
    if (isset($_POST['course_id']) && isset($_POST['lecr_id']) && isset($_POST['class_date']) && isset($_POST['start_time']) && isset($_POST['end_time']) && ($user->isAdmin() || $user->isLecturer())) {
        $courseId = $_POST['course_id'];
        $lecrId = $user->getLectureIdByUserId($_POST['lecr_id']);
        $classDate = $_POST['class_date'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $Instructors = isset($_POST['class-instructors']) ? $_POST['class-instructors'] : null;
        if ($classId = $lecr->createClass($lecrId, $courseId, $classDate, $startTime, $endTime)) {
            if ($Instructors != null) {
                foreach ($Instructors as $instructor) {
                    $lecr->addInstructorToClass($classId, intval($instructor));
                    $goMessage[] = "Instructor " . $instructor . " Added Successfully";
                }
            }
            $goMessage[] = "Class Created Successfully";
        } else {
            $errors[] = "Class Creation Failed - Possible schedule conflict or database error";
        }
    } else {
        $errors[] = "Class Validation Failed";
    }
} else if (isset($_POST['updateClass'])) {

    if (isset($_POST['class_id']) && isset($_POST['course_id']) && isset($_POST['lecr_id']) && isset($_POST['class_date']) && isset($_POST['start_time']) && isset($_POST['end_time']) && ($user->isAdmin() || $user->isLecturer())) {
        $classData = array();
        $classData['classId'] = $_POST['class_id'];
        $classData['courseId'] = $_POST['course_id'];
        $classData['lecrId'] = $_POST['lecr_id'];
        $classData['classDate'] = $_POST['class_date'];
        $classData['startTime'] = $_POST['start_time'];
        $classData['endTime'] = $_POST['end_time'];
        $classData['Instructors'] = $_POST['class-instructors'];
        if ($lecr->updateClassInfo($classData['classId'], $classData)) {
            $goMessage[] = "Class Updated Successfully";
        } else {
            $errors[] = "Class Update Failed - Possible schedule conflict or database error";
        }
    } else {
        $errors[] = "Class Validation Failed";
    }

} else if (isset($_POST['deleteClass'])) {
    if (isset($_POST['class_id']) && ($user->isAdmin() || $user->isLecturer())) {
        $classId = $_POST['class_id'];
        if ($lecr->deleteClass($classId)) {
            $goMessage[] = "Class Deleted Successfully";
        } else {
            $errors[] = "Class Deletion Failed";
        }
    } else {
        $errors[] = "Invalid Privileges or Invalid Class";
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
        if (isset($_POST['user_role']) && preg_match("/^[0-4]{1}$/", $_POST['user_role'])) {
            if ($_POST['user_role'] == 0) {
                if ($user->isAdmin()) {
                    $user_role = $_POST['user_role'];
                } else {
                    $errors[] = "User Operation not permitted!";
                }
            } else {
                $user_role = $_POST['user_role'];
            }
        } else {
            $errors[] = "User Role format invalid or empty.";
        }
        //user_status
        if (isset($_POST['user_status']) && $user->isAdmin()) {
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
            if (isset($_POST['lecr_gender'])) {
                if (preg_match("/^[0-2]{1}$/", $_POST['lecr_gender'])) {
                    $userData['lecr_gender'] = $_POST['lecr_gender'];
                } else {
                    $errors[] = "Gender Invalid!";
                }
            }
            if (is_uploaded_file($_FILES["lecr_profile_pic"]["tmp_name"]) && !empty($_POST['lecr_nic'])) {
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
            if (isset($_POST['std_index']) && preg_match("/^S\s\d{5}$/", $_POST['std_index'])) {
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
            if (isset($_POST['std_gender'])) {
                if (preg_match("/^[0-2]{1}$/", $_POST['std_gender'])) {
                    $userData['std_gender'] = $_POST['std_gender'];
                } else {
                    $errors[] = "Gender format invalid";
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
                if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $_POST['date_admission'])) {
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
                $picLocation = $util->storeProfilePic(ROOT_PATH . '/res/profiles/student/', 'std_profile_pic', $_POST['std_nic']);
                if ($picLocation != null && $picLocation) {
                    $userData['std_profile_pic'] = SERVER_ROOT . "/res/profiles/student/" . basename($picLocation);
                } else {
                    $userData['std_profile_pic'] = SERVER_ROOT . '/res/profiles/lecturer/default.png';
                    $errors[] = "Error Uploading Profile Picture";
                }
            }
        }

        // Uniqueness validation before registration
        if ($user_role == 3) {
            // Student uniqueness checks
            if (isset($userData['std_index']) && $user->isStudentIndexExist($userData['std_index'])) {
                $errors[] = "Student index number already exists";
            }
            if (isset($userData['std_nic']) && $user->isNicExist($userData['std_nic'])) {
                $errors[] = "NIC number already registered";
            }
            if (isset($userData['std_email']) && $user->isEmailExist($userData['std_email'])) {
                $errors[] = "Email address already registered";
            }
        } else {
            // Lecturer uniqueness checks
            if (isset($userData['lecr_nic']) && $user->isNicExist($userData['lecr_nic'])) {
                $errors[] = "NIC number already registered";
            }
            if (isset($userData['lecr_email']) && $user->isEmailExist($userData['lecr_email'])) {
                $errors[] = "Email address already registered";
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
} else if (isset($_POST['updateReg'])) {
    $userId = $_POST['user_id'];
    if ($user->isAdmin() || $_SESSION['user_id'] == $userId) {

        //to Store userData
        $userData = array();
        $user_role = $user->retrieveUserRole($userId);
        $userDefault = $user->retrieveUserDetails($userId);
        $user_status = $userDefault['user_status'];

        if (!empty($_POST['password'])) {
            $password = $_POST['password'];
        } else {
            $password = null;
        }

        if ($user->isAdmin()) {
            if (isset($_POST['user_status']) && $user->isAdmin()) {
                if (preg_match("/^[0-4]{1}$/", $_POST['user_status'])) {
                    $user_status = $_POST['user_status'];
                } else {
                    $errors[] = "Status format invalid!";
                }
            } else {
                $user_status = null;
            }
        }

        // --------------- To lecture data --> userData -------------------------
        if ($user_role == 0 || $user_role == 1 || $user_role == 2) {
            // Following has to change with admin privilages
            if ($user->isAdmin()) {
                if (!empty($_POST['lecr_nic'])) {
                    if (preg_match("/^\d{9}(V|v)?$|^(\d{12})$/", $_POST['lecr_nic'])) {
                        $userData['lecr_nic'] = $_POST['lecr_nic'];
                    } else {
                        $errors[] = "NIC should contain only 9 digits and V or 12 digits.";
                    }
                } else {
                    $userData['lecr_nic'] = $userDefault['lecr_nic'];
                }

                if (!empty($_POST['lecr_email'])) {
                    if (filter_var($_POST['lecr_email'], FILTER_VALIDATE_EMAIL)) {
                        $userData['lecr_email'] = $_POST['lecr_email'];
                    } else {
                        $errors[] = "Email is required and should be valid.";
                    }
                } else {
                    $userData['lecr_email'] = $userDefault['lecr_email'];
                }

                if (!empty($_POST['lecr_mobile'])) {
                    if (preg_match("/^\d{10}$/", $_POST['lecr_mobile'])) {
                        $userData['lecr_mobile'] = $_POST['lecr_mobile'];
                    } else {
                        $errors[] = "Mobile number is required and should contain only 10 digits.";
                    }
                } else {
                    $userData['lecr_mobile'] = $userDefault['lecr_mobile'];
                }
            } else {
                $userData['lecr_nic'] = $userDefault['lecr_nic'];
                $userData['lecr_email'] = $userDefault['lecr_email'];
                $userData['lecr_mobile'] = $userDefault['lecr_mobile'];
            }
            if (isset($_POST['lecr_gender'])) {
                if (preg_match("/^[0-2]{1}$/", $_POST['lecr_gender'])) {
                    $userData['lecr_gender'] = $_POST['lecr_gender'];
                } else {
                    $errors[] = "Gender format invalid";
                }
            } else {
                $userData['lecr_gender'] = $userDefault['lecr_gender'];
            }


            if (!empty($_POST['lecr_name'])) {
                if (preg_match("/^[a-zA-Z ]*$/", $_POST['lecr_name'])) {
                    $userData['lecr_name'] = $_POST['lecr_name'];
                } else {
                    $errors[] = "Name should contain only letters and whitespaces.";
                }
            } else {
                $userData['lecr_name'] = $userDefault['lecr_name'];
            }

            if (!empty($_POST['lecr_address'])) {
                if (preg_match("/^[a-zA-Z0-9\/, ]+$/", $_POST['lecr_address'])) {
                    $userData['lecr_address'] = $_POST['lecr_address'];
                } else {
                    $errors[] = "Address is required and should contain only letters, numbers, spaces and / or ,";
                }
            } else {
                $userData['lecr_address'] = $userDefault['lecr_address'];
            }

            if (is_uploaded_file($_FILES["lecr_profile_pic"]["tmp_name"])) {
                //TODO: get and upload photo
                $picLocation = $util->storeProfilePic(ROOT_PATH . '/res/profiles/lecturer/', 'lecr_profile_pic', $userData['lecr_nic']);
                if ($picLocation != null && $picLocation) {
                    $userData['lecr_profile_pic'] = SERVER_ROOT . "/res/profiles/lecturer/" . basename($picLocation);
                } else {
                    $userData['lecr_profile_pic'] = SERVER_ROOT . '/res/profiles/lecturer/default.png';
                    $errors[] = "Error Uploading Profile Picture";
                }
            } else {
                $userData['lecr_profile_pic'] = $userDefault['lecr_profile_pic'];
            }
            // --------------- End of To lecture data --> userData ----------------------
            //  ----------------------------- To Student data --> userData --------------------------------

        } else if ($user_role == 3) {
            $userData['std_index'] = $userDefault['std_index'];

            // Some details need admin privillages
            if ($user->isAdmin()) {
                if (!empty($_POST['std_nic'])) {
                    if (preg_match("/^\d{9}(V|v)?$|^(\d{12})$/", $_POST['std_nic'])) {
                        $userData['std_nic'] = $_POST['std_nic'];
                    } else {
                        $errors[] = "NIC is required and should contain only 9 digits and V or 12 digits.";
                    }
                } else {
                    $userData['std_nic'] = $userDefault['std_nic'];
                }

                if (!empty($_POST['mobile_tp_no'])) {
                    if (preg_match("/^\d{10}$/", $_POST['mobile_tp_no'])) {
                        $userData['mobile_tp_no'] = $_POST['mobile_tp_no'];
                    } else {
                        $errors[] = "Mobile number is required and should contain only 10 digits.";
                    }
                } else {
                    $userData['mobile_tp_no'] = $userDefault['mobile_tp_no'];
                }

                if (!empty($_POST['std_email'])) {
                    if (filter_var($_POST['std_email'], FILTER_VALIDATE_EMAIL)) {
                        $userData['std_email'] = $_POST['std_email'];
                    } else {
                        $errors[] = "Email is required and should be valid.";
                    }
                } else {
                    $userData['std_email'] = $userDefault['std_email'];
                }
            } else {
                $userData['std_nic'] = $userDefault['std_nic'];
                $userData['mobile_tp_no'] = $userDefault['mobile_tp_no'];
                $userData['std_email'] = $userDefault['std_email'];
            }

            if (!empty($_POST['std_fullname'])) {
                if (preg_match("/^[A-Za-z ]*$/", $_POST['std_fullname'])) {
                    $userData['std_fullname'] = $_POST['std_fullname'];
                } else {
                    $errors[] = "Name should contain only letters and whitespaces.";
                }
            } else {
                $userData['std_fullname'] = $userDefault['std_fullname'];
            }
            // Debugging
            if (isset($_POST['std_gender'])) {
                if (preg_match("/^[0-2]{1}$/", $_POST['std_gender'])) {
                    $userData['std_gender'] = $_POST['std_gender'];
                } else {
                    $errors[] = "Gender format invalid";
                }
            } else {
                $userData['std_gender'] = $userDefault['std_gender'];
            }

            if (!empty($_POST['std_batchno'])) {
                $userData['std_batchno'] = $_POST['std_batchno'];
            } else {
                $userData['std_batchno'] = $userDefault['std_batchno'];
            }

            if (!empty($_POST['std_dob'])) {
                if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $_POST['std_dob'])) {
                    $userData['std_dob'] = $_POST['std_dob'];
                } else {
                    $errors[] = "Date of Birth format invalid";
                }
            } else {
                $userData['std_dob'] = $userDefault['std_dob'];
            }

            if (!empty($_POST['date_admission'])) {
                if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $_POST['date_admission'])) {
                    $userData['date_admission'] = $_POST['date_admission'];
                } else {
                    $errors[] = "Date of Admission format invalid";
                }
            } else {
                $userData['date_admission'] = $userDefault['date_admission'];
            }

            if (!empty($_POST['current_address'])) {
                if (preg_match("/^[a-zA-Z0-9\/, ]+$/", $_POST['current_address'])) {
                    $userData['current_address'] = $_POST['current_address'];
                } else {
                    $errors[] = "Address should contain only letters, numbers, spaces and / or ,";
                }
            } else {
                $userData['current_address'] = $userDefault['current_address'];
            }
            if (!empty($_POST['permanent_address'])) {
                if (preg_match("/^[a-zA-Z0-9\/, ]+$/", $_POST['permanent_address'])) {
                    $userData['permanent_address'] = $_POST['permanent_address'];
                } else {
                    $errors[] = "Address should contain only letters, numbers, spaces and / or ,";
                }
            } else {
                $userData['permanent_address'] = $userDefault['permanent_address'];
            }

            if (!empty($_POST['home_tp_no'])) {
                if (preg_match("/^\d{10}$/", $_POST['home_tp_no'])) {
                    $userData['home_tp_no'] = $_POST['home_tp_no'];
                } else {
                    $errors[] = "Home number is required and should contain only 10 digits.";
                }
            } else {
                $userData['home_tp_no'] = $userDefault['home_tp_no'];
            }

            if (!empty($_POST['current_level'])) {
                $userData['current_level'] = $_POST['current_level'];
            } else {
                $userData['current_level'] = $userDefault['current_level'];
            }

            if (is_uploaded_file($_FILES["std_profile_pic"]["tmp_name"])) {
                //TODO: get and upload photo
                $picLocation = $util->storeProfilePic(ROOT_PATH . '/res/profiles/student/', 'std_profile_pic', $userData['std_nic']);
                if ($picLocation != null && $picLocation) {
                    $userData['std_profile_pic'] = SERVER_ROOT . "/res/profiles/student/" . basename($picLocation);
                } else {
                    $userData['std_profile_pic'] = SERVER_ROOT . '/res/profiles/lecturer/default.png';
                    $errors[] = "Error Uploading Profile Picture";
                }
            } else {
                $userData['std_profile_pic'] = $userDefault['std_profile_pic'];
            }
        }
        if (empty($errors)) {
            if ($user->editUser($userId, $password, $user_status, $userData)) {
                $goMessage[] = "User Update successfull";
            } else {
                $errors[] = "User Update Failed";
            }
        }
    } else {
        $errors[] = "User Operation not permitted!";
    }

    // Delete a User
} else if (isset($_POST['deleteReg'])) {
    if ($user->isAdmin()) {
        $userId = $_POST['user_id'];
        if ($user->deleteUser($userId)) {
            $goMessage[] = "User $userId Deleted Successfully";
        } else {
            $errors[] = "User Deletion Failed";
        }
    } else {
        $errors[] = "User Operation not permitted!";
    }
} else {
    header("Location: " . SERVER_ROOT . "/index.php");
}

include ROOT_PATH . '/php/include/header.php';
echo "<title>AMS Registration</title>";
include ROOT_PATH . '/php/include/content.php';
?>
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