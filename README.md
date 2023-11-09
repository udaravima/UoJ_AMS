# UoJ_AMS - Attendance Management System for DCS_UoJ

## 1. Introduction
Welcome to the Attendance Management System for University Departments. This system streamlines and manages the process of handling attendance, lectures, and percentage within the department. This README provides an overview of the system, its features, and instructions for installation and usage.

## 2. Features
### User Authentication
- Secure login and access control for administrators and users.
  
### Attendance register 
-Lecturer or Instructor can mark the students attendance 

### Attendance Management
- Administrators track and resolve attendants percentage for each subject efficiently.

### Bulk User Upload
- Can entroll student users to course.


### Customization
- Can change status of students and Lecturer dynamically from the admin panel.

## 3. Installation
To install and set up the Attendance Management System, follow these steps:

### Requirements
- Web server (e.g., Apache, Nginx)
- PHP (>= 7.0) and a MySQL database
- Composer for PHP

#### Clone the Repository in to desired server path
```bash
git clone https://github.com/udaravima/UoJ_AMS.git 
```
#### change directory in to UoJ_AMS
```bash
cd UoJ_AMS
```
#### configure 
```bash
nano config.php
```
```bash
change define('SERVER_ROOT', '/MyAttendanceSys'); -> define('SERVER_ROOT', '<Your Server Path>/UoJ_AMS');
```
#### import table_script.sql 
```bash
mysql -h <hostname> -u <username> -p <password> < '<PathToProject>/Database Sql File/Table_script.sql'
```
##### if you are get any error due to database already existance drop uoj and do it again
#### config database setup localhost, username, password, database = uoj
```bash
nano <pathToProject>/php/config/Database.php

