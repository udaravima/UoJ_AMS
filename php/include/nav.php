<nav class="navbar navbar-expand-md fixed-top navbar-light mt-0 mb-5">
    <div class="container-md rounded">
        <a class="navbar-brand" href="<?php echo SERVER_ROOT; ?>/index.php">
            <img id="logo" src="<?php echo SERVER_ROOT; ?>/res/logo/AMS_logo.png" alt="AMS_logo" width="40" height="40"
                class="d-inline-block align-text-top">
            AMS
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav nav ml-auto nav-pills">
                <?php
                if ($_SESSION['user_role'] == 0) {
                    echo "
                <li class='nav-item'>
                    <a class='nav-link " . (($activeDash == 0) ? "active" : "") . "' href='" . SERVER_ROOT . "/php/admin_dashboard.php'>Admin</a>
                </li>";
                }

                if ($_SESSION['user_role'] < 2) {
                    echo "
                <li class='nav-item'>
                    <a class='nav-link " . (($activeDash == 1) ? "active" : "") . "' href='" . SERVER_ROOT . "/php/lecturer_dashboard.php'>Lecturer</a>
                </li>";
                }

                if ($_SESSION['user_role'] < 3) {
                    echo "
                <li class='nav-item'>
                    <a class='nav-link " . (($activeDash == 2) ? "active" : "") . "' href='" . SERVER_ROOT . "/php/Instructor_dashboard.php'>Instructor</a>
                </li>";
                }
                ?>
                <li class='nav-item'>
                    <a class='nav-link <?php echo ($activeDash == 3) ? "active" : ""; ?>'
                        href='<?php echo SERVER_ROOT; ?>/php/student_dashboard.php'>Student</a>
                </li>
            </ul>

            <hr>

            <div class="dropdown ms-auto">
                <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo $_SESSION['user_profile_pic'] ?>" alt="" width="64" height="64 "
                        class="rounded-circle me-2">
                    <strong>
                        <?php echo $_SESSION['user_name']; ?>
                    </strong>
                </a>
                <ul class="dropdown-menu text-small shadow">
                    <li><a class="dropdown-item" href="#">New project...</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><a class="dropdown-item" data-bs-toggle="offcanvas" aria-controls="profileOffcanvas"
                            href="#profileOffcanvas" role="button">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="<?php echo SERVER_ROOT; ?>/php/logout.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- User Info offcanvasa -->
<div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="profileOffcanvas"
    aria-labelledby="profileOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="profileOffcanvasLabel">
            Your Info:
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card">
            <img src="<?php echo $_SESSION['user_profile_pic'] ?>" alt="Profile Image" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">
                    <?php echo $_SESSION['user_name']; ?>
                </h5>
            </div>
            <ul class="list-group list-group-flush">
                <?php
                $userDetails = $user->retrieveUserDetails($_SESSION['user_id']);

                if ($_SESSION['user_role'] == '0' || $_SESSION['user_role'] == '1' || $_SESSION['user_role'] == '2') {
                    $lecrInfoDsp = "
                    <li class='list-group-item'>NIC No: {$userDetails['lecr_nic']}</li>
                    <li class='list-group-item'>Full Name: {$userDetails['lecr_name']}</li>
                    <li class='list-group-item'>Mobile No: {$userDetails['lecr_mobile']}</li>
                    <li class='list-group-item'>Email: {$userDetails['lecr_email']}</li>
                    <li class='list-group-item'>Gender: {$user->getGenderStr($userDetails['lecr_gender'])}</li>
                    <li class='list-group-item'>Address: {$userDetails['lecr_address']}</li>";
                    echo $lecrInfoDsp;
                } else if ($_SESSION['$user_role'] == '3') {
                    $stdInfoDsp = "
                    <li class='list-group-item'>Current Level: {$userDetails['current_level']}</li>
                    <li class='list-group-item'>Batch No: {$userDetails['std_batchno']}</li>
                    <li class='list-group-item'>Index No: {$userDetails['std_index']}</li>
                    <li class='list-group-item'>Registration No: {$userDetails['std_regno']}</li>
                    <li class='list-group-item'>Full Name: {$userDetails['std_fullname']}</li>
                    <li class='list-group-item'>Gender: {$user->getGenderStr($userDetails['std_gender'])}</li>
                    <li class='list-group-item'>Degree Program: {$userDetails['degree_program']}</li>
                    <li class='list-group-item'>Subject Combination: {$userDetails['std_subjectcom']}</li>
                    <li class='list-group-item'>NIC No: {$userDetails['std_nic']}</li>
                    <li class='list-group-item'>Date of Birth: {$userDetails['std_dob']}</li>
                    <li class='list-group-item'>Date of Admission: {$userDetails['date_admission']}</li>
                    <li class='list-group-item'>Permanent Address: {$userDetails['permanent_address']}</li>
                    <li class='list-group-item'>Temporary Address: {$userDetails['current_address']}</li>
                    <li class='list-group-item'>Mobile No: {$userDetails['mobile_tp_no']}</li>
                    <li class='list-group-item'>Home Tp No: {$userDetails['home_tp_no']}</li>
                    <li class='list-group-item'>E-Mail: {$userDetails['std_email']}</li>";
                    echo $stdInfoDsp;
                }
                ?>
            </ul>

            <div class="card-body">
                <a href="#" class="btn btn-primary">Edit Profile</a>
                <a href="<?php echo SERVER_ROOT; ?>/php/logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </div>
</div>

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
                dark: "<?php echo SERVER_ROOT; ?>/res/logo/AMS_logo_w.png",
                light: "<?php echo SERVER_ROOT; ?>/res/logo/AMS_logo.png",
            };

            // Set the image source based on the selected theme
            logo.src = logoImages[theme];
        }
    });
</script>