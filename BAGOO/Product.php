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
    if (isset($_POST['add_product'])) {
        // Handle add product
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_stock = $_POST['product_stock'];
        $product_category = $_POST['product_category'];
        $product_image = $_FILES['product_image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($product_image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file size
        if ($_FILES['product_image']['size'] > 3 * 1024 * 1024) {
            $message = 'Sorry, your file is too large.';
        } elseif (!in_array($imageFileType, ['jpeg', 'jpg', 'png', 'jfif'])) {
            $message = 'Sorry, only JPEG, PNG & JFIF files are allowed.';
        } else {
            $imageDimensions = getimagesize($_FILES['product_image']['tmp_name']);
            if ($imageDimensions[0] < 225 || $imageDimensions[1] < 224 || $imageDimensions[0] > 300 || $imageDimensions[1] > 300) {
                $message = 'Sorry, image dimensions should be between 225x224 and 300x300 pixels.';
            } else {
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }

                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                    $sql = "INSERT INTO product (`Merchandise ID`, name, price, stock, category, image) VALUES (NULL, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssss", $product_name, $product_price, $product_stock, $product_category, $target_file);
                    $stmt->execute();
                    $message = 'Product added successfully';
                } else {
                    $message = 'Sorry, there was an error uploading your file.';
                }
            }
        }

        // Redirect to avoid form resubmission
        header('Location: Product.php?message=' . urlencode($message));
        exit();
    } elseif (isset($_POST['update_product'])) {
        // Handle update product
        $product_id = $_POST['update_p_id'];
        $product_name = $_POST['update_p_name'];
        $product_price = $_POST['update_p_price'];
        $product_stock = $_POST['update_p_stock'];
        $product_category = $_POST['update_p_category'];
        $product_image = $_FILES['update_p_image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($product_image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file size
        if ($_FILES['update_p_image']['size'] > 3 * 1024 * 1024) {
            $message = 'Sorry, your file is too large.';
        } elseif (!in_array($imageFileType, ['jpeg', 'jpg', 'png', 'jfif'])) {
            $message = 'Sorry, only JPEG, PNG & JFIF files are allowed.';
        } else {
            $imageDimensions = getimagesize($_FILES['update_p_image']['tmp_name']);
            if ($imageDimensions[0] < 225 || $imageDimensions[1] < 224 || $imageDimensions[0] > 300 || $imageDimensions[1] > 300) {
                $message = 'Sorry, image dimensions should be between 225x224 and 300x300 pixels.';
            } else {
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }

                if (move_uploaded_file($_FILES['update_p_image']['tmp_name'], $target_file)) {
                    $sql = "UPDATE product SET name=?, price=?, stock=?, category=?, image=? WHERE `Merchandise ID`=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssi", $product_name, $product_price, $product_stock, $product_category, $target_file, $product_id);
                    $stmt->execute();
                    $message = 'Product updated successfully';
                } else {
                    $message = 'Sorry, there was an error uploading your file.';
                }
            }
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
    $sql = "SELECT `Merchandise ID`, name, price, stock, category, image FROM product";
    $result = $conn->query($sql);
} else {
    $sql = "SELECT name, price, stock, category, image FROM product";
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
                <form action="Product.php" method="post" enctype="multipart/form-data" id="addProductForm" onsubmit="return validateImage()">
                    <input type="text" name="product_name" class="box" placeholder="Enter the product name" required>
                    <input type="number" name="product_price" class="box" placeholder="Enter the product price" required>
                    <input type="number" name="product_stock" class="box" placeholder="Enter the product stock" required>
                    <select name="product_category" class="box" required>
                        <option value="Top">Top</option>
                        <option value="Bottom">Bottom</option>
                        <option value="Set of Uniform">Set of Uniform</option>
                    </select>
                    <input type="file" name="product_image" class="box" accept=".jpeg,.jpg,.png,.jfif" required>
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
                        <form action="Product.php" method="post" enctype="multipart/form-data" onsubmit="return validateImage()">
                            <input type="hidden" name="update_p_id" value="<?php echo $fetch_edit['Merchandise ID']; ?>">
                            <input type="text" class="box" required name="update_p_name" value="<?php echo htmlspecialchars($fetch_edit['name']); ?>" placeholder="Enter the product name">
                            <input type="number" min="0" class="box" required name="update_p_price" value="<?php echo htmlspecialchars($fetch_edit['price']); ?>" placeholder="Enter the product price">
                            <input type="number" min="0" class="box" required name="update_p_stock" value="<?php echo htmlspecialchars($fetch_edit['stock']); ?>" placeholder="Enter the product stock">
                            <select name="update_p_category" class="box" required>
                                <option value="Top" <?php echo $fetch_edit['category'] == 'Top' ? 'selected' : ''; ?>>Top</option>
                                <option value="Bottom" <?php echo $fetch_edit['category'] == 'Bottom' ? 'selected' : ''; ?>>Bottom</option>
                                <option value="Set of Uniform" <?php echo $fetch_edit['category'] == 'Set of Uniform' ? 'selected' : ''; ?>>Set of Uniform</option>
                            </select>
                            <input type="file" class="box" required name="update_p_image" accept="image/png, image/jpg, image/jpeg, image/jfif">
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
            
            <?php if (!$is_admin): ?>
            <div class="product-list">
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p>Price: <?php echo htmlspecialchars($row['price']); ?> Php</p>
                    <p>Stock: <?php echo htmlspecialchars($row['stock']); ?></p>
                    <p>Category: <?php echo htmlspecialchars($row['category']); ?></p>
                    <?php if ($row['stock'] > 0): ?>
                        <button class="btn" onclick="showOrderForm()"> Order </button>
                    <?php else: ?>
                        <button class="btn" disabled> Reserved </button>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>

            <?php if ($is_admin): ?>
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
                            <th>Product Stock</th>
                            <th>Product Category</th>
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
                                <td><?php echo htmlspecialchars($row['stock']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td class="action-buttons">
                                    <a href="Product.php?edit=<?php echo $row['Merchandise ID']; ?>" class="btn btn-update update-btn">Update</a>
                                    <form action="Product.php" method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
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
            <?php endif; ?>
        </main>
    </div>

    <!-- Order Form Modal -->
    <div id="orderFormModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeOrderForm()">&times;</span>
            <h2>Order Form</h2>
            <form id="orderForm">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required><br><br>
                <label for="address">Email:</label>
                <input type="text" id="email" name="email" required><br><br>
                <label for="size">Size:</label><br>
                <input type="radio" id="small" name="size" value="small" required>
                <label for="small">Small</label><br>
                <input type="radio" id="medium" name="size" value="medium">
                <label for="medium">Medium</label><br>
                <input type="radio" id="large" name="size" value="large">
                <label for="large">Large</label><br>
                <input type="radio" id="xl" name="size" value="xl">
                <label for="xl">XL</label><br>
                <input type="radio" id="2xl" name="size" value="2xl">
                <label for="2xl">2XL</label><br><br>
                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 <a href="#" style="color: white;">Quantify</a> All rights reserved.</p>
    </footer>

    <script>
    function validateImage() {
        const imageInput = document.querySelector('input[type="file"]');
        const file = imageInput.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/jfif'];

        if (file.size > 3 * 1024 * 1024) {
            alert('File size must not exceed 3MB.');
            return false;
        }

        if (!validTypes.includes(file.type)) {
            alert('Only JPEG, PNG, and JFIF formats are allowed.');
            return false;
        }

        const img = new Image();
        img.onload = function() {
            if (this.width < 225 || this.height < 224 || this.width > 300 || this.height > 300) {
                alert('Image dimensions should be between 225x224 and 300x300 pixels.');
                return false;
            }
        };
        img.src = URL.createObjectURL(file);

        return true;
    }

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

    function showOrderForm() {
        document.getElementById('orderFormModal').style.display = "block";
    }

    function closeOrderForm() {
        document.getElementById('orderFormModal').style.display = "none";
    }

    window.onclick = function(event) {
        const modal = document.getElementById('orderFormModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
