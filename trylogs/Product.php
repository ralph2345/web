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

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        // Handle add product
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_quantity = $_POST['product_quantity'];
        $product_image = $_FILES['product_image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($product_image);

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            $sql = "INSERT INTO product (`Merchandise ID`, name, price, quantity, image) VALUES (NULL, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $product_name, $product_price, $product_quantity, $target_file);
            $stmt->execute();
            $message = 'Product added successfully';
        } else {
            $message = 'Sorry, there was an error uploading your file.';
        }

        // Redirect to avoid form resubmission
        header('Location: Product.php?message=' . urlencode($message));
        exit();
    } elseif (isset($_POST['update_product'])) {
        // Handle update product
        $product_id = $_POST['update_p_id'];
        $product_name = $_POST['update_p_name'];
        $product_price = $_POST['update_p_price'];
        $product_quantity = $_POST['update_p_quantity'];
        $product_image = $_FILES['update_p_image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($product_image);

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        if (move_uploaded_file($_FILES['update_p_image']['tmp_name'], $target_file)) {
            $sql = "UPDATE product SET name=?, price=?, quantity=?, image=? WHERE `Merchandise ID`=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $product_name, $product_price, $product_quantity, $target_file, $product_id);
            $stmt->execute();
            $message = 'Product updated successfully';
        } else {
            $message = 'Sorry, there was an error uploading your file.';
        }

        // Redirect to avoid form resubmission
        header('Location: Product.php?message=' . urlencode($message));
        exit();
    } elseif (isset($_POST['delete_product'])) {
        // Handle delete product
        $product_id = $_POST['delete_product_id'];
        $sql = "DELETE FROM product WHERE `Merchandise ID`=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $message = 'Product deleted successfully';
    }

    // Redirect to avoid form resubmission
    header('Location: Product.php?message=' . urlencode($message));
    exit();
}

// Fetch products from the database if an admin is logged in
if ($is_admin) {
    $sql = "SELECT `Merchandise ID`, name, price, quantity, image FROM product";
    $result = $conn->query($sql);
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
            <?php if ($is_admin): ?>
            <div class="add-product-form" id="addProductForm">
                <h3 id="formHeading">Add a New Product</h3>
                <form action="Product.php" method="post" enctype="multipart/form-data" id="addProductForm">
                    <input type="text" name="product_name" class="box" placeholder="Enter the product name" required>
                    <input type="text" name="product_price" class="box" placeholder="Enter the product price" required>
                    <input type="number" name="product_quantity" class="box" placeholder="Enter the product quantity (e.g., 10)" required>
                    <input type="file" name="product_image" class="box" required>
                    <button type="submit" class="btn" name="add_product">Add The Product</button>
                </form>
            </div>
            <?php endif; ?>

            <?php if ($is_admin): ?>
                <section class="update-form-container hidden" id="updateFormContainer">
                    <?php
                    if (isset($_GET['edit'])) {
                        $edit_id = $_GET['edit'];
                        $edit_query = mysqli_query($conn, "SELECT * FROM `product` WHERE `Merchandise ID` = $edit_id");
                        if (mysqli_num_rows($edit_query) > 0) {
                            while ($fetch_edit = mysqli_fetch_assoc($edit_query)) {
                    ?>
                    <div class="update-product-form">
                        <h3>Update Product</h3>
                        <div class="image-container">
                            <img src="<?php echo htmlspecialchars($fetch_edit['image']); ?>" height="200" alt="">
                        </div>
                        <form action="Product.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="update_p_id" value="<?php echo $fetch_edit['Merchandise ID']; ?>">
                            <input type="text" class="box" required name="update_p_name" value="<?php echo htmlspecialchars($fetch_edit['name']); ?>" placeholder="Enter the product name">
                            <input type="number" min="0" class="box" required name="update_p_price" value="<?php echo htmlspecialchars($fetch_edit['price']); ?>" placeholder="Enter the product price">
                            <input type="number" min="0" class="box" required name="update_p_quantity" value="<?php echo htmlspecialchars($fetch_edit['quantity']); ?>" placeholder="Enter the product quantity">
                            <input type="file" class="box" required name="update_p_image" accept="image/png, image/jpg, image/jpeg">
                            <button type="submit" name="update_product" class="btn">Update the product</button>
                            <button type="reset" id="closeEdit" class="btn">Cancel</button>
                        </form>
                    </div>
                    <?php
                            }
                        }
                        echo "<script>document.getElementById('updateFormContainer').classList.remove('hidden');</script>";
                    }
                    ?>
                </section>
            <?php endif; ?>
            
            <div class="product-list-header" id="productListHeader">
                <h3>Product List</h3>
            </div>
            <div id="message" class="message"></div>
            <div class="product-table-container">
                <table class="product-table" id="productTable">
                    <thead>
                        <tr>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Product Price</th>
                            <th>Product Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($is_admin && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($row['image']); ?>" class="product-image"></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['price']); ?> Php</td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td> <!-- Ensure this line is here -->
                                <td class="action-buttons">
                                    <a href="Product.php?edit=<?php echo $row['Merchandise ID']; ?>" class="btn btn-update update-btn">Update</a>
                                    <form action="Product.php" method="post" style="display: inline;" onsubmit="return confirmDelete()">
                                        <input type="hidden" name="delete_product_id" value="<?php echo $row['Merchandise ID']; ?>">
                                        <button type="submit" class="btn btn-delete" name="delete_product">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
                </table>
            </div>
            
        </main>
    </div>

    <footer>
        <p>&copy; 2024 <a href="#" style="color: white;">Quantify</a> All rights reserved.</p>
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

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.toggle-btn');

            if (window.innerWidth <= 768 && !sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });

        // Hide elements when update button is clicked
        document.querySelectorAll('.update-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                window.location.href = this.href;
                document.getElementById('addProductForm').classList.add('hidden');
                document.getElementById('productListHeader').classList.add('hidden');
                document.getElementById('productTable').classList.add('hidden');
                document.getElementById('updateFormContainer').classList.remove('hidden');
            });
        });

        // Show elements when cancel button is clicked
        document.getElementById('closeEdit').addEventListener('click', function() {
            document.getElementById('addProductForm').classList.remove('hidden');
            document.getElementById('productListHeader').classList.remove('hidden');
            document.getElementById('productTable').classList.remove('hidden');
            document.getElementById('updateFormContainer').classList.add('hidden');
        });

        // Automatically show update form if edit parameter is present in URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('edit')) {
                document.getElementById('addProductForm').classList.add('hidden');
                document.getElementById('productListHeader').classList.add('hidden');
                document.getElementById('productTable').classList.add('hidden');
                document.getElementById('updateFormContainer').classList.remove('hidden');
            }
        });

        // Confirmation popup for delete action
        function confirmDelete() {
            return confirm('Are you sure you want to delete this product?');
        }
    </script>
</body>
</html>
