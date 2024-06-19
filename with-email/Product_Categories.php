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

$logged_in_user = $_SESSION['admin_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Quantify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="HomePage.css">
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
                <li><a href="Product.php"><i class="bi bi-box2"></i> <span>Products</span></a></li>
                <li><a href="Order.php"><i class="bi bi-bag-check"></i> <span>Orders</span></a></li>
                <li><a href="emailForm.php"><i class="bi bi-envelope"></i> <span>Mail</span></a></li>
                <li><a href="logout.php" id="logoutLink"><i class="bi bi-box-arrow-left"></i> <span>Sign Out</span></a></li>
                <?php else : ?>
                <li><a href="Product.php"><i class="bi bi-box2"></i> <span>Products</span></a></li>
                <li><a href="Cart.php"><i class="bi bi-bag-check"></i> <span>Cart</span></a></li>
                <li><a href="logout.php" id="exitLink"><i class="bi bi-box-arrow-left"></i> <span>Exit</span></a></li>
                <?php endif; ?>
            </ul>
        </aside>
    <main class="main-content">
        <!-- Admin-specific content goes here -->
    </main>
</div>
<footer>
    <p>&copy; 2024 <a href="#" style="color: white;">Quantify</a>. All rights reserved.</p>
</footer>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');

        sidebar.classList.toggle('minimized');

        const menuHeader = document.getElementById('menuHeader');
        menuHeader.style.display = sidebar.classList.contains('minimized') ? 'none' : 'inline';
    }

    document.getElementById('logoutLink').addEventListener('click', function(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to sign out?')) {
            window.location.href = this.href;
        }
    });
</script>
</body>
</html>
