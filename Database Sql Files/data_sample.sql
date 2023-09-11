-- Insert test data into uoj_user table

INSERT INTO
    uoj_user (
        username,
        user_password,
        user_salt,
        user_status,
        user_role,
        user_session
    )
VALUES (
        'admin',
        'hashed_password_admin',
        'salt_admin',
        1,
        0,
        0
    ), (
        'lecturer1',
        'hashed_password_lecturer1',
        'salt_lecturer1',
        1,
        1,
        0
    ), (
        'lecturer2',
        'hashed_password_lecturer2',
        'salt_lecturer2',
        1,
        1,
        0
    ), (
        'instructor1',
        'hashed_password_instructor1',
        'salt_instructor1',
        1,
        2,
        0
    ), (
        'student1',
        'hashed_password_student1',
        'salt_student1',
        1,
        3,
        0
    ), (
        'student2',
        'hashed_password_student2',
        'salt_student2',
        1,
        3,
        0
    );

-- Insert test data into uoj_student table

INSERT INTO
    uoj_student (
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
        'B01',
        'Computer Science',
        'CS-MATH',
        '1234567890A',
        '2000-01-15',
        '2020-09-01',
        '123 Main St',
        '456 Elm St',
        '1234567890',
        '9876543210',
        'john.doe@example.com',
        'profile_pic1.jpg',
        '02',
        5
    ), (
        'S11267',
        '2020CSC051',
        'Jane Smith',
        1,
        'B01',
        'Computer Science',
        'CS-PHYS',
        '0987654321B',
        '1999-11-20',
        '2020-09-01',
        '456 Oak St',
        '789 Maple St',
        '2345678901',
        '8765432109',
        'jane.smith@example.com',
        'profile_pic2.jpg',
        '02',
        6
    );

-- Insert test data into uoj_lecturer table

INSERT INTO
    uoj_lecturer (
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
        '987654321C',
        'Professor Smith',
        '5551234567',
        'prof.smith@example.com',
        0,
        '789 Pine St',
        'lecturer_pic1.jpg',
        2
    ), (
        '123456789A',
        'Dr. Johnson',
        '5559876543',
        'dr.johnson@example.com',
        1,
        '567 Cedar St',
        'lecturer_pic2.jpg',
        3
    );

-- Insert test data into uoj_course table

INSERT INTO
    uoj_course (course_code, course_name)
VALUES (
        'CSC101',
        'Introduction to Computer Science'
    ), ('MAT201', 'Linear Algebra'), (
        'PHY301',
        'Physics for Engineers'
    );

-- Insert test data into uoj_class table

INSERT INTO
    uoj_class (
        lecr_id,
        course_id,
        class_date,
        start_time,
        end_time
    )
VALUES (
        1,
        1,
        '2023-09-05',
        '09:00:00',
        '10:30:00'
    ), (
        2,
        1,
        '2023-09-06',
        '10:00:00',
        '11:30:00'
    ), (
        1,
        2,
        '2023-09-07',
        '13:00:00',
        '14:30:00'
    );

-- Insert test data into uoj_student_course table

INSERT INTO
    uoj_student_course (std_id, course_id)
VALUES (1, 1), (2, 2), (1, 3);