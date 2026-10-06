<?php
$pageTitle = 'Sửa sản phẩm';
require_once '/var/www/src/config/database.php';

$productID = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($productID <= 0) {
    header('Location: /admin/products/');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE ProductID = ?");
$stmt->bind_param('i', $productID);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: /admin/products/');
    exit;
}

$error = '';
$categories = $conn->query("SELECT CategoryID, CategoryName FROM categories ORDER BY CategoryName");
$suppliers = $conn->query("SELECT SupplierID, SupplierName FROM suppliers ORDER BY SupplierName");

// XỬ LÝ POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Nhánh Xóa ảnh
    if (isset($_POST['delete_image'])) {
        $imageID = (int) $_POST['delete_image'];

        try {
            $conn->begin_transaction();

            $sqlImage = "
                SELECT ProductImageID, ImageFile, IsPrimary, SortOrder
                FROM product_images
                WHERE ProductImageID = ? AND ProductID = ?
            ";
            $stmtImage = $conn->prepare($sqlImage);
            $stmtImage->bind_param('ii', $imageID, $productID);
            $stmtImage->execute();
            $imageToDelete = $stmtImage->get_result()->fetch_assoc();
            $stmtImage->close();

            if (!$imageToDelete) {
                throw new Exception('Không tìm thấy ảnh cần xóa.');
            }

            $sqlDelete = "
                DELETE FROM product_images
                WHERE ProductImageID = ? AND ProductID = ?
            ";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bind_param('ii', $imageID, $productID);
            $stmtDelete->execute();

            if ($stmtDelete->affected_rows !== 1) {
                throw new Exception('Không thể xóa ảnh.');
            }
            $stmtDelete->close();

            if ((int) $imageToDelete['IsPrimary'] === 1) {
                $sqlNewPrimary = "
                    UPDATE product_images
                    SET IsPrimary = 1
                    WHERE ProductImageID = (
                        SELECT ProductImageID
                        FROM (
                            SELECT ProductImageID
                            FROM product_images
                            WHERE ProductID = ?
                            ORDER BY SortOrder, ProductImageID
                            LIMIT 1
                        ) AS remaining_images
                    )
                ";
                $stmtNewPrimary = $conn->prepare($sqlNewPrimary);
                $stmtNewPrimary->bind_param('i', $productID);
                $stmtNewPrimary->execute();
                $stmtNewPrimary->close();
            }

            $deletedSortOrder = (int) $imageToDelete['SortOrder'];

            $sqlReorder = "
                UPDATE product_images
                SET SortOrder = SortOrder - 1
                WHERE ProductID = ? AND SortOrder > ?
            ";
            $stmtReorder = $conn->prepare($sqlReorder);
            $stmtReorder->bind_param('ii', $productID, $deletedSortOrder);
            $stmtReorder->execute();
            $stmtReorder->close();

            $conn->commit();

            $filePath = '/var/www/html/uploads/products/' . $imageToDelete['ImageFile'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            header('Location: /admin/products/edit.php?id=' . $productID . '&image_deleted=1');
            exit;

        } catch (Throwable $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }

    // 2. Nhánh Thêm ảnh
    elseif (isset($_POST['add_images'])) {
        $files = $_FILES['product_images'] ?? null;

        if (!$files || !isset($files['name']) || !is_array($files['name'])) {
            $error = 'Vui lòng chọn ít nhất một ảnh.';
        } else {
            $maxSize = 2 * 1024 * 1024;
            $extensionMap = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];
            $validImages = [];
            $fileCount = count($files['name']);
            $finfo = new finfo(FILEINFO_MIME_TYPE);

            for ($i = 0; $i < $fileCount; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    $error = 'Có lỗi xảy ra khi upload ảnh.';
                    break;
                }
                if ($files['size'][$i] > $maxSize) {
                    $error = 'Mỗi ảnh chỉ được có kích thước tối đa 2 MB.';
                    break;
                }

                $mimeType = $finfo->file($files['tmp_name'][$i]);

                if (!isset($extensionMap[$mimeType])) {
                    $error = 'Chỉ chấp nhận ảnh JPG, PNG hoặc WebP.';
                    break;
                }

                $extension = $extensionMap[$mimeType];
                $fileName = 'product-' . bin2hex(random_bytes(8)) . '.' . $extension;

                $validImages[] = [
                    'tmp_name'  => $files['tmp_name'][$i],
                    'file_name' => $fileName
                ];
            }

            if (!$error && count($validImages) === 0) {
                $error = 'Vui lòng chọn ít nhất một ảnh.';
            }
        }

        if (!$error) {
            $sqlImageState = "
                SELECT COUNT(*) AS ImageCount, COALESCE(MAX(SortOrder), 0) AS MaxSortOrder
                FROM product_images
                WHERE ProductID = ?
            ";
            $stmtImageState = $conn->prepare($sqlImageState);
            $stmtImageState->bind_param('i', $productID);
            $stmtImageState->execute();
            $imageState = $stmtImageState->get_result()->fetch_assoc();
            $stmtImageState->close();

            $imageCount = (int) $imageState['ImageCount'];
            $nextSortOrder = (int) $imageState['MaxSortOrder'] + 1;

            $movedFiles = [];

            try {
                $conn->begin_transaction();

                $sqlInsertImage = "
                    INSERT INTO product_images (ProductID, ImageFile, AltText, IsPrimary, SortOrder)
                    VALUES (?, ?, ?, ?, ?)
                ";
                $stmtInsertImage = $conn->prepare($sqlInsertImage);

                foreach ($validImages as $index => $image) {
                    $destination = '/var/www/html/uploads/products/' . $image['file_name'];

                    if (!move_uploaded_file($image['tmp_name'], $destination)) {
                        throw new Exception('Không thể lưu một trong các ảnh.');
                    }

                    $movedFiles[] = $destination;

                    $isPrimary = ($imageCount === 0 && $index === 0) ? 1 : 0;
                    $sortOrder = $nextSortOrder + $index;

                    $altText = $product['ProductName'] . ($isPrimary === 1 ? ' - ảnh chính' : ' - ảnh ' . $sortOrder);

                    $stmtInsertImage->bind_param('issii', $productID, $image['file_name'], $altText, $isPrimary, $sortOrder);

                    if (!$stmtInsertImage->execute()) {
                        throw new Exception('Không thể lưu thông tin ảnh.');
                    }
                }

                $stmtInsertImage->close();
                $conn->commit();

                header('Location: /admin/products/edit.php?id=' . $productID . '&images_added=1');
                exit;

            } catch (Throwable $e) {
                $conn->rollback();
                foreach ($movedFiles as $movedFile) {
                    if (file_exists($movedFile)) {
                        unlink($movedFile);
                    }
                }
                $error = $e->getMessage();
            }
        }
    }

    // 3. Nhánh Đặt ảnh chính
    elseif (isset($_POST['set_primary_image'])) {
        $imageID = (int) $_POST['set_primary_image'];

        try {
            $conn->begin_transaction();

            $sqlResetPrimary = "UPDATE product_images SET IsPrimary = 0 WHERE ProductID = ?";
            $stmtResetPrimary = $conn->prepare($sqlResetPrimary);
            $stmtResetPrimary->bind_param('i', $productID);
            $stmtResetPrimary->execute();
            $stmtResetPrimary->close();

            $sqlSetPrimary = "UPDATE product_images SET IsPrimary = 1 WHERE ProductImageID = ? AND ProductID = ?";
            $stmtSetPrimary = $conn->prepare($sqlSetPrimary);
            $stmtSetPrimary->bind_param('ii', $imageID, $productID);
            $stmtSetPrimary->execute();

            if ($stmtSetPrimary->affected_rows !== 1) {
                throw new Exception('Không thể đặt ảnh chính.');
            }

            $stmtSetPrimary->close();
            $conn->commit();

            header('Location: /admin/products/edit.php?id=' . $productID . '&primary_updated=1');
            exit;

        } catch (Throwable $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }

    // 4. Nhánh Cập nhật thông tin sản phẩm thông thường
    else {
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
                header('Location: /admin/products/');
                exit;
            }
            $error = 'Không thể cập nhật sản phẩm.';
            $stmt->close();
        }
    }
}

