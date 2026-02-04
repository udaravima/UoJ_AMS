# UoJ_AMS - Attendance Management System for DCS_UoJ

**UoJ_AMS** is a comprehensive web-based Attendance Management System designed for university departments. It streamlines the entire attendance lifecycle, from marking attendance in classes to generating detailed PDF reports for administration.

## 🚀 Features

### 👤 Role-Based Access Control
- **Administrator**: Full system control, user management (Student/Lecturer), and system configuration.
- **Lecturer**: Create classes, mark attendance, and view course reports.
- **Instructor**: Assist in marking attendance.
- **Student**: View personal attendance records and course history.

### 📅 Attendance Management
- **Efficient Marking**: Manual or NFC-based attendance marking.
- **Status Tracking**: Distinguishes between 'Present', 'Late', and 'Absent'.
- **Class Scheduling**: Schedule classes, defining dates and times.
- **Dynamic Status**: Lecturers can update student status dynamically.

### 💳 NFC Integration
- **Card Management**: Assign NFC cards to students for quick identification.
- **Seamless Attendance**: Infrastructure for tapping cards to mark attendance.

### 📊 Reporting & Analytics
- **PDF Generation**: Powered by **Dompdf** for professional-grade reports.
- **Report Types**:
    - **Class Attendance Report**: Detailed list of students for a specific session.
    - **Student Attendance Report**: Individual attendance history across courses.
    - **Course Summary Report**: Aggregate statistics for course performance.

### 🛠 Administrative Tools
- **User Registration**: Enrolling students and staff.
- **Course Management**: Enroll students into specific courses.
- **Calendar Setup**: Manage academic calendar settings and semesters.

## 🛠 Technology Stack
- **Backend**: PHP (>= 7.0)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla), Bootstrap
- **Dependencies**:
    - `Dompdf`: For generating PDF reports.

## ⚙️ Installation & Setup

### Prerequisites
- PHP >= 7.0
- MySQL Database
- Composer (for dependency management)
- Web Server (Apache/Nginx)

### Steps

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/udaravima/UoJ_AMS.git
    cd UoJ_AMS
    ```

2.  **Install Dependencies**
    Ensure you have Composer installed, then run:
    ```bash
    composer install
    ```

3.  **Database Configuration**
    - Import the database schema:
        ```bash
        mysql -h <hostname> -u <username> -p <password> < 'Database Sql Files/Table_script.sql'
        ```
        *Note: If you encounter errors about the database existing, drop the `uoj` database and retry.*

    - Configure the database connection:
        Open `php/config/Database.php` and update the credentials:
        ```php
        private $host = "localhost";
        private $db_name = "uoj";
        private $username = "<your_username>";
        private $password = "<your_password>";
        ```

4.  **System Configuration**
    - Open `config.php` and set the server root path:
        ```php
        // Example: If your project is at http://localhost/UoJ_AMS
        define('SERVER_ROOT', '/UoJ_AMS'); 
        ```

5.  **Create Administrator Account**
    1.  Access the web interface (e.g., `http://localhost/UoJ_AMS`) and register a new account.
    2.  Manually promote the user to Admin via SQL (since the first user needs to be bootstrapped):
        ```bash
        mysql -u <username> -p <password>
        ```
        ```sql
        USE uoj;
        UPDATE uoj_user SET user_status = 1, user_role = 0 WHERE user_id = 1;
        ```
        *This sets the first registered user (ID 1) as an active Administrator.*

## 📖 Usage Guide

### For Administrators
- Log in to access the **Admin Dashboard**.
- Manage users, configure semester details, and oversee all courses.
- Use the **NFC Management** section to register student cards.
- Generate system-wide reports.

### For Lecturers
- Log in to the **Lecturer Dashboard**.
- **Create Class**: Schedule a new lecture session.
- **Mark Attendance**: Use the interface to mark students present.
- **Reports**: Generate PDF reports for your specific classes or courses.

### For Students
- Log in to the **Student Dashboard**.
- View your attendance percentage per course.
- Check valid vs. invalid attendance records.

## 🤝 Contribution
Contributions are welcome! Please fork the repository and submit a pull request.
