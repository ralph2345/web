<?php
session_start();
require 'config.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        // Handle add product
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_image = $_FILES['product_image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($product_image);

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            $sql = "INSERT INTO product (`Merchandise ID`, name, price, image) VALUES (NULL, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $product_name, $product_price, $target_file);
            $stmt->execute();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } elseif (isset($_POST['update_product'])) {
        // Handle update product
        $product_id = $_POST['update_p_id'];
        $product_name = $_POST['update_p_name'];
        $product_price = $_POST['update_p_price'];
        $product_image = $_FILES['update_p_image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($product_image);

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        if (move_uploaded_file($_FILES['update_p_image']['tmp_name'], $target_file)) {
            $sql = "UPDATE product SET name=?, price=?, image=? WHERE `Merchandise ID`=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $product_name, $product_price, $target_file, $product_id);
            $stmt->execute();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } elseif (isset($_POST['delete_product'])) {
        // Handle delete product
        $product_id = $_POST['delete_product_id'];
        $sql = "DELETE FROM product WHERE `Merchandise ID`=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
    }
}
?>
