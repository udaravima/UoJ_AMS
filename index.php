<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/MyAttendanceSys/config.php';
require_once 'config.php';
include_once ROOT_PATH . '/php/config/Database.php'; // Carefull with location!
include_once ROOT_PATH . '/php/class/Utils.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Lecturer.php';
// Initializing
$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$lecturer = new Lecturer($db);
$util = new Utils();

if ($user->isLoggedIn()) {
    if ($user->isAdmin()) {
        header("Location: " . SERVER_ROOT . "/php/admin_dashboard.php");
    } else if ($user->isLecturer()) {
        header("Location: " . SERVER_ROOT . "/php/lecturer_dashboard.php");
    } else if ($user->isInstructor()) {
        header("Location: " . SERVER_ROOT . "/php/instructor_dashboard.php");
    } else if ($user->isStudent()) {
        header("Location: " . SERVER_ROOT . "/php/student_dashboard.php");
    }
}

if (isset($_POST['sign_in'])) {

    $username = (isset($_POST['reg-name'])) ? $_POST['reg-name'] : null;
    $password = (isset($_POST['user_password'])) ? $_POST['user_password'] : null;
    $rememberMe = (isset($_POST['rem_me'])) ? $_POST['rem_me'] : null;

    if ($user->login($username, $password, $rememberMe)) {
        if ($user->isAdmin()) {
            header("Location: " . SERVER_ROOT . "/php/admin_dashboard.php");
        } else if ($user->isLecturer()) {
            header("Location: " . SERVER_ROOT . "/php/lecturer_dashboard.php");
        } else if ($user->isInstructor()) {
            header("Location: " . SERVER_ROOT . "/php/instructor_dashboard.php");
        } else if ($user->isStudent()) {
            header("Location: " . SERVER_ROOT . "/php/student_dashboard.php");
        } else {
            echo "Something Wrong!";
        }

    } else {
        // get the message and preview on modal
        $message[] = $user->getLoginMessage();
        $_POST = array();
        $_SESSION["error"] = $message;
        // user message modal
        $logInfoModal = $util->setMessage($message, 'alert', 'danger');
        echo $logInfoModal;
        // echo '<meta http-equiv="refresh" content= "0">';
    }
}
?>
<?php
include ROOT_PATH . '/php/include/header.php';

echo "<title> AMS Login </title>";

include ROOT_PATH . '/php/include/content.php';
?>
<!-- content begins  -->

<!-- main container -->
<div class="container-lg justify-content-center align-items-center mt-5">
    <div class="row align-items-center justify-content-center">

        <div
            class="col-8 col-md-4 col-lg-3 border rounded shadow form-signin align-items-center justify-content-center">
            <form action="" method="post" class="form">
                <img id="logo" class="img-fluid" src="<?php echo SERVER_ROOT; ?>/res/logo/AMS_side_banner_w.png"
                    alt="AMS_Banner" width="" height="">
                <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
                <!-- <div id="login-alert" class="alert">
                     <p class="">Hello</p> 
                </div> -->
                <div class="form-floating">
                    <input type="text" class="form-control" id="reg-name" name="reg-name" placeholder="2020csc000"
                        required autofocus>
                    <label for="reg-name">Registration No:</label>
                </div>

                <div class="form-floating">
                    <input type="password" class="form-control" id="user_password" name="user_password"
                        placeholder="Password" required>
                    <label for="user_password">Password</label>
                </div>

                <div class="form-check text-start my-3">
                    <input class="form-check-input" type="checkbox" value="rem_me" name="rem_me" id="rem_me">
                    <label class="form-check-label" for="rem_me">
                        Remember me
                    </label>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit" name="sign_in" id="sign_in">Sign in</button>
                <div class="mt-4">
                    <p class="align-items-center justify-content-center">
                        <a class="link-underline link-opacity-75-hover link-offset-2" href="#"> Forgot your password?
                        </a>
                    </p>
                    <p class="align-items-center justify-content-center">
                        <a class="link-underline link-offset-2 link-opacity-75-hover" data-bs-toggle="modal"
                            data-bs-target="#reg_user" href="#">Don't have an account?
                        </a>
                    </p>
                </div>
                <div class="mt-4">
                    <p class="align-items-center justify-content-center">&copy; 2023 Attendance Management System </p>
                </div>

                <!-- <p class="mt-5 mb-3 text-body-secondary align-items-center justify-content-center">Attendance Management System <br> Department of Computer Science<br> University of Jaffna</p> -->
            </form>
        </div>
    </div>
</div>

<!-- Reg User Module -->
<?php
include ROOT_PATH . '/php/include/modal_form.php';
?>

<!-- page scripts -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const logo = document.getElementById('logo');
        const themeLinks = document.querySelectorAll('[data-bs-theme]');

        themeLinks.forEach(link => {
            link.addEventListener("click", function (e) {
                // e.preventDefault(); // unable to click when its on
                const theme = this.getAttribute("data-bs-theme");
                updateImage(theme);
            });
        });

        function updateImage(theme) {
            // Define a mapping of themes to image URLs
            const logoImages = {
                dark: "<?php echo SERVER_ROOT; ?>/res/logo/AMS_side_banner_w.png",
                light: "<?php echo SERVER_ROOT; ?>/res/logo/AMS_side_banner.png",
            };

            // Set the image source based on the selected theme
            logo.src = logoImages[theme];
        }
    });
</script>

<!-- content ends  -->
<?php
include ROOT_PATH . '/php/include/footer.php';
?>