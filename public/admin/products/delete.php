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
        $stmtImages = $conn->prepare("SELECT ImageFile FROM product_images WHERE ProductID = ?");
        $stmtImages->bind_param('i', $productID);
        $stmtImages->execute();
        $imageFiles = [];
        $imageResult = $stmtImages->get_result();
        while ($image = $imageResult->fetch_assoc()) $imageFiles[] = $image['ImageFile'];
        $stmtImages->close();

        $stmtOrderDetails = $conn->prepare("DELETE FROM orderdetail WHERE ProductID = ?");
        $stmtOrderDetails->bind_param('i', $productID);
        $stmtOrderDetails->execute();
        $stmtOrderDetails->close();
        $stmt = $conn->prepare("DELETE FROM products WHERE ProductID = ?");
        $stmt->bind_param('i', $productID);

        if ($stmt->execute()) {
            $conn->commit();
            $stmt->close();
            foreach ($imageFiles as $imageFile) {
                $stmtReferences = $conn->prepare("SELECT COUNT(*) AS ReferenceCount FROM product_images WHERE ImageFile = ?");
                $stmtReferences->bind_param('s', $imageFile);
                $stmtReferences->execute();
                $referenceCount = (int) $stmtReferences->get_result()->fetch_assoc()['ReferenceCount'];
                $stmtReferences->close();
                $filePath = '/var/www/html/uploads/products/' . $imageFile;
                if ($referenceCount === 0 && is_file($filePath)) unlink($filePath);
            }
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
