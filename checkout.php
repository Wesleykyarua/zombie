<?php
session_start();

// Example: Product IDs added to cart (in a real scenario, this would be dynamic)
$_SESSION['cart'] = [1, 2]; // Example product IDs

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "postgress";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user ID is set in the session
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user details
$id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Retrieve product names from the database
$product_names = [];
if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $stmt = $conn->prepare("SELECT product_name FROM products WHERE id IN ($placeholders)");
    $stmt->execute($_SESSION['cart']);
    $product_names = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $home_address = htmlspecialchars($_POST['home_address']);
    $street = htmlspecialchars($_POST['street']);
    $region = htmlspecialchars($_POST['region']);
    $country = htmlspecialchars($_POST['country']);

    // Insert data into the checkouts table
    try {
        $product_name_string = implode(', ', $product_names);

        $stmt = $conn->prepare("INSERT INTO checkouts (fullname, email, product_name, phone_number, home_address, street, region, country, checkout_date) VALUES (:fullname, :email, :product_name, :phone_number, :home_address, :street, :region, :country, NOW())");
        $stmt->bindParam(':fullname', $user['fullname']);
        $stmt->bindParam(':email', $user['email']);
        $stmt->bindParam(':product_name', $product_name_string);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':home_address', $home_address);
        $stmt->bindParam(':street', $street);
        $stmt->bindParam(':region', $region);
        $stmt->bindParam(':country', $country);
        $stmt->execute();

        // Send an email confirmation using PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ivy.trendz1@gmail.com';
            $mail->Password = 'jsfd lyhf htvt yhhb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('ivy.trendz1@gmail.com', 'IvyTrendz');
            $mail->addAddress($user['email'], $user['fullname']);

            $mail->isHTML(true);
            $mail->Subject = 'Order Confirmation - IvyTrendz';
            $mail->Body = "Dear " . htmlspecialchars($user['fullname']) . ",<br><br>Your products have been checked out successfully. Products: $product_name_string. We will contact you shortly via email or phone.<br><br>Thank you for shopping with us!<br><br>IvyTrendz";
            $mail->AltBody = "Dear " . htmlspecialchars($user['fullname']) . ",\n\nYour products have been checked out successfully. Products: $product_name_string. We will contact you shortly via email or phone.\n\nThank you for shopping with us!\n\nIvyTrendz";

            $mail->send();
            echo "<p>Checkout successful! A confirmation email has been sent to your email address.</p>";
        } catch (Exception $e) {
            echo "<p>Checkout successful! However, we were unable to send a confirmation email. Mailer Error: {$mail->ErrorInfo}</p>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - IvyTrendz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #000;
            padding: 20px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        form label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #333;
        }
    </style>
</head>
<body>

<form method="POST" action="">
    <h2>Checkout</h2>
    
    <label for="fullname">Full Name</label>
    <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" readonly>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">

    <label for="product_names">Product Names</label>
    <input type="text" id="product_names" name="product_names" value="<?php echo htmlspecialchars(implode(', ', $product_names)); ?>" readonly>

    <label for="phone_number">Phone Number</label>
    <input type="text" id="phone_number" name="phone_number" required>

    <label for="home_address">Home Address</label>
    <input type="text" id="home_address" name="home_address" required>

    <label for="street">Street</label>
    <input type="text" id="street" name="street" required>

    <label for="region">Region</label>
    <input type="text" id="region" name="region" required>

    <label for="country">Country</label>
    <input type="text" id="country" name="country" required>

    <input type="submit" value="Checkout">
</form>

</body>
</html>