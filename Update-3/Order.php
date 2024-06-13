<?php
session_start();
require 'config.php'; // Include your database connection

// Determine if an admin is logged in
$is_admin = false;
if (isset($_SESSION['admin_name'])) {
    $logged_in_user = $_SESSION['admin_name'];
    $is_admin = true;
} else {
    $logged_in_user = 'Guest';
}

// Fetch orders from the database
$sql = "SELECT order_id, name, email, contact_no, product_name, amount, order_date, pickup_date, status FROM user_oder_form";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quantify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="HomePage.css"> <!-- Link to external CSS file -->
</head>
<body>
<header>
    <div class="header-left">
        <div>Welcome, <?php echo htmlspecialchars($logged_in_user); ?></div>
    </div>
</header>
<div class="container">
    <aside class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="bi bi-list"></i></button> 
        <h2 id="menuHeader">Menu</h2>
        <ul>
            <?php if ($is_admin) : ?>
            <li><a href="Dashboard.php"><i class="bi bi-person-video"></i> <span>Dashboard</span></a></li>
            <li><a href="Customer.php"><i class="bi bi-people"></i> <span>Customers</span></a></li>
            <?php endif; ?>
            <li><a href="Product.php"><i class="bi bi-box2"></i> <span>Products</span></a></li>
            <li><a href="Order.php"><i class="bi bi-bag-check"></i> <span>Orders</span></a></li>
            
            <?php if ($is_admin) : ?>
            <li><a href="logout.php" id="logoutLink"><i class="bi bi-box-arrow-left"></i> <span>Sign Out</span></a></li>
            <?php else : ?>
            <li><a href="logout.php" id="exitLink"><i class="bi bi-box-arrow-left"></i> <span>Exit</span></a></li>
            <?php endif; ?>
            
        </ul>
    </aside>
    <main class="main-content">
    <div class="orders-section">
            <h2>List of orders</h2>
            <table class="orders-table styled-table">
                <thead>
                    <tr>
                        <th>Order No:</th>
                        <th>Customer Name:</th>
                        <th>Email:</th>
                        <th>Contact No.:</th>
                        <th>Product:</th>
                        <th>Total:</th>
                        <th>Order Date:</th>
                        <th>Pickup Date:</th>
                        <th>Status:</th>
                        <th>Action:</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['order_id']) . "</td>
                                    <td>" . htmlspecialchars($row['name']) . "</td>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td>" . htmlspecialchars($row['contact_no']) . "</td>
                                    <td>" . htmlspecialchars($row['product_name']) . "</td>
                                    <td>" . htmlspecialchars($row['amount']) . "</td>
                                    <td>" . htmlspecialchars($row['order_date']) . "</td>
                                    <td>" . htmlspecialchars($row['pickup_date']) . "</td>
                                    <td>" . htmlspecialchars($row['status']) . "</td>
                                    <td><a href='edit_order.php?order_id=" . htmlspecialchars($row['order_id']) . "' class='btn btn-edit'>Edit</a></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<footer>
    <p>&copy; 2024 <a href="#" style="color: white;"></a> All rights reserved.</p>
</footer>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const menuHeader = document.getElementById('menuHeader');
        
        sidebar.classList.toggle('minimized');
        menuHeader.style.display = sidebar.classList.contains('minimized') ? 'none' : 'inline';
    }

    document.getElementById('logoutLink').addEventListener('click', function(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to sign out?')) {
            window.location.href = this.href;
        }
    });

    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.querySelector('.toggle-btn');

        if (window.innerWidth <= 768 && !sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    });
</script>
</body>
</html>
