<?php
session_start(); // Start the session

$errors = array();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connect to the database
    $servername = "localhost"; // Replace with your MySQL server hostname or IP address
    $username = "root";
    $password = "";
    $dbname = "postgress";

    try {
        // Create a new PDO instance with error handling
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get the product details from the form
        $productName = $_POST['product-name'];
        $productDetails = $_POST['product-details'];
        $productPrice = $_POST['product-price'];

        // Handle image upload
        $imageData = file_get_contents($_FILES['image']['tmp_name']);

        // Insert the product details and image into the products table using prepared statements
        $sql = "INSERT INTO products (product_name, product_details, price, image) 
                VALUES (:productName, :productDetails, :productPrice, :imageData)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':productName', $productName);
        $stmt->bindParam(':productDetails', $productDetails);
        $stmt->bindParam(':productPrice', $productPrice);
        $stmt->bindParam(':imageData', $imageData, PDO::PARAM_LOB);
        $stmt->execute();

        // Display a success message
        $success_message = "Product listed successfully!";

    } catch (PDOException $e) {
        // Handle database errors
        echo "Connection failed: " . $e->getMessage();
    } catch (Exception $e) {
        // Handle other errors
        echo $e->getMessage();
    } finally {
        // Close the database connection
        $conn = null;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Listing</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('popo.jpg');
            background-size: cover;
            background-repeat: no-repeat;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.8);
            max-width: 700px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            color: #555;
            font-weight: bold;
        }

        input[type=text],
        input[type=number],
        textarea,
        select,
        input[type=file] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        textarea {
            resize: vertical;
        }

        input[type=submit] {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            align-self: flex-start;
        }

        input[type=submit]:hover {
            background-color: #555;
        }

        .success-message {
            color: #28a745;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Product Listing</h1>
        <form action="product.php" method="POST" enctype="multipart/form-data">
            <label for="product-name">Product Name:</label>
            <input type="text" name="product-name" id="product-name" required>

            <label for="product-details">Product Details:</label>
            <textarea name="product-details" id="product-details" required></textarea>

            <label for="product-price">Price:</label>
            <input type="number" name="product-price" id="product-price" step="0.01" required>

            <label for="image">Product Image:</label>
            <input type="file" name="image" id="image" required accept="image/*">

            <input type="submit" value="List Product">
            
            <?php if (!empty($success_message)) { ?>
                <div class="success-message">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php } ?>
        </form>
    </div>
</body>
</html>
