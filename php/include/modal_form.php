<!-- Add class modal -->
<div class="modal fade" id="add_class">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content rounded shadow">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">Add Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form action="<?php echo SERVER_ROOT; ?>/php/form_action.php" method="post" id="addClass">
                    <input type="hidden" name="class_id" id="class_id" value="">
                    <div class="form-group mt-3">
                        <label for="course_code">Course Code:</label>
                        <select name="course_id" id="course_id" class="form-control" aria-label="course selection"
                            title="course selection">
                            <?php
                            //TODO: check following
                            //$courses = $lecr->getCourseList();
                            //while ($course = $courses->fetch_assoc()) {
                            //    echo "<option value='" . $course['course_id'] . "'>" . $course['course_code'] . "</option>";
                            //}
                            ?>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="lecr_id">LectureID: </label>
                        <?php
                        if ($user->isAdmin()) {
                            echo "<select name='lecr_id' id='lecr_id' class='form-control'>";
                            $lecturers = $user->getLecturerTable();
                            while ($lecturer = $lecturers->fetch_assoc()) {
                                echo "<option value='" . $lecturer['lecr_id'] . "'>" . $lecturer['lecr_name'] . "</option>";
                            }
                            echo "</select>";
                        } else {
                            echo "<input type='text' class='form-control' id='lecr_id' name='lecr_id' value='" . $_SESSION['user_id'] . "' readonly>";
                        }
                        ?>
                    </div>
                    <div class="form-group mt-3">
                        <label for="class_date">Date:</label>
                        <input type="date" class="form-control" id="class_date" name="class_date" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="start_time">Start Time:</label>
                        <input type="time" class="form-control" id="start_time" name="start_time" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="end_time">End Time:</label>
                        <input type="time" class="form-control" id="end_time" name="end_time" required>
                    </div>
                    <input type="hidden" name="submit_class" value="submit">
                </form>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer mt-5">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-class">Close</button>
                <button type="button" onclick="exeSubmit('addClass')" class="btn btn-primary" name="submit_class"
                    id="sumbit_class">Add Class</button>
            </div>
        </div>
    </div>
</div>

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
                <form action="<?php echo SERVER_ROOT; ?>/php/form_action.php" method="post" id='addCourse'>
                    <div class="form-group mt-3">
                        <label for="course_code">Course Code:</label>
                        <input type="text" class="form-control" id="course_code" name="course_code"
                            placeholder="CSC 1010" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="course_name">Course Name:</label>
                        <input type="text" class="form-control" id="course_name" name="course_name"
                            placeholder="Introduction to Programming" required>
                    </div>
                    <input type="hidden" name="submit_course" value="sumbit">
                </form>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer mt-5">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-course">Close</button>
                <button type="button" onclick="exeSubmit('addCourse')" class="btn btn-primary" name="submit_course"
                    id="submit_course">Add
                    Course</button>
            </div>
        </div>
    </div>
</div>

