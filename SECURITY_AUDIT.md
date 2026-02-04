# PHP Codebase Security Audit Report

## Executive Summary
A comprehensive flow verification and code audit was performed on the `UoJ_AMS` PHP codebase. The audit focused on security, logic flow, and code quality. 

**Critical Vulnerabilities Found:** 5
**High Severity Issues:** 2
**Medium/Low Issues:** 3

The most critical issues involve **SQL Injection** in the Lecturer class, **Unauthenticated Access** to the Report Generation logic and NFC API, and **Missing CSRF Protection** in AJAX endpoints.

---

## 1. Critical Vulnerabilities

### 1.1 SQL Injection in `Lecturer.php`
**File:** `php/class/Lecturer.php`  
**Method:** `getStudentsFromCourseId` (Line 173)  
**Description:** User input (`$order['search']`) is directly concatenated into the SQL query string without sanitization or parameter binding, despite the use of prepared statements for other variables.
**Impact:** A malicious actor (logged in as Lecturer/Admin) can dump the entire database or modify data.
**Vulnerable Code:**
```php
$query .= " AND {$this->std}.std_fullname LIKE '%" . $order['search'] . "%' ...";
```
**Remediation:**  
Use prepared statements for the search term as well.
```php
$query .= " AND ({$this->std}.std_fullname LIKE ? OR ...)";
// Bind params dynamically
```

### 1.2 Unauthenticated Report Generation
**File:** `php/generate_report.php`  
**Description:** This script generates PDF reports containing sensitive student data. It **lacks any authentication or authorization checks**.
**Impact:** Any unauthenticated user who guesses the URL and parameters (e.g., `class_id=1`) can download attendance reports.
**Remediation:**  
Add the following at the top of the file:
```php
if (!($user->isLoggedIn()) || !($user->isAdmin() || $user->isLecturer() || $user->isInstructor())) {
    header("Location: " . SERVER_ROOT . "/index.php");
    exit();
}
```

### 1.3 Unauthenticated NFC API
**File:** `api/nfc_attendance.php`  
**Description:** The API endpoint used to mark attendance has **no authentication**. It accepts a JSON payload with `nfc_uid` and `class_id` and marks attendance.
**Impact:** Anyone can spoof attendance for any student if they know the Class ID (often sequential) and NFC UID (brute-forceable).
**Remediation:**  
Implement API Key authentication or Require a Session Token from the hardware device.

### 1.4 Missing CSRF Protection in AJAX
**Files:** `php/mark_attendance.php` (frontend), `php/mark_attendance_action.php` (backend)  
**Description:** 
- `mark_attendance.php` makes AJAX POST requests but does not send the CSRF token.
- `mark_attendance_action.php` does not verify the CSRF token.
**Impact:** Attackers can trick a logged-in lecturer into marking/unmarking students' attendance by visiting a malicious site.
**Remediation:**  
1. Include the CSRF token in the AJAX headers/payload in `mark_attendance.php`.
2. Call `CSRF::requireValidToken()` in `mark_attendance_action.php`.

### 1.5 Missing CSRF Protection in NFC Management
**File:** `php/nfc_management.php`  
**Description:** The form handlers for registering/revoking cards do not verify the CSRF token.
**Impact:** Attackers can revoke or register NFC cards on behalf of a logged-in admin.
**Remediation:**  
Add `CSRF::requireValidToken();` inside the POST handling block.

---

## 2. High Severity Issues

### 2.1 Weak File Upload Validation
**File:** `php/class/Utils.php`  
**Method:** `storeProfilePic`  
**Description:** The code only checks the file extension by extracting the string after the last dot. It does not verify the actual MIME type or content of the file.
```php
$extension = substr($filename, strrpos($filename, '.'));
```
**Impact:** An attacker could upload a PHP shell named `image.php` (or `image.png.php`) if the client-side checks are bypassed, leading to Remote Code Execution (RCE).
**Remediation:**  
Whitelist allowed extensions (jpg, png) and verify `mime_content_type()`.

### 2.2 Unrestricted Attendance Logic
**File:** `php/mark_attendance_action.php`  
**Description:** The system allows marking attendance via the API even if the class is not currently "active" or "in-session" (logic exists in frontend JS but backend verification is weak or minimal in `Lecturer::markAttendance`).
**Impact:** Attendance data integrity can be compromised.
**Remediation:**  
Enforce server-side time checks ensuring `attendTime` is within the class schedule window.

---

## 3. Medium & Low Issues

### 3.1 Hardcoded Database Credentials
**File:** `php/config/Database.php`  
**Description:** Database credentials (password `beyondm`) are hardcoded as defaults.
**Recommendation:** Ensure `.env` is used in production and these defaults are removed or changed to empty strings.

### 3.2 Manual Salt Management
**File:** `php/class/User.php`  
**Description:** The application appends a manual salt (`$user['user_salt']`) to the password *before* hashing with `password_hash`. `password_hash` generates a cryptographically secure salt automatically. Adding a manual salt helps only slightly but adds complexity.
**Recommendation:** Rely on `password_hash`'s built-in salting mechanism.

### 3.3 Potential XSS in Admin Dashboard
**File:** `php/admin_dashboard.php`  
**Description:** While `form_action.php` validates input strictly (Regex), `admin_dashboard.php` echoes data directly.
**Recommendation:** Always escape output using `htmlspecialchars()` when echoing user data, e.g., `htmlspecialchars($lecturer['username'])`.

---

## Conclusion
The application has a good foundation (use of prepared statements, basic access control), but several critical holes exist that make it vulnerable to attack. Prioritize fixing the **SQL Injection** and **Unauthenticated Access** issues immediately.
