<?php
class User
{
    private $userTable = "uoj_user";
    private $lecrTable = "uoj_lecturer";
    private $stdTable = "uoj_student";
    private $conn;
    private $loginMessage;
    private $default_pro_picture = SERVER_ROOT . '/res/profiles/default.png'; //Set the location of default profile


    public function __construct($db)
    {
        $this->conn = $db;
        $this->loginMessage = '';
    }

    public function getLoginMessage()
    {
        return $this->loginMessage;
    }

    public function setLoginMessage($Message)
    {
        $this->loginMessage = $Message;
    }

    public function processNameInitials($fullName)
    {
        // Processing Name with Initial
        $nameParts = explode(" ", $fullName);
        $processedName = '';
        foreach ($nameParts as $index => $namePart) {
            if ($index === count($nameParts) - 1) {
                $processedName .= ucfirst($namePart);
            } else {
                $processedName .= ucfirst(substr($namePart, 0, 1)) . " ";
            }
        }
        return $processedName;
    }

    // TODO: fix remeberme!
    public function login($username, $password, $rememberMe)
    {
        if (!empty($username) && !empty($password)) {
            $username = strtolower($username);
            //Retrieving User
            $getUserQuery = "SELECT * FROM " . $this->userTable . " WHERE username = ?";
            $stmt = $this->conn->prepare($getUserQuery);
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                // if (!($user['user_session'])) {
                // echo "$username";
                if (password_verify($password . $user['user_salt'], $user['user_password'])) {
                    if ($user['user_status'] == 1) {
                        $_SESSION["user_id"] = $user['user_id'];
                        $_SESSION["user_role"] = $user['user_role'];
                        if ($user['user_role'] == 3) {
                            $_SESSION["std_id"] = $this->getStdId($user['user_id']);
                        } else {
                            $_SESSION["lecr_id"] = $this->getLecrId($user['user_id']);
                        }
                        $this->setUserBadge();
                        $this->setUserLock(true);
                        return true;
                    } else {
                        $this->loginMessage = "Account is not Active or Pending Review!";
                        return false; // Message if user has not active or pending
                    }
                } else {
                    $this->loginMessage = "Username or Password Invalid!";
                    return false; // Password Incorrect!
                }
                // } else {
                // $this->loginMessage = "User is already logged in.";
                // return false;
                // }
            } else {
                $this->loginMessage = "Username or Password Invalid!";
                return false; // Message if username is incorrect!
            }
        } else {
            $this->loginMessage = "Username or Password Invalid!";
            return false; // Message if username or password is empty!
        }

    }

    public function logout()
    {
        $this->setUserLock(false);
        $_SESSION['user_id'] = '';
        session_destroy();
        header("Location: " . SERVER_ROOT . "/index.php");
    }

    public function getStdId($user_id)
    {
        $query = "SELECT std_id FROM {$this->stdTable} WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['std_id'];
        } else {
            return false;
        }
    }
    public function getLecrId($user_id)
    {
        $query = "SELECT lecr_id FROM {$this->lecrTable} WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['lecr_id'];
        } else {
            return false;
        }
    }
    public function setUserLock($status)
    {
        $query = "UPDATE $this->userTable SET user_session = " . (($status) ? 1 : 0) . " WHERE user_id= ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $_SESSION['user_id']);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    //retrieve for profile or update info
    public function setUserBadge()
    {
        $user = $this->retrieveUserDetails($_SESSION['user_id']);
        if ($user) {
            $_SESSION['user_name'] = $this->processNameInitials(($_SESSION['user_role'] == 3) ? $user['std_fullname'] : $user['lecr_name']);
            $picture = (isset($user['std_profile_pic'])) ? $user['std_profile_pic'] : ((isset($user['lecr_profile_pic'])) ? $user['lecr_profile_pic'] : false);
            $_SESSION['user_profile_pic'] = ($picture) ? $picture : $this->default_pro_picture;
        }
    }

    public function retrieveUserDetails($userId)
    {
        $userTable = $this->userTable;
        // get user role for select table
        $RetUserRolequery = "SELECT user_role FROM {$this->userTable} WHERE user_id =?";
        $stmt = $this->conn->prepare($RetUserRolequery);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $userRole = $result->fetch_assoc()['user_role'];
        } else {
            return false;
        }

        $table = ($userRole >= 0 && $userRole < 3) ? $this->lecrTable : $this->stdTable;
        $query = "SELECT urd.* , users.user_role, users.user_status, users.username FROM $table as urd INNER JOIN $userTable as users ON urd.user_id = users.user_id WHERE users.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return ($result->fetch_assoc());
        } else {
            return false;
        }
    }

    public function getRoleStr($roleNo)
    {
        if ($roleNo == 0) {
            return 'Administrator';
        } else if ($roleNo == 1) {
            return 'Lecturer';
        } else if ($roleNo == 2) {
            return 'Instructor';
        } else if ($roleNo == 3) {
            return 'Student';
        } else {
            return 'Unknown';
        }
    }

    public function getStatusStr($statusNo)
    {
        if ($statusNo == 2) {
            return 'Pending';
        } else if ($statusNo == 1) {
            return 'Active';
        } else if ($statusNo == 0) {
            return 'Inactive';
        } else {
            return 'Unknown';
        }
    }

    public function getGenderStr($genderNo)
    {
        if ($genderNo == '0') {
            return 'Male';
        } else if ($genderNo == '1') {
            return 'Female';
        }
    }

    public function retrieveUserRole($userId)
    {
        $query = "SELECT user_role FROM {$this->userTable} WHERE user_id =?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['user_role'];
        }
    }
    public function isUsernameAvailable($username)
    {
        $query = "SELECT * FROM {$this->userTable} WHERE username=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function isUserIdExist($userId)
    {
        $query = "SELECT * FROM {$this->userTable} WHERE user_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function isLoggedIn()
    {
        if (isset($_SESSION["user_id"])) {
            return true;
        } else {
            return false;
        }
    }

    public function isAdmin()
    {
        if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isLecturer()
    {
        if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function isInstructor()
    {
        if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == 2) {
            return true;
        } else {
            return false;
        }
    }

    public function isStudent()
    {
        if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == 3) {
            return true;
        } else {
            return false;
        }
    }

    public function listStaffUsers()
    {

    }

    public function getDefaultProfilePic()
    {
        return $this->default_pro_picture;

    }

    public function getStudentTable($order = array())
    {
        $query = "SELECT std.*, users.user_role, users.user_status, users.username, users.user_id FROM $this->stdTable as std INNER JOIN $this->userTable as users ON users.user_id = std.user_id ";
        if (isset($order['search'])) {
            $query .= "WHERE std.std_fullname LIKE '%" . $order['search'] . "%' OR OR users.username LIKE '%" . $order['search'] . "%' std.std_nic LIKE '%" . $order['search'] . "%' OR std.std_email LIKE '%" . $order['search'] . "%' OR std.std_mobile_tp_no LIKE '%" . $order['search'] . "%' OR std.std_home_tp_no LIKE '%" . $order['search'] . "%' OR std.std_batchno LIKE '%" . $order['search'] . "%' OR std.std_current_level LIKE '%" . $order['search'] . "%' OR std.std_dob LIKE '%" . $order['search'] . "%' OR std.std_date_admission LIKE '%" . $order['search'] . "%' OR std.std_current_address LIKE '%" . $order['search'] . "%' OR std.std_permanent_address LIKE '%" . $order['search'] . "%' ";
        }
        //TODO: CHECK
        if (isset($order['column'])) {
            $query .= " ORDER BY " . $order['column'] . ' ' . $order['dir'];
        } else {
            $query .= " ORDER BY users.username ASC";
        }
        if (isset($order['offset']) && $order['offset'] != -1) {
            $query .= " LIMIT " . $order['limit'] . ' OFFSET ' . $order['offset'];
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->get_result();
        return $results;
    }

    public function getLecturerTable($order = array())
    {
        $query = "SELECT lecr.*, users.user_role, users.user_status, users.username, users.user_id FROM $this->lecrTable as lecr INNER JOIN $this->userTable as users ON users.user_id = lecr.user_id ";
        if (isset($order['search'])) {
            $query .= "WHERE lecr.lecr_name LIKE '%" . $order['search'] . "%' OR lecr.lecr_nic LIKE '%" . $order['search'] . "%' OR users.username LIKE '%" . $order['search'] . "%' OR lecr.lecr_email LIKE '%" . $order['search'] . "%' OR lecr.lecr_mobile LIKE '%" . $order['search'] . "%' OR lecr.lecr_address LIKE '%" . $order['search'] . "%' ";
        }
        if (isset($order['column'])) {
            $query .= " ORDER BY " . $order['column'] . ' ' . $order['dir'];
        } else {
            $query .= " ORDER BY users.username ASC";
        }
        if (isset($order['offset']) && $order['offset'] != -1) {
            $query .= " LIMIT " . $order['limit'] . ' OFFSET ' . $order['offset'];
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->get_result();
        return $results;
    }

    public function countRecords($userTable)
    {
        $query = "SELECT COUNT(*) as count FROM $userTable";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->get_result();
        return $results->fetch_assoc()['count'];
    }

    // Register User -> Insert User -> (Register Lecturer OR Register Student)
    public function registerUser($username, $password, $userRole, $userData, $userStatus)
    {
        $userId = $this->insertUser($username, $password, $userRole, $userStatus);
        if ($userId) {
            if ($userRole == 0 || $userRole == 1 || $userRole == 2) {
                return $this->insertLecturer($userId, $userData);
            } else if ($userRole == 3) {
                return $this->insertStudent($userId, $userData);
            }
        } else {
            return false;
        }
    }

    public function insertUser($username, $password, $userRole, $userStatus)
    {
        $username = strtolower($username);
        $salt = bin2hex(random_bytes(16));
        $hashedPassword = (password_hash($password . $salt, PASSWORD_BCRYPT));
        $query = "INSERT INTO $this->userTable(username, user_password, user_salt, user_role" . (($userStatus) ? ", user_status" : "") . ") VALUES(?, ?, ?, ? " . (($userStatus) ? ", ?" : "") . ")";
        $stmt = $this->conn->prepare($query);
        if ($userStatus) {
            $stmt->bind_param('sssii', $username, $hashedPassword, $salt, $userRole, $userStatus);
        } else {
            $stmt->bind_param('sssi', $username, $hashedPassword, $salt, $userRole);
        }
        if ($stmt->execute()) {
            return $stmt->insert_id;
        } else {
            return false;
        }
    }

    // UserData: 0->index, 1->regno, 2->fullname, 3->gender, 4->batchno, 5->dgree_program, 6->subjectcomb, 7->nic, 8->dob, 9->admission, 10->current_address, 11->permanent_address
    //           12->mobile_tp_no, 13->home_tp_no, 14->email, 15->profile_pic, 16-> current_level
    public function insertStudent($userId, $userData)
    {
        $userData['std_index'] = strtolower($userData['std_index']);
        $userData['std_fullname'] = strtolower($userData['std_fullname']);
        //        $userData['std_dob']=date("Y-m-d",strtotime($_POST["std_dob"]));
        //        $userData['date_admission']=date("Y-m-d",strtotime($_POST["date_admission"]));

        $query = "INSERT INTO $this->stdTable(std_index,  std_fullname, std_gender, std_batchno,  std_nic, std_dob, date_admission, current_address, permanent_address, mobile_tp_no, home_tp_no, std_email, std_profile_pic, current_level, user_id) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssissssssssssi', $userData['std_index'], $userData['std_fullname'], $userData['std_gender'], $userData['std_batchno'], $userData['std_nic'], $userData['std_dob'], $userData['date_admission'], $userData['current_address'], $userData['permanent_address'], $userData['mobile_tp_no'], $userData['home_tp_no'], $userData['std_email'], $userData['std_profile_pic'], $userData['current_level'], $userId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }

    }

    //UserData: 0->name, 1->mobile, 2->email, 3->gender, 4->address, 5->profile_pic
    public function insertLecturer($userId, $userData)
    {
        $userData['lecr_name'] = strtolower($userData['lecr_name']);
        $query = "INSERT INTO $this->lecrTable(lecr_nic, lecr_name, lecr_mobile, lecr_email, lecr_gender, lecr_address, lecr_profile_pic, user_id) VALUES(?,?,?,?,?,?,?,?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssssissi', $userData['lecr_nic'], $userData['lecr_name'], $userData['lecr_mobile'], $userData['lecr_email'], $userData['lecr_gender'], $userData['lecr_address'], $userData['lecr_profile_pic'], $userId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // editUser -> Update User(User Status) -> (Update Lecturer OR Update Student)Details or update password
    public function editUser($userId, $password, $userStatus, $userData)
    {
        $userRole = $this->retrieveUserRole($userId);
        if (!empty($password)) {
            if (!($this->changeUserPassword($userId, $password))) {
                return false;
            }
        }
        if (!empty($userStatus) && $this->isAdmin()) {
            if (!($this->updateUser($userId, $userStatus))) {
                return false;
            }
        }
        if ($userRole == 0 || $userRole == 1 || $userRole == 2) {
            if (!($this->updateLecturer($userId, $userData))) {
                return false;
            }
        } else if ($userRole == 3) {
            if (!($this->updateStudent($userId, $userData))) {
                return false;
            }
        }
        return true;
    }

    public function changeUserPassword($userId, $password)
    {
        $query = "UPDATE $this->userTable SET user_password = ?, user_salt = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $salt = bin2hex(random_bytes(16));
        $hashedPassword = password_hash($password . $salt, PASSWORD_BCRYPT);
        $stmt->bind_param('ssi', $hashedPassword, $salt, $userId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function updateUser($userId, $userStatus)
    {
        $query = "UPDATE $this->userTable SET user_status = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $userStatus, $userId);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateLecturer($userId, $userData)
    {
        $query = "UPDATE $this->lecrTable SET lecr_nic = ?, lecr_name = ?, lecr_mobile = ?, lecr_email = ?, lecr_gender = ?, lecr_address = ?, lecr_profile_pic = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssssissi', $userData['lecr_nic'], $userData['lecr_name'], $userData['lecr_mobile'], $userData['lecr_email'], $userData['lecr_gender'], $userData['lecr_address'], $userData['lecr_profile_pic'], $userId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateStudent($userId, $userData)
    {
        $query = "UPDATE $this->stdTable SET std_index = ?,  std_fullname = ?, std_gender = ?, std_batchno = ?,   std_nic = ?, std_dob = ?, date_admission = ?, current_address = ?, permanent_address = ?, mobile_tp_no = ?, home_tp_no = ?, std_email = ?, std_profile_pic = ?, current_level = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssissssssssssi', $userData['std_index'], $userData['std_fullname'], $userData['std_gender'], $userData['std_batchno'], $userData['std_nic'], $userData['std_dob'], $userData['date_admission'], $userData['current_address'], $userData['permanent_address'], $userData['mobile_tp_no'], $userData['home_tp_no'], $userData['std_email'], $userData['std_profile_pic'], $userData['current_level'], $userId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteUser($userId)
    {
        $userTable = $this->userTable;
        $query = "DELETE FROM $userTable WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $userId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

/*
 *   /index.php
 *   /php/admin_dashboard.php
 *   /php/
 */