<!-- Reg User Modal -->
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
                <form action="<?php echo SERVER_ROOT; ?>/php/form_action.php" method="post" id='RegistrationForm'
                    enctype="multipart/form-data">
                    <input type="hidden" name="user_id" id="user_id" value=""> <!-- to pass userID -->
                    <div class="form-group mt-3">
                        <label for="username">Registration No:</label>
                        <input type="text" class="form-control" id="username" name="username"
                            oninput="userAvailabilityCheck()" placeholder="2020csc000" required>
                        <span id="availability_message"></span>
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
                        <select name="user_role" id="user_role" class="form-control" onchange="toggleFields()"
                            title="user-role" aria-label="Select user-role" required>
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
                    <div class="" id="std_fields">
                        <!-- std_index -->
                        <div class="form-group mt-3">
                            <label for="std_index">Student Index:</label>
                            <input type="text" class="form-control" id="std_index" name="std_index">
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
                                <option value='0'>Male</option>
                                <option value='1'>Female</option>
                            </select>
                        </div>
                        <!-- std_batchno -->
                        <div class="form-group mt-3">
                            <label for="std_batchno">Batch Number:</label>
                            <input type="text" class="form-control" id="std_batchno" name="std_batchno">
                        </div>
                        <!-- std_nic -->
                        <div class="form-group mt-3">
                            <label for="std_nic">NIC:</label>
                            <input type="text" class="form-control" id="std_nic" name="std_nic"
                                pattern="\d{9}(V|v)?$|^(\d{12})$">
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
                            <input type="text" class="form-control" id="lecr_nic" name="lecr_nic"
                                pattern="\d{9}(V|v)?$|^(\d{12})$">
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
                    <input type="hidden" name="register" id="regSubmit" value="submit">
                </form>
                <div id="error-log" class="d-none alert alert-danger mt-3">
                    <h5>Errors: </h5>
                    <ul id="error-list" class="">
                    </ul>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer mt-5">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-reg">Close</button>
                <button type="button" onclick="exeSubmit('RegistrationForm')" class="btn btn-primary" name="register"
                    id="register">Register</button>
                <?php if ($user->isAdmin()) {
                    echo "<button type='button' onclick='deleteRec(\"RegistrationForm\")' class='btn btn-danger d-none'
                    name='deleteReg' id='deleteReg'>Delete</button>";
                } ?>
                <button type="button" onclick="updateRec('RegistrationForm')" class="btn btn-primary d-none"
                    name="updateReg" id="updateReg">Update</button>

            </div>
        </div>
    </div>
</div>

