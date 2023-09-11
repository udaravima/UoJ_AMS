SELECT 
    uoj_user.user_id,
    lecr.lecr_name,
    lecr.lecr_mobile,
    lecr.lecr_email,
    CASE
        WHEN lecr.lecr_gender = 0 THEN 'Male'
        ELSE 'Female'
    END as lecr_gender,
    lecr.lecr_profile_pic,
    CASE
        WHEN uoj_user.user_role = 0 THEN 'Administrator'
        WHEN uoj_user.user_role = 1 THEN 'Lecturer'
        WHEN uoj_user.user_role = 2 THEN 'Instructor'
        WHEN uoj_user.user_role = 3 THEN 'Student'
    END as user_role,
    CASE
        WHEN uoj_user.user_status = 0 THEN 'InActive'
        WHEN uoj_user.user_status = 1 THEN 'Active'
        WHEN uoj_user.user_status = 2 THEN 'Pending'
        ELSE 'Unokown'
    END as user_status
FROM
    uoj_lecturer AS lecr
        INNER JOIN
    uoj_user ON uoj_user.user_id = lecr.user_id;
    
SELECT 
    *
FROM
    uoj_user;