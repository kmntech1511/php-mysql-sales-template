<?php
$pageTitle = 'Sửa sản phẩm';
require_once '/var/www/src/config/database.php';

$productID = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($productID <= 0) {
    header('Location: /products/');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE ProductID = ?");
$stmt->bind_param('i', $productID);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: /products/');
    exit;
}

$error = '';
$categories = $conn->query("SELECT CategoryID, CategoryName FROM categories ORDER BY CategoryName");
$suppliers = $conn->query("SELECT SupplierID, SupplierName FROM suppliers ORDER BY SupplierName");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productCode = trim($_POST['product_code'] ?? '');
    $productName = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $unit = trim($_POST['unit'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stockQuantity = (int) ($_POST['stock_quantity'] ?? 0);
    $categoryID = (int) ($_POST['category_id'] ?? 0);
    $supplierID = (int) ($_POST['supplier_id'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($productCode === '' || $productName === '') {
        $error = 'Vui lòng nhập đầy đủ mã và tên sản phẩm.';
    } else {
        $sql = "UPDATE products SET ProductCode = ?, ProductName = ?, Description = ?, Unit = ?, Price = ?, StockQuantity = ?, IsActive = ?, SupplierID = ?, CategoryID = ? WHERE ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssdiiiii', $productCode, $productName, $description, $unit, $price, $stockQuantity, $isActive, $supplierID, $categoryID, $productID);

        if ($stmt->execute()) {
            header('Location: /products/');
            exit;
        }
        $error = 'Không thể cập nhật sản phẩm.';
        $stmt->close();
    }
}

require_once '/var/www/src/includes/header.php';
require_once '/var/www/src/includes/navbar.php';
?>

<div class="container mt-4">
    <h2>Sửa sản phẩm</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="mt-3">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Mã sản phẩm</label>
                <input type="text" name="product_code" class="form-control" value="<?= htmlspecialchars($product['ProductCode']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tên sản phẩm</label>
                <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($product['ProductName']) ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['Description'] ?? '') ?></textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Đơn vị tính</label>
                <input type="text" name="unit" class="form-control" value="<?= htmlspecialchars($product['Unit'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Giá bán</label>
                <input type="number" step="1000" name="price" class="form-control" value="<?= (float) $product['Price'] ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Số lượng tồn kho</label>
                <input type="number" name="stock_quantity" class="form-control" value="<?= (int) $product['StockQuantity'] ?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Danh mục</label>
                <select name="category_id" class="form-select" required>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['CategoryID'] ?>" <?= $cat['CategoryID'] == $product['CategoryID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['CategoryName']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nhà cung cấp</label>
                <select name="supplier_id" class="form-select" required>
                    <?php while ($sup = $suppliers->fetch_assoc()): ?>
                        <option value="<?= $sup['SupplierID'] ?>" <?= $sup['SupplierID'] == $product['SupplierID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sup['SupplierName']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" <?= (int) $product['IsActive'] === 1 ? 'checked' : '' ?>>
            <label class="form-check-label" for="isActive">Đang kinh doanh</label>
        </div>

        <button type="submit" class="btn btn-warning">Cập nhật</button>
        <a href="/products/" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php
require_once '/var/www/src/includes/footer.php';
$conn->close();
?>