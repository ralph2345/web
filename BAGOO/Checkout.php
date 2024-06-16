<?php
session_start();
require 'config.php'; // Include your database connection

$orderPlaced = false; // Flag to check if order is placed successfully

// Determine if an admin is logged in
$is_admin = false;
if (isset($_SESSION['admin_name'])) {
    $logged_in_user = $_SESSION['admin_name'];
    $is_admin = true;
} else {
    $logged_in_user = 'Guest';
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = htmlspecialchars($_POST['name']);
    $number = htmlspecialchars($_POST['number']);
    $email = htmlspecialchars($_POST['email']);
    $pickup_date = htmlspecialchars($_POST['pickupdate']);
    $grand_total = htmlspecialchars($_POST['grand_total']);
    $status = 'Pending'; // Default status

    // Initialize total_products string
    $total_products = '';

    // Process items
    $items = $_POST['items'];
    foreach ($items as $item) {
        $product_name = htmlspecialchars($item['name']);
        $size = htmlspecialchars($item['size']);
        $quantity = htmlspecialchars($item['quantity']);
        $total_products .= "$product_name ($size): $quantity, ";
    }
    $total_products = rtrim($total_products, ', ');

    // Insert data into order_form table
    $query = "INSERT INTO order_form (name, number, email, total_products, total_price, order_date, pickup_date, status) 
              VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sssssss', $name, $number, $email, $total_products, $grand_total, $pickup_date, $status);

    if (mysqli_stmt_execute($stmt)) {
        $orderPlaced = true; // Set the flag to true if order is placed
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

// Fetch order details from the database
$query = "SELECT name, price, quantity, size FROM orders";
$result = mysqli_query($conn, $query);

// Initialize variables for order summary
$items = [];
$grand_total = 0;

// Process the result
while ($row = mysqli_fetch_assoc($result)) {
    $name = htmlspecialchars($row['name']);
    $price = htmlspecialchars($row['price']);
    $quantity = htmlspecialchars($row['quantity']);
    $size = htmlspecialchars($row['size']);
    $total_price = $price * $quantity;
    $grand_total += $total_price;

    $items[] = [
        'name' => $name,
        'price' => $price,
        'quantity' => $quantity,
        'size' => $size,
        'total_price' => $total_price,
    ];
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Quantify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="Checkout.css">
    <style>
        .order-summary {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            background-color: #e9ecef;
            border-radius: 5px;
            padding: 10px 15px;
            margin: 5px 0;
            font-size: 16px;
            white-space: nowrap; 
        }
        .order-item span {
            flex: 1;
            text-align: center;
        }
        .order-item .item-name {
            text-align: left;
            flex: 2;
        }
        .order-item .item-quantity {
            text-align: center;
            flex: 1;
        }
        .order-item .item-total {
            text-align: right;
            flex: 1;
            margin-left: 10px; 
        }
        .order-total {
            background-color: #ff5722;
            color: white;
            text-align: center;
            padding: 10px 0;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
        }
        
    </style>
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
            <li><a href="logout.php" id="logoutLink"><i class="bi bi-box-arrow-left"></i> <span>Sign Out</span></a></li>
            <?php else : ?>
            <li><a href="Product.php"><i class="bi bi-box2"></i> <span>Products</span></a></li>
            <li><a href="Cart.php"><i class="bi bi-bag-check"></i> <span>Cart</span></a></li>
            <li><a href="logout.php" id="exitLink"><i class="bi bi-box-arrow-left"></i> <span>Exit</span></a></li>
            <?php endif; ?>
        </ul>
    </aside>
    <main class="main-content">
        <h1>Checkout Form</h1>
        <form action="" method="POST" id="checkoutForm"> <!-- Form action is set to current page -->
            <div class="order-summary">
                <?php foreach ($items as $index => $item): ?>
                    <div class="order-item">
                        <span class="item-name"><?php echo $item['name']; ?> (<?php echo $item['size']; ?>)</span>
                        <span class="item-quantity">Quantity: <?php echo $item['quantity']; ?></span>
                        <span class="item-total">Total: Php <?php echo number_format($item['total_price'], 2); ?></span>
                    </div>
                    <input type="hidden" name="items[<?php echo $index; ?>][name]" value="<?php echo $item['name']; ?>">
                    <input type="hidden" name="items[<?php echo $index; ?>][size]" value="<?php echo $item['size']; ?>">
                    <input type="hidden" name="items[<?php echo $index; ?>][quantity]" value="<?php echo $item['quantity']; ?>">
                    <input type="hidden" name="items[<?php echo $index; ?>][price]" value="<?php echo $item['price']; ?>">
                <?php endforeach; ?>
                <div class="order-total">
                    Grand Total: Php <?php echo number_format($grand_total, 2); ?>
                    <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your name" pattern="[A-Za-z\s]+" title="Name should only contain letters and spaces.">
                </div>
                <div class="form-group">
                    <label for="number">Phone Number:</label>
                    <input type="tel" id="number" name="number" required placeholder="Enter your phone number" pattern="\d{10,15}" title="Phone number should contain 10 to 11 digits.">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label for="pickupdate">Pickup Date:</label>
                    <input type="date" id="pickupdate" name="pickupdate">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Order Now</button>
        </form>
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

    document.getElementById('checkoutForm').addEventListener('submit', function(event) {
        const nameInput = document.getElementById('name');
        const phoneInput = document.getElementById('number');
        const emailInput = document.getElementById('email');

        const namePattern = /^[A-Za-z\s]+$/;
        const phonePattern = /^\d{10,11}$/;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!namePattern.test(nameInput.value)) {
            alert('Invalid name. Only letters and spaces are allowed.');
            event.preventDefault();
        }

        if (!phonePattern.test(phoneInput.value)) {
            alert('Invalid phone number. Only digits are allowed, and it should be between 10 to 15 digits.');
            event.preventDefault();
        }

        if (!emailPattern.test(emailInput.value)) {
            alert('Invalid email address.');
            event.preventDefault();
        }
    });
</script>
</body>
</html>