<script>
    // Reload window when the modal closing
    document.getElementById('add_course').addEventListener('hidden.bs.modal', function () {
        window.location.reload();
    });
    document.getElementById('add_class').addEventListener('hidden.bs.modal', function () {
        window.location.reload();
    });
    document.getElementById('reg_user').addEventListener('hidden.bs.modal', function () {
        window.location.reload();
    });
    //proccess for update class
    document.getElementById('update_class').addEventListener('show.bs.modal', function (event) {
        var cid = $(event.relatedTarget).data("class-id");
        if (typeof cid !== "undefined") {
            document.getElementById('classSubmit').name = "updateClass";
            document.getElementById('classSubmit').value = "update";
            document.getElementById('class_id').value = cid;
            // console.log(cid);
            // var userForm = document.getElementById('RegistrationForm');
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/validation.php',
                data: { cid: cid },
                dataType: 'json',
                success: function (response) {
                    if (!response.error) {
                        $('#course_id').val(response.course_id);
                        $('#course_id').prop('readonly', true);
                        $('#lecr_id').val(response.lecr_id);
                        $('#lecr_id').prop('readonly', true);
                        $('#class_date').val(response.class_date);
                        $('#start_time').val(response.start_time);
                        $('#end_time').val(response.end_time);
                        $('#updateClass').removeClass('d-none');
                        $('#deleteClass').removeClass('d-none');
                        $('#addClass').addClass('d-none');
                    } else {
                        console.log(response.error);
                    }
                },
                error: function () {
                    console.log('error');
                }
            });
        } else {
            console.log("no data");
        }
    });
    // process for registration -> update and delete
    document.getElementById('reg_user').addEventListener('show.bs.modal', function (event) {
        var uid = $(event.relatedTarget).data("user-id");
        if (typeof uid !== "undefined") {
            document.getElementById('regSubmit').name = "updateReg";
            document.getElementById('regSubmit').value = "update";
            document.getElementById('user_id').value = uid;
            // console.log(uid);
            // var userForm = document.getElementById('RegistrationForm');
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/validation.php',
                data: { uid: uid },
                dataType: 'json',
                success: function (response) {
                    if (!response.error) {
                        $('#username').val(response.username);
                        $('#username').prop('readonly', true);
                        $('#password-strength').html("<span class='text-success'>Leave empty to keep the current password</span>");
                        $("#password").attr("required", false);
                        $('#user_role').val(response.user_role);
                        $('#user_role').prop('readonly', true);
                        $('#updateReg').removeClass('d-none');
                        <?php if ($user->isadmin()) {
                            echo "
                                $('#deleteReg').removeClass('d-none');
                                $('#user_status').val(response.user_status);
                            ";
                        } ?>
                        $('#register').addClass('d-none');
                        if (response.user_role == 0 || response.user_role == 1 || response.user_role == 2) {
                            $('#lecr_nic').val(response.lecr_nic);
                            $('#lecr_mobile').val(response.lecr_mobile);
                            $('#lecr_email').val(response.lecr_email);
                            $('#lecr_name').val(response.lecr_name);
                            $('#lecr_address').val(response.lecr_address);
                            console.log(response.lecr_gender + "For debug Gender");
                            $('#lecr_gender').val(response.lecr_gender);
                            $('#lecr_profile_pic').prop('required', false);
                            $('#lecr_profile_error').html("<span class='text-success'>Leave empty to keep the current profile picture</span>");
                            $('#lecr_profile_error').removeClass('text-danger');
                            $('#lecr_profile_error').addClass('text-success');
                            $('#lecr_fields').removeClass('d-none');
                            $('#std_fields').addClass('d-none');
                        } else if (response.user_role == 3) {
                            $('#std_index').val(response.std_index);
                            $('#std_fullname').val(response.std_fullname);
                            $('#mobile_tp_no').val(response.mobile_tp_no);
                            $('#home_tp_no').val(response.home_tp_no);
                            $('#std_email').val(response.std_email);
                            $('#current_address').val(response.current_address);
                            $('#permanent_address').val(response.permanent_address);
                            $('#std_nic').val(response.std_nic);
                            $('#std_dob').val(response.std_dob);
                            // console.log(response.std_gender + "For debug Gender");
                            $('#std_gender').val(response.std_gender);
                            $('#std_batchno').val(response.std_batchno);
                            $('#date_admission').val(response.date_admission);
                            $('#current_level').val(response.current_level);
                            $('#std_index').prop('readonly', true);

                            $('#std_profile_pic').prop('required', false);
                            $('#std_profile_error').html("<span class='text-success'>Leave empty to keep the current profile picture</span>");
                            $('#std_profile_error').removeClass('text-danger');
                            $('#std_profile_error').addClass('text-success');
                            $('#std_profile_error').removeClass('d-none');
                            $('#std_fields').removeClass('d-none');
                            $('#lecr_fields').addClass('d-none');
                        }
                        <?php if (!$user->isAdmin())
                            echo "
                                $('#lecr_nic').prop('readonly', true);
                                $('#lecr_mobile').prop('readonly', true);
                                $('#lecr_email').prop('readonly', true);
                                $('#std_email').prop('readonly', true);
                                $('#std_nic').prop('readonly', true);
                                $('#mobile_tp_no').prop('readonly', true);
                                ";
                        ?>
                    } else {
                        console.log(response.error);
                    }
                },
                error: function () {
                    console.log('error');
                }
            });
        } else {
            console.log("no data");
        }
    });

    // Execute submission with register button with validation
    function exeSubmit(id) {
        // for user registration
        if (id == 'RegistrationForm') {
            const username = document.getElementById("username");
            const password = document.getElementById("password").value;
            const confirm_password = document.getElementById("confirm_password").value;
            const user_role = document.getElementById('user_role').value;
            const maxSizeInBytes = 1024 * 1024; //200 KB
            var goFlag = true;
            var errors = [];

            if (user_role == '0' || user_role == '1' || user_role == '2') {
                const lecr_nic = document.getElementById('lecr_nic');
                if (lecr_nic.value.length != 10 && lecr_nic.value.length != 12) {
                    errors.push('Invalid NIC');
                    goFlag = false;
                }
                const lecr_mobile = document.getElementById('lecr_mobile');
                if (lecr_mobile.value.length != 10) {
                    errors.push('Invalid Mobile Number');
                    goFlag = false;
                }
                const lecr_email = document.getElementById('lecr_email');
                if (!lecr_email.value.includes('@') || !lecr_email.value.includes('.')) {
                    errors.push('Invalid Email');
                    goFlag = false;
                }
                const lecr_name = document.getElementById('lecr_name');
                if (lecr_name.value.length < 5) {
                    errors.push('Invalid Name');
                    goFlag = false;
                }
                const profile = document.getElementById('lecr_profile_pic');
                if (profile.files.length > 0 && profile.files[0].size > maxSizeInBytes) {
                    errors.push('Upload a picture less than 1024KB');
                    goFlag = false;
                }

            } else if (user_role == '3') {
                const profile = document.getElementById('std_profile_pic');
                if (profile.files.length > 0 && profile.files[0].size > maxSizeInBytes) {
                    errors.push('Upload a picture less than 1024KB');
                    goFlag = false;
                }
                const std_nic = document.getElementById('std_nic');
                if (std_nic.value.length != 10 && std_nic.value.length != 12) {
                    errors.push('Invalid NIC');
                    goFlag = false;
                }
                const std_mobile = document.getElementById('mobile_tp_no');
                if (std_mobile.value.length != 10) {
                    errors.push('Invalid Mobile Number');
                    goFlag = false;
                }
                const std_email = document.getElementById('std_email');
                if (!std_email.value.includes('@') || !std_email.value.includes('.')) {
                    errors.push('Invalid Email');
                    goFlag = false;
                }
                const std_name = document.getElementById('std_fullname');
                if (std_name.value.length < 5) {
                    errors.push('Invalid Name');
                    goFlag = false;
                }
                const std_index = document.getElementById('std_index');
                if (std_index.value.length < 5) {
                    errors.push('Invalid Index Number');
                    goFlag = false;
                }
            } else {
                errors.push('Invalid User Role');
                goFlag = false;
            }

            if (password !== confirm_password || password.length < 8) {
                errors.push('Password should be at least 8 chars and matched!');
                goFlag = false;
            }

            if (goFlag) {
                document.getElementById(id).submit();
            } else {
                var errorLog = document.getElementById("error-log");
                errorLog.classList.remove("d-none");
                var errorList = document.getElementById("error-list");
                errorList.innerHTML = "";
                errors.forEach(element => {
                    errorList.innerHTML += "<li>" + element + "</li>";
                });
            }
        } else {
            document.getElementById(id).submit();
        }
    }

    // execute submission for update records
    function updateRec(formId) {
        // for user update dialog
        if (formId == 'RegistrationForm') {
            // const username = document.getElementById("username");
            const password = document.getElementById("password").value;
            const confirm_password = document.getElementById("confirm_password").value;
            const user_role = document.getElementById('user_role').value;
            const maxSizeInBytes = 1024 * 1024; //200 KB
            var goFlag = true;
            var errors = [];
            console.log(password);
            if (password != null && password != "") {
                if (password !== confirm_password || password.length < 8) {
                    errors.push('Password should be at least 8 chars and matched!');
                    goFlag = false;
                }
            }
            if (user_role == '0' || user_role == '1' || user_role == '2') {
                const lecr_nic = document.getElementById('lecr_nic');
                if (lecr_nic.value.length != 10 && lecr_nic.value.length != 12) {
                    errors.push('Invalid NIC');
                    goFlag = false;
                }
                const lecr_mobile = document.getElementById('lecr_mobile');
                if (lecr_mobile.value.length != 10) {
                    errors.push('Invalid Mobile Number');
                    goFlag = false;
                }
                const lecr_email = document.getElementById('lecr_email');
                if (!lecr_email.value.includes('@') || !lecr_email.value.includes('.')) {
                    errors.push('Invalid Email');
                    goFlag = false;
                }
                const lecr_name = document.getElementById('lecr_name');
                if (lecr_name.value.length < 5) {
                    errors.push('Invalid Name');
                    goFlag = false;
                }
                const profile = document.getElementById('lecr_profile_pic');
                if (profile.files.length > 0 && profile.files[0].size > maxSizeInBytes) {
                    errors.push('Upload a picture less than 1024KB');
                    goFlag = false;
                }

            } else if (user_role == '3') {

                const profile = document.getElementById('std_profile_pic');
                if (profile.files.length > 0 && profile.files[0].size > maxSizeInBytes) {
                    errors.push('Upload a picture less than 1024KB');
                    goFlag = false;
                }
                const std_nic = document.getElementById('std_nic');
                if (std_nic.value.length != 10 && std_nic.value.length != 12) {
                    errors.push('Invalid NIC');
                    goFlag = false;
                }
                const std_mobile = document.getElementById('mobile_tp_no');
                if (std_mobile.value.length != 10) {
                    errors.push('Invalid Mobile Number');
                    goFlag = false;
                }
                const std_email = document.getElementById('std_email');
                if (!std_email.value.includes('@') || !std_email.value.includes('.')) {
                    errors.push('Invalid Email');
                    goFlag = false;
                }
                const std_name = document.getElementById('std_fullname');
                if (std_name.value.length < 5) {
                    errors.push('Invalid Name');
                    goFlag = false;
                }
            }

            if (goFlag) {
                document.getElementById(formId).submit();
            } else {
                var errorLog = document.getElementById("error-log");
                errorLog.classList.remove("d-none");
                var errorList = document.getElementById("error-list");
                errorList.innerHTML = "";
                errors.forEach(element => {
                    errorList.innerHTML += "<li>" + element + "</li>";
                });
            }
        }
    }

    // execute submission for delete records
    function deleteRec(formId) {
        if (formId == 'RegistrationForm') {
            document.getElementById('regSubmit').name = "deleteReg";
            document.getElementById('regSubmit').value = "delete";
            var cfm = confirm('You sure you want to delete?');
            if (cfm) {
                document.getElementById(formId).submit();
            }
        }
    }
    function toggleFields() {
        var userType = document.getElementById("user_role").value;
        var studentFields = document.getElementById("std_fields");
        var lecturerFields = document.getElementById("lecr_fields");

        studentFields.classList.add("d-none");
        lecturerFields.classList.add("d-none");

        if (userType == 3) {
            document.getElementById('std_index').required = true;
            document.getElementById('std_fullname').required = true;
            document.getElementById('std_nic').required = true;
            document.getElementById('std_email').required = true;
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

    function userAvailabilityCheck() {
        var username = document.getElementById("username").value;
        var message = document.getElementById("availability_message");
        // Send an AJAX request to check the username availability
        if (username.length > 5) {
            $.ajax({
                type: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/validation.php',
                data: { username: username },
                dataType: 'json',
                success: function (response) {
                    if (response.available) {
                        message.textContent = "Username is available";
                        message.style.color = "green";

                    } else {
                        message.textContent = "Username is already taken";
                        message.style.color = "red";

                    }
                }
            });

        } else {
            message.textContent = "Invalid Username!";
            message.style.color = "red";
        }
    }

    document.getElementById('std_profile_pic').addEventListener('change', function () {
        const maxSizeInBytes = 1024 * 1024; // 1024KB
        const file = this.files[0];
        if (file && file.size > maxSizeInBytes) {
            const error_label = document.getElementById('std_profile_error');
            error_label.textContent = "File size is too large";
            error_label.addClass = "text-danger";
        } else {
            const error_label = document.getElementById('std_profile_error');
            error_label.textContent = "";
        }
    });

    document.getElementById('lecr_profile_pic').addEventListener('change', function () {
        const maxSizeInBytes = 1024 * 1024; // 1024KB
        const file = this.files[0];
        if (file && file.size > maxSizeInBytes) {
            const error_label = document.getElementById('lecr_profile_error');
            error_label.textContent = "File size is too large";
            error_label.addClass = "text-danger";
        } else {
            const error_label = document.getElementById('lecr_profile_error');
            error_label.textContent = "";
        }
    });

</script>