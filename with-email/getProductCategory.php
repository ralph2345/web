<?php
require 'config.php'; // Include your database connection

if (isset($_GET['merchandise_id'])) {
    $merchandise_id = intval($_GET['merchandise_id']);

    $sql = "SELECT category FROM product WHERE `Merchandise ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $merchandise_id);
    $stmt->execute();
    $stmt->bind_result($category);
    $stmt->fetch();

    echo json_encode(['category' => $category]);
}
?>
