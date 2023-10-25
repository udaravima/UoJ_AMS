<!-- User Details modal -->
<div class="modal fade" id="fetch-user-details" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content rounded shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="record-user-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- User Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="<?php echo $user->getDefaultProfilePic() ?>" class="img-fluid rounded-circle" alt="Profile Picture" id="user-profile-photo">
                            </div>
                            <div class="col-md-8" id="record-user-details">
                                <!-- User details goes here -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Assign Courses search bar-->
                <div class="container mt-3">
                    <div class="input-group mb-3">
                        <input type="search" class="form-control" placeholder="Search Course" aria-label="Search Course" aria-describedby="course-addon" id="search-course" data-bs-toggle="dropdown" data-bs-target="#update-course-list" name="search-course">
                        <div class="input-group-append">
                            <button class="btn btn-secondary rounded btn-group" type="button" id="course-selectAll" onclick="selectAllCourses(this, 'course-select[]')">*</button>
                        </div>
                        <div class="dropdown-menu" aria-labelledby="course-addon" id="update-course-list">
                            <!-- Courses goes here -->
                            <!-- temp example -->

                        </div>
                    </div>
                </div>

                <!-- User Courses -->
                <div class="container mt-3">
                    <table class="table table-striped table-hover border shadow">
                        <h3>Assigned courses</h3>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Course Code</th>
                                <th scope="col">Course Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="record-user-courses">
                            <!-- Current courses goes here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type='button' class='btn btn-warning' data-user-id='' onclick="updateUserCourse(this)" id="record-update-button">Update</button>
                <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#reg_user' data-user-id='' id="record-edit-button">Edit Profile</button>
            </div>
        </div>
    </div>
</div>

<!-- course details Modal-->

<div class="modal fade" id="course-info-card" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content rounded shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="record-course-title">Course Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- User Search -->
                <div class="container mt-3">
                    <div class="input-group mb-3">
                        <input type="search" class="form-control" placeholder="Search User" aria-label="Search Course" aria-describedby="course-addon" id="search-user-for-course" data-bs-toggle="dropdown" data-bs-target="#update-user-list" name="search-user-for-course">
                        <div class="input-group-append">
                            <button class="btn btn-secondary rounded btn-group" type="button" id="user-selectAll" onclick="selectAllUsers(this)">*</button>
                        </div>
                        <div class="dropdown-menu" aria-labelledby="users-addon-course" id="update-user-list">
                            <!-- users goes here -->
                            <!-- wait -->
                        </div>
                    </div>
                </div>
                <!-- Student table -->
                <div class="container mt-3">
                    <!-- Student table -->
                    <table class="table table-striped table-hover border shadow">
                        <h3>Student</h3>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Index</th>
                                <th scope="col">Reg No</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="current-student-course">
                            <!-- Current students for the course goes here -->
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation">
                        <ul class="pageination justify-content-center" id="current-student-page-navigation">
                            <!-- pagination goes here -->
                        </ul>
                    </nav>
                </div>

                <div class="container mt-3">
                    <!-- Lecture table -->
                    <table class="table table-striped table-hover border shadow">
                        <h3>Lecture</h3>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Reg No</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="current-lecture-course">
                            <!-- Current lectures for the course goes here -->
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation">
                        <ul class="pageination justify-content-center" id="current-student-page-navigation">
                            <!-- pagination goes here -->
                        </ul>
                    </nav>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type='button' class='btn btn-warning' data-course-id='' onclick="updateUserForCourse(this)" id="user-update-button">Update</button>
                <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#add_course' data-course-id='' id="course-edit-button">Edit Course</button>
            </div>
        </div>
    </div>
</div>


