<?php
$pageTitle = 'Quản lý nhà cung cấp';
require_once '/var/www/src/config/database.php';

$sql = "
    SELECT
        s.SupplierID,
        s.SupplierName,
        s.ContactName,
        s.Phone,
        s.City,
        s.Country,
        COUNT(p.ProductID) AS ProductCount
    FROM suppliers s
    LEFT JOIN products p ON p.SupplierID = s.SupplierID
    GROUP BY
        s.SupplierID,
        s.SupplierName,
        s.ContactName,
        s.Phone,
        s.City,
        s.Country
    ORDER BY s.SupplierName
";

$result = $conn->query($sql);
$notice = $_GET['notice'] ?? '';

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<main class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h2 class="mb-1">Quản lý nhà cung cấp</h2>
            <p class="text-muted mb-0">Thông tin nhà cung cấp được sử dụng khi tạo và cập nhật sản phẩm.</p>
        </div>
        <a href="/admin/suppliers/create.php" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Thêm nhà cung cấp
        </a>
    </div>

    <?php if ($notice === 'deleted'): ?>
        <div class="alert alert-success">Đã xóa nhà cung cấp.</div>
    <?php elseif ($notice === 'in_use'): ?>
        <div class="alert alert-warning">Không thể xóa nhà cung cấp đang được gán cho sản phẩm.</div>
    <?php elseif ($notice === 'not_found'): ?>
        <div class="alert alert-warning">Nhà cung cấp không tồn tại hoặc đã được xóa.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nhà cung cấp</th>
                    <th>Người liên hệ</th>
                    <th>Điện thoại</th>
                    <th>Khu vực</th>
                    <th>Sản phẩm</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Chưa có nhà cung cấp. Hãy thêm nhà cung cấp trước khi tạo sản phẩm.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php while ($supplier = $result->fetch_assoc()): ?>
                        <?php $productCount = (int) $supplier['ProductCount']; ?>
                        <tr>
                            <td><?= (int) $supplier['SupplierID'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($supplier['SupplierName']) ?></td>
                            <td><?= htmlspecialchars($supplier['ContactName'] ?? '') ?></td>
                            <td><?= htmlspecialchars($supplier['Phone'] ?? '') ?></td>
                            <td>
                                <?= htmlspecialchars(implode(', ', array_filter([
                                    $supplier['City'] ?? '',
                                    $supplier['Country'] ?? ''
                                ]))) ?>
                            </td>
                            <td>
                                <span class="badge text-bg-light border">
                                    <?= $productCount ?> sản phẩm
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a
                                    href="/admin/suppliers/edit.php?id=<?= (int) $supplier['SupplierID'] ?>"
                                    class="btn btn-sm btn-warning"
                                    aria-label="Sửa <?= htmlspecialchars($supplier['SupplierName']) ?>"
                                >
                                    <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                                </a>
                                <?php if ($productCount === 0): ?>
                                    <form
                                        action="/admin/suppliers/delete.php"
                                        method="post"
                                        class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa nhà cung cấp này?');"
                                    >
                                        <input type="hidden" name="id" value="<?= (int) $supplier['SupplierID'] ?>">
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            aria-label="Xóa <?= htmlspecialchars($supplier['SupplierName']) ?>"
                                        >
                                            <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        disabled
                                        aria-label="Không thể xóa nhà cung cấp đang có sản phẩm"
                                        title="Nhà cung cấp đang được sử dụng bởi sản phẩm"
                                    >
                                        <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php
require_once '/var/www/src/includes/admin/footer.php';
$result->free();
$conn->close();
?>
