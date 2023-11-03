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
                        <div class="dropdown-menu" style="overflow:hidden auto; max-height:245px;" aria-labelledby="course-addon" id="update-course-list">
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
                        <input type="search" class="form-control" placeholder="Search User" aria-label="Search Course" aria-describedby="course-addon" id="search-user-for-course" data-bs-toggle="dropdown" data-bs-target="#course-assigned-user" name="search-user-for-course">
                        <div class="input-group-append">
                            <button class="btn btn-secondary rounded btn-group" type="button" id="user-selectAll" onclick="selectAllUsers(this, 'student-list[]')">Students</button>
                            <button class="btn btn-secondary rounded btn-group" type="button" id="user-selectAll" onclick="selectAllUsers(this, 'lecturer-list[]')">Lecturer</button>
                        </div>
                        <div class="dropdown-menu" aria-labelledby="users-addon-course" style="overflow:hidden auto; max-height:245px;" id="course-assigned-user">
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
    let addStudentList = [];
    let removeStudentList = [];
    let indexNewStudent = 1;
    let addLectureList = [];
    let removeLectureList = [];
    let indexNewLecture = 1;



    document.getElementById('course-info-card').addEventListener('hidden.bs.modal', function(event) {
        document.getElementById('course-edit-button').setAttribute("data-course-id", '');
        document.getElementById('user-update-button').setAttribute("data-course-id", '');
        document.getElementById('current-student-course').innerHTML = "";
        document.getElementById('current-lecture-course').innerHTML = "";
        document.getElementById('search-user-for-course').value = "";
        document.getElementById('search-course').value = "";
        document.getElementById('course-assigned-user').innerHTML = "";
        document.getElementById('update-course-list').innerHTML = "";
        document.getElementById('course-info-card').setAttribute("data-course-id", '');
        addStudentList = [];
        removeStudentList = [];
        indexNewStudent = 1;
        addLectureList = [];
        removeLectureList = [];
        indexNewLecture = 1;

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
        if (userSearch.length > 3) {
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/validation.php',
                data: {
                    userSearch: userSearch
                },
                dataType: 'json',
                success: function(response) {
                    let userListCourse = $('#course-assigned-user');
                    userListCourse.empty();
                    // let optionGroupStd = $(document.createElement("optgroup")).attr("label", "Student");
                    // let optionGroupLecr = $(document.createElement("optgroup")).attr("label", "Lecture");
                    userListCourse.append($(document.createElement("div")).addClass("dropdown-header").text("Student"));
                    (response.students).forEach(student => {
                        let data = $(document.createElement("div")).addClass("form-check").addClass("dropdown-item");
                        data.append($(document.createElement("input")).addClass("form-check-input").attr("value", student[0]).attr("type", "checkbox").attr("id", "filterStdCheckbox" + student[0]).attr("name", "student-list[]").attr("data-user-role", 3).attr("data-user-name", student[2]).attr("data-user-index", student[1]).attr("data-user-reg", student[18]).attr("onchange", "addUserToCourse(this)"));
                        data.append($(document.createElement("label")).addClass("form-check-label").attr("for", "filterStdCheckbox" + student[0]).text(student[1] + " - " + student[18]));
                        userListCourse.append(data);
                    });
                    userListCourse.append($(document.createElement("div")).addClass("dropdown-divider"));
                    userListCourse.append($(document.createElement("div")).addClass("dropdown-header").text("Lecturer"));
                    (response.lecturers).forEach(lecture => {
                        let data = $(document.createElement("div")).addClass("form-check").addClass("dropdown-item");
                        data.append($(document.createElement("input")).addClass("form-check-input").attr("value", lecture[0]).attr("type", "checkbox").attr("id", "filterLecrCheckbox" + lecture[0]).attr("name", "lecturer-list[]").attr("data-user-role", 1).attr("data-user-name", lecture[2]).attr("data-user-reg", lecture[11]).attr("onchange", "addUserToCourse(this)"));
                        data.append($(document.createElement("label")).addClass("form-check-label").attr("for", "filterLecrCheckbox" + lecture[0]).text(lecture[11] + " - " + lecture[2]));
                        userListCourse.append(data);
                    });
                    $('#course-assigned-user').selectpicker('refresh');

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
                            rw.append($(document.createElement("td")).append($(document.createElement("button")).addClass("btn btn-danger rounded-pill").attr("type", "button").attr("onclick", "toggleExistStudent(" + student[3] + ")").attr("id", "existCourseStd" + student[3]).text("Remove")));
                            currentStudent.append(rw);
                        });
                        i = 1;
                        (response.lecturers).forEach(lecture => {
                            let rw = $(document.createElement("tr"));
                            rw.append($(document.createElement("td")).text(i++));
                            rw.append($(document.createElement("td")).text(lecture[0]));
                            rw.append($(document.createElement("td")).text(lecture[1]));
                            rw.append($(document.createElement("td")).append($(document.createElement("button")).addClass("btn btn-danger rounded-pill").attr("type", "button").attr("onclick", "toggleExistLecture(" + lecture[2] + ")").attr("id", "existCourseLecr" + lecture[2]).text("Remove")));
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

    function selectAllUsers(button, selection) {
        let checkboxes = document.getElementsByName(selection);
        if (button.classList.contains('btn-secondary')) {
            for (let i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = true;
                addUserToCourse(checkboxes[i]);
            }
            button.classList.remove('btn-secondary');
            button.classList.add('btn-primary');

        } else {
            for (let i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = false;
                addUserToCourse(checkboxes[i]);
            }
            button.classList.remove('btn-primary');
            button.classList.add('btn-secondary');
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
            button.classList.remove('btn-secondary');
            button.classList.add('btn-primary');
        } else {
            for (let i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                    addCourse(checkboxes[i]);
                }
            }
            button.classList.remove('btn-primary');
            button.classList.add('btn-secondary');
        }
    }


    function addUserToCourse(checkbox) {
        let userRole = checkbox.getAttribute("data-user-role");
        let userId = checkbox.value;
        let userName = checkbox.getAttribute("data-user-name");
        let userReg = checkbox.getAttribute("data-user-reg");
        if (userRole < 3) {
            if (checkbox.checked) {
                $('#filterLecrCheckbox' + userId).parent('div').addClass("active");
                if ($('#existCourseLecr' + userId).length > 0) {
                    if (removeLectureList.includes(Number(userId))) {
                        removeLectureList.splice(removeLectureList.indexOf(Number(userId)), 1);
                        $('#existCourseLecr' + userId).removeClass("btn-warning").addClass('btn-danger').text("Remove");
                    }
                } else {
                    if (!(addLectureList.includes(Number(userId)))) {
                        addLectureList.push(Number(userId));
                        let lectures = $('#current-lecture-course');
                        let rw = $(document.createElement("tr"));
                        rw.append($(document.createElement("td")).text(indexNewLecture++));
                        rw.append($(document.createElement("td")).text(userReg));
                        rw.append($(document.createElement("td")).text(userName));
                        rw.append($(document.createElement("td")).append($(document.createElement("button")).addClass("btn btn-warning rounded-pill").attr("type", "button").attr("onclick", "resetLectureCheckBox(" + userId + ")").attr("id", "newLecture" + userId).text("+Pending")));
                        lectures.append(rw);
                    }
                }
            } else {
                $('#filterLecrCheckbox' + userId).parent('div').removeClass("active");
                if (addLectureList.includes(Number(userId))) {
                    addLectureList.splice(addLectureList.indexOf(Number(userId)), 1);
                }
                if ($('#existCourseLecr' + userId).length > 0) {
                    if (!(removeLectureList.includes(Number(userId)))) {
                        removeLectureList.push(Number(userId));
                        $('#existCourseLecr' + userId).removeClass("btn-danger").addClass('btn-warning').text("-Pending");
                    }
                }
                if ($('#newLecture' + userId).length > 0) {
                    $('#newLecture' + userId).closest('tr').remove();
                    indexNewLecture--;
                }
            }
        } else if (userRole == 3) {
            let index = checkbox.getAttribute("data-user-index");
            if (checkbox.checked) {
                $('#filterStdCheckbox' + userId).parent('div').addClass("active");
                if ($('#existCourseStd' + userId).length > 0) {
                    if (removeStudentList.includes(Number(userId))) {
                        removeStudentList.splice(removeStudentList.indexOf(Number(userId)), 1);
                        $('#existCourseStd' + userId).removeClass("btn-warning").addClass('btn-danger').text("Remove");
                    }
                } else {
                    if (!(addStudentList.includes(Number(userId)))) {
                        addStudentList.push(Number(userId));
                        let students = $('#current-student-course');
                        let rw = $(document.createElement("tr"));
                        rw.append($(document.createElement("td")).text(indexNewStudent++));
                        rw.append($(document.createElement("td")).text(index));
                        rw.append($(document.createElement("td")).text(userReg));
                        rw.append($(document.createElement("td")).text(userName));
                        rw.append($(document.createElement("td")).append($(document.createElement("button")).addClass("btn btn-warning rounded-pill").attr("type", "button").attr("onclick", "resetStudentCheckBox(" + userId + ")").attr("id", "newStudent" + userId).text("+Pending")));
                        students.append(rw);
                    }
                }
            } else {
                $('#filterStdCheckbox' + userId).parent('div').removeClass("active");
                if (addStudentList.includes(Number(userId))) {
                    addStudentList.splice(addStudentList.indexOf(Number(userId)), 1);
                }
                if ($('#existCourseStd' + userId).length > 0) {
                    if (!(removeStudentList.includes(Number(userId)))) {
                        removeStudentList.push(Number(userId));
                        $('#existCourseStd' + userId).removeClass("btn-danger").addClass('btn-warning').text("-Pending");
                    }
                }
                if ($('#newStudent' + userId).length > 0) {
                    $('#newStudent' + userId).closest('tr').remove();
                    indexNewStudent--;
                }
            }
        }
    }

    function resetStudentCheckBox(sid) {
        sid = Number(sid);
        if (addStudentList.includes(sid)) {
            addStudentList.splice(addStudentList.indexOf(sid), 1);
            $('#newStudent' + sid).closest('tr').remove();
            indexNewStudent--;
        }
        if ($('#filterStdCheckbox' + sid).length > 0) {
            $('#filterStdCheckbox' + sid).prop('checked', false);
            $('#filterStdCheckbox' + sid).parent('div').removeClass("active");
        }
    }

    function resetLectureCheckBox(lid) {
        lid = Number(lid);
        if (addLectureList.includes(lid)) {
            addLectureList.splice(addLectureList.indexOf(lid), 1);
            $('#newLecture' + lid).closest('tr').remove();
            indexNewLecture--;
        }
        if ($('#filterLecrCheckbox' + lid).length > 0) {
            $('#filterLecrCheckbox' + lid).prop('checked', false);
            $('#filterLecrCheckbox' + lid).parent('div').removeClass("active");
        }
    }

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

    function toggleExistStudent($stdId) {
        $stdId = Number($stdId);
        if (removeStudentList.includes($stdId)) {
            removeStudentList.splice(removeStudentList.indexOf($stdId), 1);
            $('#existCourseStd' + $stdId).removeClass("btn-warning").addClass('btn-danger').text("Remove");
        } else {
            removeStudentList.push($stdId);
            $('#existCourseStd' + $stdId).removeClass("btn-danger").addClass('btn-warning').text("-Pending");
        }
    }

    function toggleExistLecture($lecrId) {
        $lecrId = Number($lecrId);
        if (removeLectureList.includes($lecrId)) {
            removeLectureList.splice(removeLectureList.indexOf($lecrId), 1);
            $('#existCourseLecr' + $lecrId).removeClass("btn-warning").addClass('btn-danger').text("Remove");
        } else {
            removeLectureList.push($lecrId);
            $('#existCourseLecr' + $lecrId).removeClass("btn-danger").addClass('btn-warning').text("-Pending");
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

    function updateUserForCourse(button) {
        let courseUserId = button.getAttribute("data-course-id");
        
        if (addStudentList.length > 0 || removeStudentList.length > 0 || addLectureList.length > 0 || removeLectureList.length > 0) {
            $.ajax({
                method: 'POST',
                url: '<?php echo SERVER_ROOT; ?>/php/validation.php',
                data: {
                    courseUserId: courseUserId,
                    addStudentList: addStudentList,
                    removeStudentList: removeStudentList,
                    addLectureList: addLectureList,
                    removeLectureList: removeLectureList
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
</script>