// Lấy danh sách hình ảnh của sản phẩm để hiển thị
$sqlImages = "
    SELECT ProductImageID, ImageFile, AltText, IsPrimary, SortOrder
    FROM product_images
    WHERE ProductID = ?
    ORDER BY SortOrder, ProductImageID
";
$stmtImages = $conn->prepare($sqlImages);
$stmtImages->bind_param('i', $productID);
$stmtImages->execute();
$productImages = $stmtImages->get_result();

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<div class="container mt-4">
    <h2>Sửa sản phẩm</h2>

    <!-- Feedback Hình ảnh -->
    <?php if (isset($_GET['primary_updated']) && $_GET['primary_updated'] === '1'): ?>
        <div class="alert alert-success">Đã cập nhật ảnh chính.</div>
    <?php endif; ?>

    <?php if (isset($_GET['images_added']) && $_GET['images_added'] === '1'): ?>
        <div class="alert alert-success">Đã thêm hình ảnh sản phẩm.</div>
    <?php endif; ?>

    <?php if (isset($_GET['image_deleted']) && $_GET['image_deleted'] === '1'): ?>
        <div class="alert alert-success">Đã xóa hình ảnh sản phẩm.</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Thêm enctype="multipart/form-data" -->
    <form method="post" class="mt-3" enctype="multipart/form-data">
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

        <!-- Bắt đầu phần quản lý hình ảnh -->
        <hr class="my-4">
        <h4 class="mb-3">Hình ảnh sản phẩm</h4>

        <div class="row">
            <?php while ($image = $productImages->fetch_assoc()): ?>
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <img src="/uploads/products/<?= htmlspecialchars($image['ImageFile']) ?>" class="card-img-top" alt="<?= htmlspecialchars($image['AltText'] ?? '') ?>">
                        <div class="card-body">
                            <small class="text-muted">Thứ tự: <?= $image['SortOrder'] ?></small>

                            <?php if ((int) $image['IsPrimary'] === 1): ?>
                                <div class="mt-2">
                                    <span class="badge bg-success">Ảnh chính</span>
                                    <button type="submit" class="btn btn-outline-danger btn-sm mt-2" name="delete_image" value="<?= $image['ProductImageID'] ?>" formaction="/admin/products/edit.php?id=<?= $productID ?>" formmethod="post" onclick="return confirm('Bạn có chắc muốn xóa ảnh này?');">Xóa ảnh</button>
                                </div>
                            <?php else: ?>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-outline-primary btn-sm mb-1" name="set_primary_image" value="<?= $image['ProductImageID'] ?>" formaction="/admin/products/edit.php?id=<?= $productID ?>" formmethod="post">Đặt làm ảnh chính</button>
                                    <button type="submit" class="btn btn-outline-danger btn-sm" name="delete_image" value="<?= $image['ProductImageID'] ?>" formaction="/admin/products/edit.php?id=<?= $productID ?>" formmethod="post" onclick="return confirm('Bạn có chắc muốn xóa ảnh này?');">Xóa ảnh</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="mb-3 mt-3">
            <label for="productImages" class="form-label">Thêm hình ảnh</label>
            <input type="file" class="form-control" id="productImages" name="product_images[]" accept="image/jpeg,image/png,image/webp" multiple>
            <div class="form-text">Chấp nhận JPG, PNG hoặc WebP. Mỗi ảnh tối đa 2 MB.</div>
        </div>

        <button type="submit" class="btn btn-outline-success mb-4" name="add_images" value="1">Thêm ảnh</button>
        <!-- Kết thúc phần quản lý hình ảnh -->

        <hr class="my-4">
        <button type="submit" class="btn btn-warning">Cập nhật</button>
        <a href="/admin/products/" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php
require_once '/var/www/src/includes/admin/footer.php';
$conn->close();
?>