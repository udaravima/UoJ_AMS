# Logic Flow Analysis Report

## Executive Summary
This report analyzes the business logic flows of the `UoJ_AMS` application, focusing on Validation, Integrity, and Security of the workflows.

**Key Findings:**
*   **Critical Logic Flaw in Attendance Marking:** The system relies on the client (browser) to determine if a student is "Present", "Late", or "Absent". This status is trusted blindly by the server.
*   **Missing Uniqueness Checks in Registration:** The application checks for unique Usernames but fails to validate the uniqueness of Student Index Numbers or National Identity Card (NIC) numbers before attempting registration.
*   **No Schedule Conflict Detection:** The system allows creating classes that overlap in time for the same lecturer or course, potentially leading to scheduling conflicts.

---

## 1. Attendance Marking Logic Flow
**Workflow:** Lecturer clicks "Mark" -> JS calculates status -> AJAX -> Backend saves status.

**Current Logic:**
1.  Frontend (`mark_attendance.php` JS):
    ```javascript
    // Pseudo-code of what likely exists based on action handling
    if (currentTime > startTime + buffer) status = "Late";
    else status = "Present";
    sendToServer(status);
    ```
2.  Backend (`mark_attendance_action.php`):
    ```php
    $attendanceStatus = $_POST['attendanceStatus'];
    $lecr->editAttendance(..., $attendanceStatus);
    ```

**Flaw:**
The server **does not verify** if the `attendanceStatus` is correct based on the time. A malicious user (or a buggy client clock) can send "Present" even if they are hours late.

**Recommendation:**
The server must calculate the status:
1.  Receive `std_id` and `class_id`.
2.  Fetch `class_date`, `start_time`, `end_time` from the database.
3.  Compare `server_time` with `class_start_time`.
4.  Determine `status` (Present/Late/Absent) entirely on the server.

---

## 2. User Registration Logic Flow
**Workflow:** Admin submits form -> `form_action.php` validates input -> `User.php` inserts data.

**Current Logic:**
*   `form_action.php` calls `$user->isUsernameAvailable($username)`.
*   If available, it calls `$user->registerUser()`, which inserts into `uoj_user` then `uoj_student`/`uoj_lecturer`.

**Flaw:**
There are **no checks** for:
*   Duplicate `std_index` (Student Reg No).
*   Duplicate `std_nic` / `lecr_nic`.
*   Duplicate `std_email` / `lecr_email`.

If the database schema enforces uniqueness, the `INSERT` will fail, and `User::insertStudent` will return `false`. However, the user is given a generic "User Registration Failed" message without knowing *why* (e.g., "Index number already in use").

**Recommendation:**
Implement `isStudentIndexExist($index)`, `isNicExist($nic)`, `isEmailExist($email)` methods and call them in `form_action.php` before attempting registration to provide meaningful error messages.

---

## 3. Class Scheduling Logic Flow
**Workflow:** Lecturer/Admin creates a class -> `form_action.php` -> `Lecturer::createClass`.

**Current Logic:**
*   `Lecturer::createClass` directly inserts the class record:
    ```php
    INSERT INTO uoj_class (lecr_id, course_id, class_date, start_time, end_time) VALUES ...
    ```

**Flaw:**
There is **no validation** for overlaps.
*   **Lecturer Conflict:** A lecturer can be assigned to two different classes at the same time.
*   **Student Conflict:** (Less critical but possible) A course could have two classes scheduled at the same time (e.g. tutorials), which is valid, but the system doesn't flag potential issues.

**Recommendation:**
Before `INSERT`, query the database:
```sql
SELECT * FROM uoj_class 
WHERE lecr_id = ? 
  AND class_date = ? 
  AND (
    (start_time <= ? AND end_time > ?) OR -- Overlaps start
    (start_time < ? AND end_time >= ?)    -- Overlaps end
  )
```
If a row exists, block creation and warn the user.
