<?php
$pageTitle = 'Thêm nhà cung cấp';
require_once '/var/www/src/config/database.php';

$error = '';
$fields = [
    'supplier_name' => '',
    'contact_name' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'postal_code' => '',
    'country' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $field => $default) {
        $fields[$field] = trim($_POST[$field] ?? $default);
    }

    if ($fields['supplier_name'] === '') {
        $error = 'Tên nhà cung cấp không được để trống.';
    } else {
        $sql = "
            INSERT INTO suppliers (
                SupplierName,
                ContactName,
                Phone,
                Address,
                City,
                PostalCode,
                Country
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sssssss',
            $fields['supplier_name'],
            $fields['contact_name'],
            $fields['phone'],
            $fields['address'],
            $fields['city'],
            $fields['postal_code'],
            $fields['country']
        );

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: /admin/suppliers/');
            exit;
        }

        $error = 'Không thể thêm nhà cung cấp.';
        $stmt->close();
    }
}

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<main class="container mt-4">
    <h2 class="mb-1">Thêm nhà cung cấp</h2>
    <p class="text-muted mb-4">Nhập thông tin đơn vị cung cấp sản phẩm cho cửa hàng.</p>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="supplier_name" class="form-label">Tên nhà cung cấp <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="supplier_name" name="supplier_name" maxlength="200" value="<?= htmlspecialchars($fields['supplier_name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="contact_name" class="form-label">Người liên hệ</label>
                <input type="text" class="form-control" id="contact_name" name="contact_name" maxlength="100" value="<?= htmlspecialchars($fields['contact_name']) ?>">
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="tel" class="form-control" id="phone" name="phone" maxlength="20" value="<?= htmlspecialchars($fields['phone']) ?>">
            </div>
            <div class="col-md-6">
                <label for="address" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="address" name="address" maxlength="200" value="<?= htmlspecialchars($fields['address']) ?>">
            </div>
            <div class="col-md-4">
                <label for="city" class="form-label">Thành phố</label>
                <input type="text" class="form-control" id="city" name="city" maxlength="100" value="<?= htmlspecialchars($fields['city']) ?>">
            </div>
            <div class="col-md-4">
                <label for="postal_code" class="form-label">Mã bưu chính</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code" maxlength="20" value="<?= htmlspecialchars($fields['postal_code']) ?>">
            </div>
            <div class="col-md-4">
                <label for="country" class="form-label">Quốc gia</label>
                <input type="text" class="form-control" id="country" name="country" maxlength="100" value="<?= htmlspecialchars($fields['country']) ?>">
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Lưu nhà cung cấp
            </button>
            <a href="/admin/suppliers/" class="btn btn-outline-secondary">Hủy</a>
        </div>
    </form>
</main>

<?php
require_once '/var/www/src/includes/admin/footer.php';
$conn->close();
?>