<script>
    // User card preview
    // global variables
    let addCourseList = [];
    let removeCourseList = [];
    let indexNewCourse = 1;
    document.getElementById('course-info-card').addEventListener('hidden.bs.modal', function(event) {
        document.getElementById('course-edit-button').setAttribute("data-course-id", '');
        document.getElementById('user-update-button').setAttribute("data-course-id", '');
        document.getElementById('current-student-course').innerHTML = "";
        document.getElementById('current-lecture-course').innerHTML = "";
        document.getElementById('search-user-for-course').value = "";
        document.getElementById('search-course').value = "";
        document.getElementById('update-user-list').innerHTML = "";
        document.getElementById('update-course-list').innerHTML = "";
        document.getElementById('course-info-card').setAttribute("data-course-id", '');

        // window.location.href = '<?php //echo SERVER_ROOT; 
                                    ?>/index.php';
    });
    document.getElementById('fetch-user-details').addEventListener('hidden.bs.modal', function(event) {
        document.getElementById('record-user-details').innerHTML = "";
        document.getElementById('record-edit-button').setAttribute("data-user-id", '');
        document.getElementById('record-update-button').setAttribute("data-user-id", '');
        //reset global
        addCourseList = [];
        removeCourseList = [];
        indexNewCourse = 1;
        // window.location.href = '<?php //echo SERVER_ROOT; 
                                    ?>/index.php';
    });

    document.getElementById('search-user-for-course').addEventListener('keyup', function(event) {
        let userSearch = document.getElementById('search-user-for-course').value;
        if (userSearch.length > 4) {
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/validation.php',
                data: {
                    userSearch: userSearch
                },
                dataType: 'json',
                success: function(response) {
                    let userListCourse = $('#update-user-list');
                    userListCourse.empty();
                    (response.users)
                },
                error: function() {
                    sendMessage('Error on loading users', 'danger');
                }
            })
        }
    });

    document.getElementById("search-course").addEventListener("keyup", function(event) {
        // if (event.keyCode === 13) {
        //     // event.preventDefault();
        // }
        // if (event.keyCode === 27) {
        //     // event.preventDefault();
        //     document.getElementById("search-course").value = "";
        // }
        let cids = document.getElementById("search-course").value;
        if (cids.length > 3) {
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/validation.php',
                data: {
                    cids: cids
                },
                dataType: 'json',
                success: function(response) {
                    if (!response.error) {
                        let dropListCourse = $('#update-course-list');
                        dropListCourse.empty();
                        (response.courses).forEach(course => {
                            let data = $(document.createElement("div")).addClass("form-check");
                            data.append($(document.createElement("input")).addClass("form-check-input").attr("type", "checkbox").attr("value", course[0]).attr("id", "filterCourseCheckbox" + course[0]).attr("name", "course-select[]").attr("data-course-code", course[1]).attr("data-course-name", course[2]).attr("onchange", "addCourse(this)"));
                            data.append($(document.createElement("label")).addClass("form-check-label").attr("for", "filterCourseCheckbox" + course[0]).text(course[1] + " - " + course[2]));
                            dropListCourse.append(data);
                        });
                    }
                },
                error: function() {
                    sendMessage('Error on loading courses', 'danger');
                }
            });
        }
    });

    //On modal loading Course Details
    document.getElementById('course-info-card').addEventListener('show.bs.modal', function(event) {
        let courseId = $(event.relatedTarget).data("course-id");
        if (courseId !== "undefined") {
            document.getElementById('course-edit-button').setAttribute("data-course-id", courseId);
            document.getElementById('user-update-button').setAttribute("data-course-id", courseId);
            //Retrieve Course Info
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/validation.php',
                data: {
                    courseId: courseId
                },
                dataType: 'json',
                success: function(response) {
                    if (!response.error) {
                        let currentStudent = $('#current-student-course');
                        let currentLecture = $('#current-lecture-course');
                        currentStudent.empty();
                        currentLecture.empty();
                        document.getElementById('record-course-title').innerHTML = response.courseCode + " - " + response.courseName;
                        let i = 1;
                        (response.students).forEach(student => {
                            let rw = $(document.createElement("tr"));
                            rw.append($(document.createElement("td")).text(i++));
                            rw.append($(document.createElement("td")).text(student[0]));
                            rw.append($(document.createElement("td")).text(student[1]));
                            rw.append($(document.createElement("td")).text(student[2]));
                            rw.append($(document.createElement("td")).append($(document.createElement("button")).addClass("btn btn-danger rounded-pill").attr("type", "button").attr("onclick", "toggleExistStudent(" + student[3] + ")").attr("id", "existCourse" + student[3]).text("Remove")));
                            currentStudent.append(rw);
                        });
                        i = 1;
                        (response.lecturers).forEach(lecture => {
                            let rw = $(document.createElement("tr"));
                            rw.append($(document.createElement("td")).text(i++));
                            rw.append($(document.createElement("td")).text(lecture[0]));
                            rw.append($(document.createElement("td")).text(lecture[1]));
                            rw.append($(document.createElement("td")).append($(document.createElement("button")).addClass("btn btn-danger rounded-pill").attr("type", "button").attr("onclick", "toggleExistLecture(" + lecture[2] + ")").attr("id", "existCourse" + lecture[2]).text("Remove")));
                            currentLecture.append(rw);
                        });
                    } else {
                        sendMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    sendMessage('Course card loading error :Ajax :(', 'danger');
                }
            });
        }
    });
    // features corosponding to User detail card
    // Getting User details on modal loading
    document.getElementById('fetch-user-details').addEventListener('show.bs.modal', function(event) {
        let uid = $(event.relatedTarget).data("user-id");
        if (typeof uid !== "undefined") {
            document.getElementById('record-edit-button').setAttribute("data-user-id", uid);
            document.getElementById('record-update-button').setAttribute("data-user-id", uid);
            //Retrieve User Info
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/validation.php',
                data: {
                    uid: uid
                },
                dataType: 'json',
                success: function(response) {
                    if (!response.error) {
                        $('#user-profile-photo').attr("src", "<?php echo $user->getDefaultProfilePic() ?>");
                        let details = $('#record-user-details');
                        let courses = $('#record-user-courses');
                        details.empty();
                        courses.empty();
                        document.getElementById('record-user-title').innerHTML = response.username;
                        if (response.user_role == 0 || response.user_role == 1 || response.user_role == 2) {
                            if (response.lecr_profile_pic != null) {
                                $('#user-profile-photo').attr("src", response.lecr_profile_pic);
                            }
                            details.append($(document.createElement("h5")).addClass("card-title").text(response.lecr_name));
                            details.append($(document.createElement("p")).addClass("card-text").text("Email: " + response.lecr_email));
                            details.append($(document.createElement("p")).addClass("card-text").text("Phone: " + response.lecr_mobile));
                            details.append($(document.createElement("p")).addClass("card-text").text("Address: " + response.lecr_address));
                            details.append($(document.createElement("p")).addClass("card-text").text("NIC: " + response.lecr_nic));
                            // details.append($(document.createElement("p")).addClass("card-text").text("DOB: " + response.lecr_dob));
                            details.append($(document.createElement("p")).addClass("card-text").text("Gender: " + ((response.lecr_gender == 0) ? "Male" : "Female")));
                        } else if (response.user_role == 3) {
                            if (response.std_profile_pic != null) {
                                $('#user-profile-photo').attr("src", response.std_profile_pic);
                            }
                            details.append($(document.createElement("h5")).addClass("card-title").text(response.std_index + " - " + response.std_shortname));
                            details.append($(document.createElement("p")).addClass("card-text").text("Full Name: " + response.std_fullname));
                            details.append($(document.createElement("p")).addClass("card-text").text("NIC: " + response.std_nic));
                            details.append($(document.createElement("p")).addClass("card-text").text("Gender: " + ((response.std_gender == 0) ? "Male" : "Female")));
                            details.append($(document.createElement("p")).addClass("card-text").text("Email: " + response.std_email));
                            details.append($(document.createElement("p")).addClass("card-text").text("Phone: " + response.mobile_tp_no));
                            details.append($(document.createElement("p")).addClass("card-text").text("Home Mobile: " + response.home_tp_no));
                            details.append($(document.createElement("p")).addClass("card-text").text("Address: " + response.current_address));
                            details.append($(document.createElement("p")).addClass("card-text").text("Home Address: " + response.permanent_address));
                            details.append($(document.createElement("p")).addClass("card-text").text("DOB: " + response.std_dob));
                            details.append($(document.createElement("p")).addClass("card-text").text("Batch: " + response.std_batchno));
                            details.append($(document.createElement("p")).addClass("card-text").text("Admission Date: " + response.date_admission));
                            details.append($(document.createElement("p")).addClass("card-text").text("Current Level: " + response.current_level));
                        }

                        <?php if (!$user->isAdmin()) {
                            echo "$('#record-edit-button').hide();";
                        }
                        ?>

                        let i = 1;
                        (response.courses).forEach(course => {
                            let rw = $(document.createElement("tr"));
                            rw.append($(document.createElement("td")).text(i++));
                            rw.append($(document.createElement("td")).text(course[3]));
                            rw.append($(document.createElement("td")).text(course[4]));
                            rw.append($(document.createElement("td")).append($(document.createElement("button")).addClass("btn btn-danger rounded-pill").attr("type", "button").attr("onclick", "toggleExistCourse(" + course[1] + ")").attr("id", "existCourse" + course[1]).text("Remove")));
                            courses.append(rw);
                        });
                    } else {
                        sendMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    sendMessage('card loading error Ajax :(', 'danger');
                }
            });

        }
    });

    function addCourse(checkbox) {
        courseCode = checkbox.getAttribute("data-course-code");
        courseName = checkbox.getAttribute("data-course-name");
        if (checkbox.checked) {
            $('#filterCourseCheckbox' + checkbox.value).parent('div').addClass("active");
            if ($('#existCourse' + checkbox.value).length > 0) {
                if (removeCourseList.includes(Number(checkbox.value))) {
                    removeCourseList.splice(removeCourseList.indexOf(Number(checkbox.value)), 1);
                    $('#existCourse' + checkbox.value).removeClass("btn-warning").addClass('btn-danger').text("Remove");
                }
            } else {
                if (!(addCourseList.includes(Number(checkbox.value)))) {
                    addCourseList.push(Number(checkbox.value));
                    let courses = $('#record-user-courses');
                    let rw = $(document.createElement("tr"));
                    rw.append($(document.createElement("td")).text(indexNewCourse++));
                    rw.append($(document.createElement("td")).text(courseCode));
                    rw.append($(document.createElement("td")).text(courseName));
                    rw.append($(document.createElement("td")).append($(document.createElement("button")).addClass("btn btn-warning rounded-pill").attr("type", "button").attr("onclick", "resetCourseCheckBox(" + checkbox.value + ")").attr("id", "newCourse" + checkbox.value).text("+Pending")));
                    courses.append(rw);
                }
            }
        } else {
            $('#filterCourseCheckbox' + checkbox.value).parent('div').removeClass("active");
            if (addCourseList.includes(Number(checkbox.value))) {
                addCourseList.splice(addCourseList.indexOf(Number(checkbox.value)), 1);
            }
            if ($('#existCourse' + checkbox.value).length > 0) {
                if (!(removeCourseList.includes(Number(checkbox.value)))) {
                    removeCourseList.push(Number(checkbox.value));
                    $('#existCourse' + checkbox.value).removeClass("btn-danger").addClass('btn-warning').text("-Pending");
                }
            }
            if ($('#newCourse' + checkbox.value).length > 0) {
                $('#newCourse' + checkbox.value).closest('tr').remove();
                indexNewCourse--;
            }
        }
    }

    function resetCourseCheckBox(cid) {
        cid = Number(cid);
        if (addCourseList.includes(cid)) {
            addCourseList.splice(addCourseList.indexOf(cid), 1);
            $('#newCourse' + cid).closest('tr').remove();
            indexNewCourse--;
        }
        if ($('#filterCourseCheckbox' + cid).length > 0) {
            $('#filterCourseCheckbox' + cid).prop('checked', false);
            $('#filterCourseCheckbox' + cid).parent('div').removeClass("active");
        }
    }

    function toggleExistCourse(cid) {
        cid = Number(cid);
        if (removeCourseList.includes(cid)) {
            removeCourseList.splice(removeCourseList.indexOf(cid), 1);
            $('#existCourse' + cid).removeClass("btn-warning").addClass('btn-danger').text("Remove");
        } else {
            if ($('#filterCourseCheckbox' + cid).length > 0) {
                $('#filterCourseCheckbox' + cid).prop('checked', false);
                $('#filterCourseCheckbox' + cid).parent('div').removeClass("active");
            }
            removeCourseList.push(cid);
            $('#existCourse' + cid).removeClass("btn-danger").addClass('btn-warning').text("-Pending");
        }
    }

    function updateUserCourse(button) {
        let userid = button.getAttribute("data-user-id");
        if (addCourseList.length > 0 || removeCourseList.length > 0) {
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/validation.php',
                data: {
                    userid: userid,
                    addCourseList: addCourseList,
                    removeCourseList: removeCourseList
                },
                dataType: 'json',
                success: function(response) {
                    $('#messageModalBody').empty();
                    if (response.errors.length > 0) {
                        let errorsRw = $(document.createElement("div")).addClass("alert").addClass("alert-danger");
                        let dataList = $(document.createElement("ul"));
                        (response.errors).forEach(error => {
                            dataList.append($(document.createElement("li")).text(error));
                        });
                        errorsRw.append(dataList);
                        $('#messageModalBody').append(errorsRw);
                    }
                    if (response.messages.length > 0) {
                        let messageRw = $(document.createElement("div")).addClass("alert").addClass("alert-success");
                        let dataList = $(document.createElement("ul"));
                        (response.messages).forEach(message => {
                            dataList.append($(document.createElement("li")).text(message));
                        });
                        messageRw.append(dataList);
                        $('#messageModalBody').append(messageRw);
                    }

                    $('#messageModal').modal('show');
                    $('#messageModal').on('hidden.bs.modal', function(e) {
                        window.location.reload();
                    });
                },
                error: function() {
                    sendMessage('error on Update :(', 'danger');
                }
            });
        }
    }

    function selectAllCourses(button, selectName) {
        let checkboxes = document.getElementsByName(selectName);
        if (button.classList.contains('btn-secondary')) {
            for (let i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                    addCourse(checkboxes[i]);
                }
            }
            this.classList.remove('btn-secondary').addClass('btn-primary');
        } else {
            for (let i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                    addCourse(checkboxes[i]);
                }
            }
            this.classList.remove('btn-primary').addClass('btn-secondary');
        }
    }
</script>