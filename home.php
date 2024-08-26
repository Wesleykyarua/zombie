<?php
session_start(); // Start the session

// Check if user ID is set in the session
if (!isset($_SESSION['id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "postgress";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search-input'])) {
    $search = mysqli_real_escape_string($conn, $_POST['search-input']);
    $sql = "SELECT * FROM products WHERE product_name LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM products";
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit(); // Stop script execution if unable to connect to the database
}

// Fetch user details from the users table
$id = $_SESSION['id']; // Assume the user ID is stored in session after login
$stmt = $conn->prepare("SELECT fullname FROM users WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Retrieve product listings from the database
try {
    $stmt = $conn->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit(); // Stop script execution if unable to retrieve product listings
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ivy Trendz Online Store</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        header {
            background-color: #000;
            color: #fff;
            padding: 15px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        nav {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #111;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 5px;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #ddd;
        }
        .search-bar {
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 5px;
            padding: 5px;
            width: 90%;
            margin-top: 10px;
        }
        .search-bar input {
            border: none;
            padding: 10px;
            font-size: 1em;
            border-radius: 5px;
            outline: none;
            flex-grow: 1;
        }
        .search-bar button {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 5px;
            transition: background-color 0.3s;
        }
        .search-bar button:hover {
            background-color: #444;
        }
        .main-banner {
            background-image: url('juja.jpeg');
            background-size: cover;
            background-position: center;
            height: 200px;
            color: #fff;
            text-align: center;
            padding: 40px 20px;
            position: relative;
        }
        .main-banner::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .main-banner h1, .main-banner p {
            position: relative;
            z-index: 1;
        }
        .main-banner h1 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
         .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }
        .product {
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 50px;
            max-width: 300px;
            text-align: center;
            margin-bottom: 20px;
            margin-left: 50px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        .product img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .product h3 {
            margin: 15px 0 10px;
            font-size: 1.2em;
        }
        .product p {
            color: #666;
            margin-bottom: 10px;
        }
        .product .price {
            font-weight: bold;
            margin-bottom: 15px;
            color: #000;
        }
        .product button {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .product button:hover {
            background-color: #444;
        }
        .cart-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000;
            transition: background-color 0.3s;
        }
        .cart-btn:hover {
            background-color: #444;
        }
        .cart {
            display: none;
            position: fixed;
            top: 60px;
            right: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 300px;
            max-height: 80vh;
            overflow-y: auto;
            z-index: 1000;
        }
        .cart h2 {
            margin: 0 0 15px 0;
            font-size: 1.5em;
        }
        .cart ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .cart li {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart li span {
            margin-right: 10px;
            flex-grow: 1;
        }
        .cart li input {
            width: 50px;
            text-align: center;
        }
        .cart-total {
            font-weight: bold;
            margin-top: 15px;
            text-align: right;
        }
        .checkout-btn {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            transition: background-color 0.3s;
        }
        .checkout-btn:hover {
            background-color: #444;
        }
        footer {
            background-color: #111;
            color: #fff;
            text-align: center;
            padding: 20px;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>Ivy Trendz Online Store</h1>
    </header>
    <nav>
        <a href="index.html">Home</a>
        <a href="#">Support</a>
        <a href="#">My Account</a>
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="Search products...">
            <button onclick="searchProducts()">Search</button>
        </div>
        <a href="logout.php">Log out</a>
    </nav>
    <div class="main-banner">
        <h1>Welcome, <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
        <p>Thank you for visiting IvyTrendz. Enjoy browsing our products!</p>
    </div>
    <div class="products" id="product-list">
        <?php foreach ($products as $product) : ?>
            <div class="product" data-name="<?php echo htmlspecialchars($product['product_name']); ?>">
                <?php if (!empty($product['image'])) : ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                <?php else : ?>
                    <img src="https://via.placeholder.com/300" alt="No Image Available">
                <?php endif; ?>
                <div class="product-details">
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p><?php echo htmlspecialchars($product['product_details']); ?></p>
                    <p class="price">Price: Tshs <?php echo number_format($product['price']); ?></p>
                    <button onclick="addToCart('<?php echo htmlspecialchars($product['product_name']); ?>', <?php echo $product['price']; ?>)">Add to Cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="cart-btn" onclick="toggleCart()">Cart</button>
    <div class="cart" id="cart">
        <h2>Cart</h2>
        <ul id="cart-items">
            <!-- Cart items will appear here -->
        </ul>
        <p class="cart-total">Total: Tshs <span id="cart-total">0</span></p>
        <button class="checkout-btn" onclick="window.location.href='checkout.php'">Checkout</button>
    </div>
    <footer>
        <p>©2024 Ivy Trendz. All rights reserved.</p>
    </footer>
    <script>
        const cartItems = [];

        function toggleCart() {
            const cart = document.getElementById('cart');
            cart.style.display = cart.style.display === 'block' ? 'none' : 'block';
        }

        function addToCart(productName, productPrice) {
            const existingItem = cartItems.find(item => item.name === productName);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cartItems.push({
                    name: productName,
                    price: productPrice,
                    quantity: 1
                });
            }

            updateCart();
        }

        function updateCart() {
            const cartItemsList = document.getElementById('cart-items');
            const cartTotalElement = document.getElementById('cart-total');

            cartItemsList.innerHTML = ''; // Clear current cart items

            let cartTotal = 0;

            cartItems.forEach(item => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `
                    <span>${item.name}</span>
                    <input type="number" value="${item.quantity}" min="1" onchange="updateQuantity('${item.name}', this.value)">
                    <span>Tshs ${item.price * item.quantity}</span>
                    <button onclick="removeFromCart('${item.name}')">Remove</button>
                `;

                cartItemsList.appendChild(listItem);

                cartTotal += item.price * item.quantity;
            });

            cartTotalElement.textContent = cartTotal.toLocaleString();
        }

        function updateQuantity(productName, quantity) {
            const item = cartItems.find(item => item.name === productName);
            if (item) {
                item.quantity = parseInt(quantity);
                updateCart();
            }
        }

        function removeFromCart(productName) {
            const index = cartItems.findIndex(item => item.name === productName);
            if (index !== -1) {
                cartItems.splice(index, 1);
                updateCart();
            }
        }

        function searchProducts() {
            const searchInput = document.getElementById('search-input').value.toLowerCase();
            const products = document.querySelectorAll('.product');

            products.forEach(product => {
                const productName = product.getAttribute('data-name').toLowerCase();
                if (productName.includes(searchInput)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>