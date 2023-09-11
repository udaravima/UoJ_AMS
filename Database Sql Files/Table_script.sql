USE UoJ;
CREATE TABLE uoj_user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    user_password CHAR(60) NOT NULL,
    -- 60 Bytes Check!
    user_salt CHAR(32) NOT NULL UNIQUE, -- Use bin2hex(random_bytes(16)) each byte represet 2 hex chars
    -- check num of chars use with bin2hex(random_bytes(16))
    user_status TINYINT(1) NOT NULL DEFAULT 2,
    -- 0:Inactive, 1:active, 2:pending
    user_role TINYINT(1) NOT NULL,
    -- 0:Administrator, 1:Lecturer, 2:Instructor, 3:Student
    user_session TINYINT(1) DEFAULT 0 -- To check whether user is already logged in or not! May not need
    -- salt comming
);
CREATE TABLE uoj_student (
    std_id INT AUTO_INCREMENT,
    std_index CHAR(7) NOT NULL UNIQUE,
    -- S 11266
    std_regno VARCHAR(11) NOT NULL UNIQUE,
    -- 2020CSC050
    std_fullname VARCHAR(100),
    std_gender TINYINT(1),
    std_batchno VARCHAR(10),
    dgree_program VARCHAR(20),
    std_subjectcomb VARCHAR(15),
    std_nic VARCHAR(12),
    std_dob DATE,
    date_admission DATE,
    current_address VARCHAR(50),
    permanent_address VARCHAR(50),
    mobile_tp_no CHAR(10) UNIQUE,
    home_tp_no CHAR(10),
    std_email VARCHAR(50) UNIQUE,
    std_profile_pic VARCHAR(30) UNIQUE,
    current_level CHAR(2),
    user_id INT,
    PRIMARY KEY(std_id, user_id),
    FOREIGN KEY (user_id) REFERENCES uoj_user (user_id) ON DELETE CASCADE
);
CREATE TABLE uoj_lecturer (
    lecr_id INT AUTO_INCREMENT,
    lecr_nic VARCHAR(12) UNIQUE,
    lecr_name VARCHAR(100),
    lecr_mobile CHAR(10) UNIQUE,
    lecr_email VARCHAR(30) UNIQUE,
    lecr_gender TINYINT(1),
    lecr_address VARCHAR(50),
    lecr_profile_pic VARCHAR(30) UNIQUE,
    user_id INT,
    PRIMARY key(lecr_id, user_id),
    FOREIGN KEY (user_id) REFERENCES uoj_user (user_id) ON DELETE CASCADE
);
CREATE TABLE uoj_course (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(10) UNIQUE,
    course_name VARCHAR(50)
);
CREATE TABLE uoj_class (
    class_id INT AUTO_INCREMENT,
    lecr_id INT,
    course_id INT,
    class_date DATE,
    start_time TIME,
    end_time TIME,
    PRIMARY key(class_id, lecr_id, course_id),
    FOREIGN Key (course_id) REFERENCES uoj_course (course_id),
    FOREIGN Key (lecr_id) REFERENCES uoj_lecturer (lecr_id)
);
CREATE TABLE uoj_lecturer_course (
    lecr_id INT,
    course_id INT,
    PRIMARY KEY (lecr_id, course_id),
    FOREIGN KEY (lecr_id) REFERENCES uoj_lecturer (lecr_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES uoj_course (course_id) ON DELETE CASCADE
);
CREATE TABLE uoJ_student_class (
    std_id INT,
    class_id INT,
    attend_time TIME,
    attendance_status TINYINT(1),
    PRIMARY KEY (std_id, class_id),
    FOREIGN KEY (std_id) REFERENCES uoj_student (std_id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES uoj_class (class_id) ON DELETE CASCADE
);
CREATE TABLE uoj_nfc_data (
    nfc_id INT AUTO_INCREMENT,
    nfc_hash VARCHAR(100) UNIQUE,
    user_id INT,
    PRIMARY KEY(nfc_id, user_id),
    FOREIGN KEY (user_id) REFERENCES uoj_user (user_id) ON DELETE CASCADE
);
CREATE TABLE uoj_student_course(
    std_id INT,
    course_id INT,
    PRIMARY KEY(std_id, course_id),
    FOREIGN key (std_id) REFERENCES uoj_student(std_id) ON DELETE CASCADE,
    FOREIGN KEY(course_id) REFERENCES uoj_course(course_id) ON DELETE CASCADE
);
/*
 users has to be asignned by administrator (admin could be a lecture or a independant account without goining to any)
 user_status: -- 0:inactive, 1:active
 user_role:  0:Administrator, 1:Lecturer, 2:Instructor, 3:Student
 user_sign: To check whether user is logged in or not!
 std_gender: 0:Male, 1:Female
 lecr_gender: 0:Male, 1:Female
 attendance_status: 0: absent, 1: present, 2:late
 User could be from students or lectures or independant account
 lecturer will assign to multiple courses and single course can be teach by multiple lecturers
 lecturers will conduct classes and signle class held by one lecturer for a single course unit
 student will have many classes and each class may have many students
 */