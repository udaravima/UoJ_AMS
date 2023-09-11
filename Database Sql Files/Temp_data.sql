INSERT INTO uoj_user (
        username,
        user_password,
        user_salt,
        user_status,
        user_role
    )
VALUES ('admin', 'admin_password', 'admin_salt', 1, 0),
    ('lecturer1', 'lec1_password', 'lec1_salt', 1, 1),
    ('student1', 'std1_password', 'std1_salt', 2, 3);

INSERT INTO uoj_student (
        std_index,
        std_regno,
        std_fullname,
        std_gender,
        std_batchno,
        dgree_program,
        std_subjectcomb,
        std_nic,
        std_dob,
        date_admission,
        current_address,
        permanent_address,
        mobile_tp_no,
        home_tp_no,
        std_email,
        std_profile_pic,
        current_level,
        user_id
    )
VALUES (
        'S11266',
        '2020CSC050',
        'John Doe',
        0,
        'Batch2020',
        'Computer Science',
        'Maths',
        '123456789012',
        '2000-01-01',
        '2020-09-01',
        '123 Main St',
        '456 Park Ave',
        '1234567890',
        '9876543210',
        'john@example.com',
        'profile_pic1.jpg',
        '01',
        3
    );

INSERT INTO uoj_lecturer (
        lecr_nic,
        lecr_name,
        lecr_mobile,
        lecr_email,
        lecr_gender,
        lecr_address,
        lecr_profile_pic,
        user_id
    )
VALUES (
        '123456789012',
        'Jane Smith',
        '9876543210',
        'jane@example.com',
        1,
        '789 Oak St',
        'profile_pic2.jpg',
        2
    );

INSERT INTO uoj_course (course_code, course_name)
VALUES ('CSC101', 'Introduction to Computer Science'),
    ('MAT201', 'Advanced Mathematics'),
    ('ENG301', 'English Composition');

INSERT INTO uoj_class (
        lecr_id,
        course_id,
        class_date,
        start_time,
        end_time
    )
VALUES (2, 1, '2023-08-01', '09:00:00', '11:00:00'),
    (2, 2, '2023-08-02', '10:00:00', '12:00:00'),
    (2, 3, '2023-08-03', '11:00:00', '13:00:00');

INSERT INTO uoj_lecturer_course (lecr_id, course_id)
VALUES (2, 1),
    (2, 2);

INSERT INTO uoJ_student_class (std_id, class_id, attend_time, attendance_status)
VALUES (1, 1, '09:15:00', 1),
    (3, 2, '10:30:00', 0),
    (1, 2, '10:30:00', 1),
    (4, 3, '11:30:00', 1);

INSERT INTO uoj_nfc_data (nfc_hash, user_id)
VALUES ('nfc_hash_1', 1),
    ('nfc_hash_2', 2),
    ('nfc_hash_3', 3);