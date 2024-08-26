<?php
session_start();
include 'connect.php';

// Initialize variables
$search = '';
$users = [];

// Check if the form is submitted to search
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search = mysqli_real_escape_string($conn, $_POST['search']);
    $sql = "SELECT * FROM users WHERE username LIKE '%$search%' OR email LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM users";
}

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

// Handle delete user request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_sql = "DELETE FROM users WHERE id = $id";
    mysqli_query($conn, $delete_sql);
    header('Location: admin.php');
    exit;
}

// Handle update user request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $id = intval($_POST['user_id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $age = intval($_POST['age']); // Get the age from the form

    // Update user details in the database
    $update_sql = "UPDATE users SET username='$username', email='$email', age=$age WHERE id=$id";
    mysqli_query($conn, $update_sql);
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
        }
        header {
            background-color: #000;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        nav {
            display: flex;
            justify-content: center;
            gap: 15px;
            background-color: #111;
            padding: 10px 0;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
        }
        nav a:hover {
            background-color: #333;
        }
        .container {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            display: block;
            overflow-x: auto;
            max-height: 300px; /* Limit the height */
        }
        th, td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
            background-color: #fff;
        }
        th {
            background-color: #000;
            color: #fff;
            position: sticky;
            top: 0;
        }
        button {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #555;
        }
        .form-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="password"],
        .form-container input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Management</h1>
    </header>
    <nav>
        <a href="#">Dashboard</a>
        <a href="#">Users</a>
        <a href="product.php">Products</a>
        <a href="order2.php">Orders</a>
    </nav>
    <div class="container">
        <!-- Search form -->
        <form method="POST" action="admin.php">
            <input type="text" name="search" placeholder="Search by username or email" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Users table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Age</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['age']); ?></td>
                        <td>
                            <form method="POST" action="admin.php" style="display:inline-block;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                <input type="number" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required>
                                <button type="submit" name="update_user">Update</button>
                            </form>
                            <a href="admin.php?delete=<?php echo $user['id']; ?>"><button type="button">Delete</button></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Add new user form -->
        <div class="form-container">
            <h2>Add New Employee</h2>
            <form action="add_user.php" method="POST">
                <input type="text" id="username" name="username" placeholder="Username" required>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="number" id="age" name="age" placeholder="Age" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit">Add User</button>
            </form>
        </div>
    </div>
</body>
</html>