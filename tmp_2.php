<!DOCTYPE html>
<html>

<head>
    <title>Form Validation</title>
</head>

<body>
    <h1>Sample Form</h1>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <!-- Text Input -->
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <br>

        <!-- Email Input -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>

        <!-- Password Input -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>

        <!-- Radio Buttons -->
        <label>Gender:</label>
        <input type="radio" id="male" name="gender" value="male" required>
        <label for="male">Male</label>
        <input type="radio" id="female" name="gender" value="female" required>
        <label for="female">Female</label>
        <br>

        <!-- Checkboxes -->
        <label>Hobbies:</label>
        <input type="checkbox" id="reading" name="hobbies[]" value="reading">
        <label for="reading">Reading</label>
        <input type="checkbox" id="sports" name="hobbies[]" value="sports">
        <label for="sports">Sports</label>
        <br>

        <!-- Textarea -->
        <label for="comments">Comments:</label>
        <textarea id="comments" name="comments" rows="4" cols="50"></textarea>
        <br>

        <!-- Submit Button -->
        <input type="submit" name="submit" value="Submit">
    </form>
    
    <?php
    // This function takes a Sri Lankan NIC number as a parameter and returns an array with the DOB, age and gender of the person
    function get_nic_details($nic)
    {
        // The first two digits of the NIC number are the year of birth
        $year = substr($nic, 0, 2);
        // The next three digits contain the number of the day in the year for the person's birth. For females, 500 is added to the number of days
        $day = substr($nic, 2, 3);
        // To get the DOB, we need to subtract 500 from the day if it is greater than 500 and add the year to 1900
        if ($day > 500) {
            $day = $day - 500;
            $gender = "F";
        } else {
            $gender = "M";
        }
        $year = $year + 1900;
        // We can use the date_create_from_format function to create a date object from the year and day
        $dob = date_create_from_format("Y-z", "$year-$day");
        // We can use the date_format function to format the date object as YYYY-MM-DD
        $dob = date_format($dob, "Y-m-d");
        // To get the age, we can use the date_diff function to get the difference between the current date and the DOB in years
        $age = date_diff(date_create($dob), date_create("today"))->y;
        // We return an array with the DOB, age and gender
        return array("dob" => $dob, "age" => $age, "gender" => $gender);
    }

    // Example usage
    $nic = "912345678V";
    $details = get_nic_details($nic);
    echo "DOB: " . $details["dob"] . "\n";
    echo "Age: " . $details["age"] . "\n";
    echo "Gender: " . $details["gender"] . "\n";
    ?>

    <?php
    // Server-side validation
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $gender = $_POST["gender"];
        $hobbies = isset($_POST["hobbies"]) ? $_POST["hobbies"] : [];
        $comments = $_POST["comments"];

        $errors = [];

        // Validation for Name (required and letters/whitespace only)
        if (empty($name) || !preg_match("/^[a-zA-Z ]*$/", $name)) {
            $errors[] = "Name is required and should contain only letters and whitespace.";
        }

        // Validation for Email (required and valid email format)
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email is required and should be in a valid format.";
        }

        // Validation for Password (required and at least 6 characters)
        if (empty($password) || strlen($password) < 6) {
            $errors[] = "Password is required and should be at least 6 characters long.";
        }

        // Validation for Gender (required)
        if (empty($gender)) {
            $errors[] = "Gender is required.";
        }

        // Validation for Hobbies (at least one should be selected)
        if (empty($hobbies)) {
            $errors[] = "At least one hobby should be selected.";
        }

        // Validation for Comments (optional)
        if (strlen($comments) > 200) {
            $errors[] = "Comments should not exceed 200 characters.";
        }

        // Display errors or process form data
        if (!empty($errors)) {
            echo "<h2>Errors:</h2>";
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
        } else {
            // Process the data (e.g., save to a database)
            echo "<h2>Form data:</h2>";
            echo "<p>Name: $name</p>";
            echo "<p>Email: $email</p>";
            echo "<p>Gender: $gender</p>";
            echo "<p>Hobbies: " . implode(", ", $hobbies) . "</p>";
            echo "<p>Comments: $comments</p>";
        }
    }
    ?>
</body>

</html>

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#log_info">
    Open Modal
</button>
<div class='modal fade' id='log_info' tabindex='-1' aria-labelledby='login-status' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h3>AMS Systems</h3>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div id='login-alert' class='alert-success'>
                    <p class=''>
                        Some Content
                    <ul>
                        <?php
                        if (!empty($goMessage)) {
                            foreach ($goMessage as $msg) {
                                echo "<li>$msg</li>";
                            }
                        }
                        ?>
                    </ul>
                    </p>
                </div>

                <div id='login-alert' class='alert-danger'>
                    <p class=''>
                    <ul>
                        <?php
                        if (!empty($errors)) {
                            foreach ($errors as $error) {
                                echo "<li>$error</li>";
                            }
                        }
                        ?>
                    </ul>
                    </p>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- <script>
    // $(document).ready(function () {
    //     $('#myModal').on('hidden.bs.modal', function () {
    //         // Redirect to the index page (you can change the URL)
    //         window.location.href = '<?php echo SERVER_ROOT; ?>/index.php';
    //     });
    // });
    document.addEventListener('DOMContentLoaded', function () {
        var loginModal = new bootstrap.Modal(document.getElementById('log_info'));
        loginModal.show();
    });
</script> -->