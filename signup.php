<?php
include 'connect.php';

$errors = array();
$success_message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $age = $_POST['age'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Validate fullname
    if (empty($fullname)) {
        $errors['fullname'] = 'Full name is required';
    }

    // Validate username
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    // Validate age
    if (empty($age)) {
        $errors['age'] = 'Age is required';
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    // Validate confirm password
    if (empty($confirm_password)) {
        $errors['confirm_password'] = 'Confirm password is required';
    } elseif ($password != $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (fullname, username, email, age, password) VALUES ('$fullname', '$username', '$email', $age, '$hashed_password')";

        if (mysqli_query($conn, $sql)) {
            $success_message = 'Sign up successful! You can now login.';
            // Redirect to login page after a delay
            header('location: login.php');
            exit;
        } else {
            $errors[] = 'Error inserting user into database: ' . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="signup.css">
    <title>Sign Up</title>
</head>


    <!-- Sign up page -->
<div class="form-container">
    <h1>Sign Up</h1>
    <form action="signup.php" method="post">
        <label for="fullname">Full Name:</label>
        <input type="text" name="fullname" id="fullname" value="<?php echo isset($_POST['fullname']) ? $_POST['fullname'] : ''; ?>">
        <?php if(isset($errors['fullname'])) { ?>
            <p class="error-message"><?php echo $errors['fullname']; ?></p>
        <?php } ?>
        <br>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>">
        <?php if(isset($errors['username'])) { ?>
            <p class="error-message"><?php echo $errors['username']; ?></p>
        <?php } ?>
        <br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
        <?php if(isset($errors['email'])) { ?>
            <p class="error-message"><?php echo $errors['email']; ?></p>
        <?php } ?>
        <br>
        <label for="age">Age:</label>
        <input type="number" name="age" id="age" value="<?php echo isset($_POST['age']) ? $_POST['age'] : ''; ?>">
        <?php if(isset($errors['age'])) { ?>
            <p class="error-message"><?php echo $errors['age']; ?></p>
        <?php } ?>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password">
        <?php if(isset($errors['password'])) { ?>
            <p class="error-message"><?php echo $errors['password']; ?></p>
        <?php } ?>
        <br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password">
        <?php if(isset($errors['confirm_password'])) { ?>
            <p class="error-message"><?php echo $errors['confirm_password']; ?></p>
        <?php } ?>
        <br>
        <input type="submit" value="Sign Up">
    </form>
    <?php if(!empty($success_message)) { ?>
        <div class="success-message">
            <p><?php echo $success_message; ?></p>
        </div>
    <?php } ?>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
