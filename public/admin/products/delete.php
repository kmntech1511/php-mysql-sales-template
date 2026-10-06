<?php
require_once '/var/www/src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/products/');
    exit;
}

$productID = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($productID > 0) {
    $conn->begin_transaction();

    try {
        $conn->query("DELETE FROM orderdetail WHERE ProductID = $productID");
        $stmt = $conn->prepare("DELETE FROM products WHERE ProductID = ?");
        $stmt->bind_param('i', $productID);

        if ($stmt->execute()) {
            $conn->commit();
            $stmt->close();
            $conn->close();
            header('Location: /admin/products/');
            exit;
        }

        $conn->rollback();
        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        die('Không thể xóa sản phẩm: ' . $e->getMessage());
    }
}

$conn->close();
die('Không thể xóa sản phẩm do ràng buộc dữ liệu đơn hàng.');