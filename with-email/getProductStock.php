<?php
require 'config.php';

if (isset($_GET['merchandise_id'])) {
    $merchandise_id = intval($_GET['merchandise_id']);
    $sql = "SELECT small_stock, medium_stock, large_stock, xl_stock, xxl_stock FROM product WHERE `Merchandise ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $merchandise_id);
    $stmt->execute();
    $stmt->bind_result($small_stock, $medium_stock, $large_stock, $xl_stock, $xxl_stock);
    $stmt->fetch();
    $stmt->close();

    echo json_encode([
        'small_stock' => $small_stock,
        'medium_stock' => $medium_stock,
        'large_stock' => $large_stock,
        'xl_stock' => $xl_stock,
        'xxl_stock' => $xxl_stock,
    ]);
}
?>
