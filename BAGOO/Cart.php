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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $merchandise_id = intval($_POST['merchandise_id']);
    $new_quantity = intval($_POST['quantity']);
    $size = $_POST['size']; // Get the size from the form

    // Fetch current quantity from orders table
    $sql = "SELECT quantity FROM orders WHERE `Order ID` = $order_id";
    $result = $conn->query($sql);
    $current_order = $result->fetch_assoc();
    $current_quantity = intval($current_order['quantity']);

    // Update orders table with new quantity
    $sql = "UPDATE orders SET quantity = $new_quantity WHERE `Order ID` = $order_id";
    $conn->query($sql);

    // Determine the stock column based on size
    $size_column = '';
    if ($size === 'N/A') {
        $size_column = 'accessories_stock';
    } else {
        switch (strtolower($size)) {
            case 'small':
                $size_column = 'small_stock';
                break;
            case 'medium':
                $size_column = 'medium_stock';
                break;
            case 'large':
                $size_column = 'large_stock';
                break;
            case 'xl':
                $size_column = 'xl_stock';
                break;
            case '2xl':
                $size_column = 'xxl_stock';
                break;
            default:
                die('Invalid size.');
        }
    }

    // Calculate the difference in quantity
    $quantity_diff = $new_quantity - $current_quantity;

    // Update product table stock
    if ($quantity_diff != 0) {
        $sql = "UPDATE product SET $size_column = $size_column - $quantity_diff WHERE `Merchandise ID` = $merchandise_id";
        $conn->query($sql);
    }

    // Redirect back to cart page
    header("Location: Cart.php");
    exit;
}

// Handle GET request for removing items
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $merchandise_id = intval($_GET['merchandise_id']);
    $quantity = intval($_GET['quantity']);
    $size = $_GET['size']; // Get the size from the URL

    // Determine the stock column based on size
    $size_column = '';
    if ($size === 'N/A') {
        $size_column = 'accessories_stock';
    } else {
        switch (strtolower($size)) {
            case 'small':
                $size_column = 'small_stock';
                break;
            case 'medium':
                $size_column = 'medium_stock';
                break;
            case 'large':
                $size_column = 'large_stock';
                break;
            case 'xl':
                $size_column = 'xl_stock';
                break;
            case '2xl':
                $size_column = 'xxl_stock';
                break;
            default:
                die('Invalid size.');
        }
    }

    // Update product table stock to restore the quantity
    $sql = "UPDATE product SET $size_column = $size_column + $quantity WHERE `Merchandise ID` = $merchandise_id";
    $conn->query($sql);

    // Remove the item from orders table
    $sql = "DELETE FROM orders WHERE `Order ID` = $order_id";
    $conn->query($sql);

    // Redirect back to cart page
    header("Location: Cart.php");
    exit;
}

// Fetch orders from the database
$sql = "SELECT `Order ID`, `Merchandise ID`, `image`, `name`, `price`, `quantity`, `size`, `category` FROM `orders`";
$result = $conn->query($sql);

// Calculate grand total
$grand_total = 0;
$orders_found = false; // Add a flag to track if orders are found
if ($result->num_rows > 0) {
    $orders_found = true; // Set the flag to true if orders are found
    while ($row = $result->fetch_assoc()) {
        $grand_total += $row['price'] * $row['quantity'];
    }
    $result->data_seek(0); // Reset the result pointer
}
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
        <div class="orders-section">
            <h2>Shopping Cart</h2>
            <table class="orders-table styled-table">
                <thead>
                    <tr>
                        <th>Image:</th>
                        <th>Name:</th>
                        <th>Price:</th>
                        <th>Quantity:</th>
                        <th>Total Price:</th>
                        <th>Size:</th>
                        <th>Category:</th>
                        <th>Action:</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<form action='Cart.php' method='post'>";
                            echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($row['Order ID']) . "'>";
                            echo "<input type='hidden' name='merchandise_id' value='" . htmlspecialchars($row['Merchandise ID']) . "'>";
                            echo "<input type='hidden' name='size' value='" . htmlspecialchars($row['size']) . "'>"; // Add size field
                            echo "<input type='hidden' name='quantity' value='" . htmlspecialchars($row['quantity']) . "'>"; // Add quantity field
                            echo "<td><img src='" . htmlspecialchars($row['image']) . "' alt='Product Image' class='order-image'></td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>Php " . htmlspecialchars($row['price']) . "</td>";
                            echo "<td><input type='number' name='quantity' value='" . htmlspecialchars($row['quantity']) . "' min='1'><button type='submit' class='update-btn'>Update</button></td>";
                            echo "<td>Php " . (htmlspecialchars($row['price']) * htmlspecialchars($row['quantity'])) . "</td>";
                            echo "<td>" . htmlspecialchars($row['size']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td><button type='button' class='remove-btn' onclick='removeOrder(" . htmlspecialchars($row['Order ID']) . ", " . htmlspecialchars($row['Merchandise ID']) . ", " . htmlspecialchars($row['quantity']) . ", \"" . htmlspecialchars($row['size']) . "\")'>Remove</button></td>";
                            echo "</form>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="order-summary">
                <div class="total-price">
                    <span>Grand Total:</span>
                    <span id="grand-total">Php <?php echo $grand_total; ?></span>
                </div>
                <form action="Checkout.php" method="get">
                    <button type="submit" class="checkout-btn" id="checkout-btn" <?php echo $orders_found ? '' : 'disabled'; ?>>Proceed To Checkout</button>
                </form>
            </div>
        </div>
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

    function removeOrder(orderId, merchandiseId, quantity, size) {
        if (confirm('Are you sure you want to delete this order?')) {
            window.location.href = 'Cart.php?order_id=' + orderId + '&merchandise_id=' + merchandiseId + '&quantity=' + quantity + '&size=' + size;
        }
    }

    // Update grand total dynamically when quantity is changed
    document.querySelectorAll('.update-btn').forEach(button => {
        button.addEventListener('click', updateGrandTotal);
    });

    function updateGrandTotal() {
        const rows = document.querySelectorAll('.orders-table tbody tr');
        let grandTotal = 0;
        rows.forEach(row => {
            const price = parseFloat(row.querySelector('td:nth-child(3)').textContent.replace('Php ', '').replace(',', ''));
            const quantity = parseInt(row.querySelector('td:nth-child(4) input').value);
            const totalPrice = price * quantity;
            row.querySelector('td:nth-child(5)').textContent = 'Php ' + totalPrice;
            grandTotal += totalPrice;
        });
        document.getElementById('grand-total').textContent = 'Php ' + grandTotal;

        // Enable or disable the checkout button based on order count
        const checkoutButton = document.getElementById('checkout-btn');
        checkoutButton.disabled = rows.length === 0;
    }

    document.querySelectorAll('.update-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const form = this.closest('form');
            form.submit();
        });
    });
</script>
</body>
</html>

