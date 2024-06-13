<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin_name'])) {
    header('Location: ../login_form.php');
    exit();
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
            <li><a href="Dashboard.php"><i class="bi bi-person-video"></i> <span>Dashboard</span></a></li>
            <li><a href="Customer.php"><i class="bi bi-people"></i> <span>Customers</span></a></li>
            <li><a href="Product.php"><i class="bi bi-box2"></i> <span>Products</span></a></li>
            <li><a href="Order.php"><i class="bi bi-bag-check"></i> <span>Orders</span></a></li>
            <li><a href="logout.php" id="logoutLink"><i class="bi bi-box-arrow-left"></i> <span>Sign Out</span></a></li>
        </ul>
    </aside>
    <main class="main-content">
        <div class="dashboard">
            <div class="card blue">
                <i class="bi bi-box-seam"></i>
                <h3>Products</h3>
                <p>0</p>
                <a href="#">View Details</a>
            </div>
            <div class="card green">
                <i class="bi bi-people"></i>
                <h3>Customers</h3>
                <p>0</p>
                <a href="#">View Details</a>
            </div>
            <div class="card orange">
                <i class="bi bi-tags"></i>
                <h3>Product Categories</h3>
                <p>0</p>
                <a href="#">View Details</a>
            </div>
            <div class="card red">
                <i class="bi bi-receipt"></i>
                <h3>Orders</h3>
                <p>0</p>
                <a href="#">View Details</a>
            </div>
        </div>
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
