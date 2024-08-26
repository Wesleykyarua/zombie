<?php
session_start();

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


// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE checkouts SET status = :status WHERE id = :order_id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
}

// Fetch all orders
$stmt = $conn->prepare("SELECT * FROM checkouts ORDER BY checkout_date DESC");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Orders - IvyTrendz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #000;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
        }
        .status-done {
            color: green;
        }
        .status-pending {
            color: yellow;
        }
        .status-canceled {
            color: red;
        }
    </style>
</head>
<body>

<h2>Manage Orders</h2>
<table>
    <thead>
        <tr>
            <th>Order Date</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Home Address</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo htmlspecialchars($order['checkout_date']); ?></td>
            <td><?php echo htmlspecialchars($order['fullname']); ?></td>
            <td><?php echo htmlspecialchars($order['email']); ?></td>
            <td><?php echo htmlspecialchars($order['phone_number']); ?></td>
            <td><?php echo htmlspecialchars($order['home_address']); ?></td>
            <td class="<?php echo 'status-' . strtolower($order['status']); ?>">
                <?php echo htmlspecialchars($order['status'] ?? 'Pending'); ?>
            </td>
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                    <select name="status">
                        <option value="Pending" <?php if ($order['status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Successful" <?php if ($order['status'] === 'Successful') echo 'selected'; ?>>Successful</option>
                        <option value="Cancelled" <?php if ($order['status'] === 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>