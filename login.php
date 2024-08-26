<?php
session_start(); // Start the session

include 'connect.php';

$errors = array();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate username
    if (empty($username)) {
        $errors[] = "Username is required";
    }

    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required";
    }

    // If there are no errors, attempt to log in
    if (empty($errors)) {
        // Check the users table for the given username
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            
            // Verify the password
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $username; // Set username in session
                $_SESSION['id'] = $row['id']; // Set user id in session (assuming user_id is the primary key)
                $_SESSION['user_role'] = 'user'; // Set user role (could be more specific if needed)
                header('Location: home.php'); // Redirect user to home page
                exit();
            } else {
                $errors[] = 'Invalid username or password';
            }
        } else {
            $errors[] = 'Invalid username or password';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="login.css">
</head>

    <div class="form-container">
        <h1>Login</h1>
        <form action="login.php" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            <?php if (in_array('Username is required', $errors)) { ?>
                <p class="error-message"><?php echo 'Username is required'; ?></p>
            <?php } ?>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
            <?php if (in_array('Password is required', $errors)) { ?>
                <p class="error-message"><?php echo 'Password is required'; ?></p>
            <?php } ?>
            <?php if (in_array('Invalid username or password', $errors)) { ?>
                <p class="error-message"><?php echo 'Invalid username or password'; ?></p>
            <?php } ?>
            <br>
            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
</body>
</html>
