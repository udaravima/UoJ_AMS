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
