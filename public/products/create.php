<?php
$pageTitle = 'Thêm sản phẩm';
require_once '/var/www/src/config/database.php';

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
    } elseif ($categoryID <= 0 || $supplierID <= 0) {
        $error = 'Vui lòng chọn danh mục và nhà cung cấp.';
    } else {
        $sql = "INSERT INTO products (ProductCode, ProductName, Description, Unit, Price, StockQuantity, IsActive, SupplierID, CategoryID)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssdiiii', $productCode, $productName, $description, $unit, $price, $stockQuantity, $isActive, $supplierID, $categoryID);

        if ($stmt->execute()) {
            header('Location: /products/');
            exit;
        }
        $error = 'Không thể thêm sản phẩm.';
        $stmt->close();
    }
}

require_once '/var/www/src/includes/header.php';
require_once '/var/www/src/includes/navbar.php';
?>

<div class="container mt-4">
    <h2>Thêm sản phẩm mới</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="mt-3">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Mã sản phẩm</label>
                <input type="text" name="product_code" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tên sản phẩm</label>
                <input type="text" name="product_name" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Đơn vị tính</label>
                <input type="text" name="unit" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Giá bán</label>
                <input type="number" step="1000" name="price" class="form-control" value="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Số lượng tồn kho</label>
                <input type="number" name="stock_quantity" class="form-control" value="0" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Danh mục</label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['CategoryID'] ?>"><?= htmlspecialchars($cat['CategoryName']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nhà cung cấp</label>
                <select name="supplier_id" class="form-select" required>
                    <option value="">-- Chọn nhà cung cấp --</option>
                    <?php while ($sup = $suppliers->fetch_assoc()): ?>
                        <option value="<?= $sup['SupplierID'] ?>"><?= htmlspecialchars($sup['SupplierName']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
            <label class="form-check-label" for="isActive">Đang kinh doanh</label>
        </div>

        <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
        <a href="/products/" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php
require_once '/var/www/src/includes/footer.php';
$conn->close();
?>