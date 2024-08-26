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


// Fetch all orders
$filter = $_GET['filter'] ?? 'all';

if ($filter === 'day') {
    $stmt = $conn->prepare("SELECT * FROM checkouts WHERE DATE(checkout_date) = CURDATE()");
} elseif ($filter === 'week') {
    $stmt = $conn->prepare("SELECT * FROM checkouts WHERE WEEK(checkout_date) = WEEK(CURDATE())");
} else {
    $stmt = $conn->prepare("SELECT * FROM checkouts ORDER BY checkout_date DESC");
}

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders - IvyTrendz</title>
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

<h2>Admin Orders</h2>

<form method="GET" action="">
    <label for="filter">Filter by:</label>
    <select name="filter" id="filter" onchange="this.form.submit()">
        <option value="all" <?php if ($filter === 'all') echo 'selected'; ?>>All</option>
        <option value="day" <?php if ($filter === 'day') echo 'selected'; ?>>Today</option>
        <option value="week" <?php if ($filter === 'week') echo 'selected'; ?>>This Week</option>
    </select>
</form>

<table>
    <thead>
        <tr>
            <th>Order Date</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Home Address</th>
            <th>Status</th>
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
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>