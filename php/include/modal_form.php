<!-- Add course modal -->
<div class="modal fade" id="add_course">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content rounded shadow">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">Add Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <
            </div>
        </div>
    </div>
</div>
<!-- Reg User Modal -->
<?php

?>
<div class="modal fade" tabindex="-1" id="reg_user">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content rounded shadow">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">User Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <form action="<?php echo ROOT_PATH; ?>/php/register_user.php" method="post" id='RegistrationForm'
                    enctype="multipart/form-data">
                    <div class="form-group mt-3">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="2020csc000"
                            required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password"
                            oninput="validatePassword()" placeholder="Password" required>
                        <span id="password-strength"></span>
                    </div>
                    <div class="form-group mt-3">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            oninput="validatePassword()" placeholder="Confirm Password" required>
                        <span id="password-match"></span>
                    </div>
                    <div class="form-group mt-3">
                        <label for="user_role">User Role:</label>
                        <select name="user_role" id="user_role" class="form-control" onchange="toggleFields()">
                            <option value='3' selected>Student</option>
                            <option value='1'>Lecturer</option>
                            <option value='2'>Instructor</option>
                            <?php
                            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 0) {
                                echo "
                                <option value='0'>Administrator</option>
                                ";
                            }
                            ?>
                        </select>
                    </div>
                    <?php
                    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 0) {
                        echo "
                        <div class='form-group mt-3'>
                            <label for='user_status'>User Status:</label>
                            <select name='user_status' id='user_status' class='form-control'>
                                <option value='2'>Pending</option>
                                <option value='1'>Active</option>
                                <option value='0'>InActive</option>
                            </select>
                        </div>
                        ";
                    }
                    ?>
                    <!-- std fields -->
                    <div class="d-none" id="std_fields">
                        <!-- <div class="form-floating">
                            <input type="text" class="form-control" id="std_regNo" name="std_regNo"
                                placeholder="2020csc000" required>
                            <label for="std_regNo">Student Reg No</label>
                        </div> -->

                        <!-- std_index -->
                        <div class="form-group mt-3">
                            <label for="std_index">Student Index:</label>
                            <input type="text" class="form-control" id="std_index" name="std_index">
                        </div>
                        <!-- std_regno -->
                        <div class="form-group mt-3">
                            <label for="std_regno">Student Registration Number:</label>
                            <input type="text" class="form-control" id="std_regno" name="std_regno">
                        </div>
                        <!-- std_fullname -->
                        <div class="form-group mt-3">
                            <label for="std_fullname">Full Name:</label>
                            <input type="text" class="form-control" id="std_fullname" name="std_fullname">
                        </div>
                        <!-- std_gender -->
                        <div class="form-group mt-3">
                            <label for="std_gender">Gender:</label>
                            <select class="form-control" id="std_gender" name="std_gender">
                                <option value="0">Male</option>
                                <option value="1">Female</option>
                            </select>
                        </div>
                        <!-- std_batchno -->
                        <div class="form-group mt-3">
                            <label for="std_batchno">Batch Number:</label>
                            <input type="text" class="form-control" id="std_batchno" name="std_batchno">
                        </div>
                        <!-- dgree_program -->
                        <div class="form-group mt-3">
                            <label for="dgree_program">Degree Program:</label>
                            <input type="text" class="form-control" id="dgree_program" name="dgree_program">
                        </div>
                        <!-- std_subjectcomb -->
                        <div class="form-group mt-3">
                            <label for="std_subjectcomb">Subject Combination:</label>
                            <input type="text" class="form-control" id="std_subjectcomb" name="std_subjectcomb">
                        </div>
                        <!-- std_nic -->
                        <div class="form-group mt-3">
                            <label for="std_nic">NIC:</label>
                            <input type="text" class="form-control" id="std_nic" name="std_nic">
                        </div>
                        <!-- std_dob -->
                        <div class="form-group mt-3">
                            <label for="std_dob">Date of Birth:</label>
                            <input type="date" class="form-control" id="std_dob" name="std_dob">
                        </div>
                        <!-- date_admission -->
                        <div class="form-group mt-3">
                            <label for="date_admission">Date of Admission:</label>
                            <input type="date" class="form-control" id="date_admission" name="date_admission">
                        </div>
                        <!-- current_address -->
                        <div class="form-group mt-3">
                            <label for="current_address">Current Address:</label>
                            <input type="text" class="form-control" id="current_address" name="current_address">
                        </div>
                        <!-- permanent_address -->
                        <div class="form-group mt-3">
                            <label for="permanent_address">Permanent Address:</label>
                            <input type="text" class="form-control" id="permanent_address" name="permanent_address">
                        </div>
                        <!-- mobile_tp_no -->
                        <div class="form-group mt-3">
                            <label for="mobile_tp_no">Mobile Phone Number:</label>
                            <input type="tel" class="form-control" id="mobile_tp_no" name="mobile_tp_no">
                        </div>
                        <!-- home_tp_no -->
                        <div class="form-group mt-3">
                            <label for="home_tp_no">Home Phone Number:</label>
                            <input type="tel" class="form-control" id="home_tp_no" name="home_tp_no">
                        </div>
                        <!-- std_email -->
                        <div class="form-group mt-3">
                            <label for="std_email">Email:</label>
                            <input type="email" class="form-control" id="std_email" name="std_email">
                        </div>
                        <!-- std_profile_pic -->
                        <div class="form-group mt-3">
                            <label for="std_profile_pic">Profile Picture:</label>
                            <input type="file" class="form-control-file" id="std_profile_pic" name="std_profile_pic"
                                accept=".jpg, .jpeg, .png">
                            <span id="std_profile_error"></span>
                        </div>
                        <!-- current_level -->
                        <div class="form-group mt-3">
                            <label for="current_level">Current Level:</label>
                            <input type="text" class="form-control" id="current_level" name="current_level">
                        </div>
                    </div>

                    <!-- lecr Fields -->
                    <div id="lecr_fields" class="d-none">
                        <!-- lecr_nic -->
                        <div class="form-group mt-3">
                            <label for="lecr_nic">NIC:</label>
                            <input type="text" class="form-control" id="lecr_nic" name="lecr_nic">
                        </div>
                        <!-- lecr_name -->
                        <div class="form-group mt-3">
                            <label for="lecr_name">Full Name:</label>
                            <input type="text" class="form-control" id="lecr_name" name="lecr_name">
                        </div>
                        <!-- lecr_mobile -->
                        <div class="form-group mt-3">
                            <label for="lecr_mobile">Mobile Phone Number:</label>
                            <input type="tel" class="form-control" id="lecr_mobile" name="lecr_mobile">
                        </div>
                        <!-- lecr_email -->
                        <div class="form-group mt-3">
                            <label for="lecr_email">Email:</label>
                            <input type="email" class="form-control" id="lecr_email" name="lecr_email">
                        </div>
                        <!-- lecr_gender -->
                        <div class="form-group mt-3">
                            <label for="lecr_gender">Gender:</label>
                            <select class="form-control" id="lecr_gender" name="lecr_gender">
                                <option value="0">Male</option>
                                <option value="1">Female</option>
                            </select>
                        </div>
                        <!-- lecr_address -->
                        <div class="form-group mt-3">
                            <label for="lecr_address">Address:</label>
                            <input type="text" class="form-control" id="lecr_address" name="lecr_address">
                        </div>
                        <!-- lecr_profile_pic -->
                        <div class="form-group mt-3">
                            <label for="lecr_profile_pic">Profile Picture:</label>
                            <input type="file" class="form-control-file" id="lecr_profile_pic" name="lecr_profile_pic"
                                accept=".jpg, .jpeg, .png">
                            <span id="lecr_profile_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer mt-5">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            id="close">Close</button>
                        <button type="submit" class="btn btn-primary" name="register" id="register">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFields() {
        var userType = document.getElementById("user_role").value;
        var studentFields = document.getElementById("std_fields");
        var lecturerFields = document.getElementById("lecr_fields");

        studentFields.classList.add("d-none");
        lecturerFields.classList.add("d-none");

        if (userType == 3) {
            document.getElementById('std_index').required = true;
            document.getElementById('std_regno').required = true;
            document.getElementById('std_fullname').required = true;
            document.getElementById('std_nic').required = true;
            document.getElementById('mobile_tp_no').required = true;
            studentFields.classList.remove("d-none");
        } else if (userType == 2 || userType == 1 || userType == 0) {
            document.getElementById('lecr_nic').required = true;
            document.getElementById('lecr_name').required = true;
            document.getElementById('lecr_mobile').required = true;
            document.getElementById('lecr_email').required = true;
            lecturerFields.classList.remove("d-none");
        }
    }

    function validatePassword() {
        var password = document.getElementById("password").value;
        var confirm_password = document.getElementById("confirm_password").value;
        var password_strength = document.getElementById("password-strength");
        var password_match = document.getElementById("password-match");

        // Validate password strength
        var strength = 0;

        if (password.match(/[a-zA-Z]+/)) {
            strength += 1;
        }
        if (password.match(/[0-9]+/)) {
            strength += 1;
        }
        if (password.match(/[$@#&!]+/)) {
            strength += 1;
        }

        if (password.length < 6 || strength < 2) {
            password_strength.textContent = "Password is weak";
            password_strength.style.color = "red";
        } else if (password.length < 8 || strength < 3) {
            password_strength.textContent = "Password is moderate";
            password_strength.style.color = "orange";
        } else {
            password_strength.textContent = "Password is strong";
            password_strength.style.color = "green";
        }

        // Validate password match
        if (password === confirm_password && password !== "") {
            password_match.textContent = "Passwords match";
            password_match.style.color = "green";
        } else if (confirm_password !== "") {
            password_match.textContent = "Passwords do not match";
            password_match.style.color = "red";
        } else {
            password_match.textContent = "";
        }
    }

    document.getElementById('RegistrationForm').addEventListener('submit', function (event) {
        event.preventDefault();
        // const formData = document.getElementById('RegistrationForm');
        const password = document.getElementById("password").value;
        const confirm_password = document.getElementById("confirm_password").value;
        const user_role = document.getElementById('user_role').value;
        const maxSizeInBytes = 500 * 1024; //200 KB
        var $goFlag = true;

        if (user_role == '0' || user_role == '1' || user_role == '2') {
            const profile = document.getElementById['lecr_profile_pic'];
            if (profile.files.length > 0 && profile.file[0].size > maxSizeInBytes) {
                alert('Upload a picture less than 500KB');
                $goFlag = false;
            }
        } else if (user_role == '3') {
            const profile = document.getElementById['std_profile_pic'];
            if (profile.files.length > 0 && profile.file[0].size > maxSizeInBytes) {
                alert('Upload a picture less than 500KB');
                $goFlag = false;
            }
        }

        if (password !== confirm_password) {
            alert('Password not matched!');
            $goFlag = false;
        }
        if ($goFlag) {
            this.submit();
        }
    });

    document.getElementById('std_profile_pic').addEventListener('change', function () {
        const maxSizeInBytes = 500 * 1024; // 500KB
        const file = this.files[0];
        if (file && file.size > maxSizeInBytes) {
            const error_label = document.getElementById('std_profile_error');
            error_label.textContent = "File size is too large";
            error_label.style.color = "red";
        } else {
            const error_label = document.getElementById('std_profile_error');
            error_label.textContent = "";
        }
    });

    document.getElementById('lecr_profile_pic').addEventListener('change', function () {
        const maxSizeInBytes = 500 * 1024; // 500KB
        const file = this.files[0];
        if (file && file.size > maxSizeInBytes) {
            const error_label = document.getElementById('lecr_profile_error');
            error_label.textContent = "File size is too large";
            error_label.style.color = "red";
        } else {
            const error_label = document.getElementById('lecr_profile_error');
            error_label.textContent = "";
        }
    });

</script>