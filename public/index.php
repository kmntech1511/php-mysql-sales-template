<?php
require_once '/var/www/src/config/session.php';
require_once '/var/www/src/config/database.php';

$pageTitle = 'CellPT | Mua sắm thiết bị điện tử';

$categoryResult = $conn->query("
    SELECT
        c.CategoryID,
        c.CategoryName,
        COUNT(p.ProductID) AS ProductCount
    FROM categories c
    LEFT JOIN products p
        ON p.CategoryID = c.CategoryID
       AND p.IsActive = 1
    GROUP BY c.CategoryID, c.CategoryName
    ORDER BY c.CategoryName
");

$categories = [];
$categoryProducts = [];

while ($category = $categoryResult->fetch_assoc()) {
    $categoryID = (int) $category['CategoryID'];
    $category['ProductCount'] = (int) $category['ProductCount'];
    $categories[] = $category;
    $categoryProducts[$categoryID] = [];
}
$categoryResult->free();

$productResult = $conn->query("
    SELECT
        p.ProductID,
        p.ProductCode,
        p.ProductName,
        p.Price,
        p.StockQuantity,
        p.CategoryID,
        c.CategoryName,
        (
            SELECT pi.ImageFile
            FROM product_images pi
            WHERE pi.ProductID = p.ProductID
              ORDER BY pi.IsPrimary DESC, pi.SortOrder, pi.ProductImageID
            LIMIT 1
        ) AS ImageFile
    FROM products p
    INNER JOIN categories c ON c.CategoryID = p.CategoryID
    WHERE p.IsActive = 1
    ORDER BY p.ProductID DESC
");

$featuredProduct = null;
while ($product = $productResult->fetch_assoc()) {
    $categoryID = (int) $product['CategoryID'];
    $product['ProductID'] = (int) $product['ProductID'];
    $product['StockQuantity'] = (int) $product['StockQuantity'];
    $categoryProducts[$categoryID][] = $product;

    if ($featuredProduct === null && !empty($product['ImageFile'])) {
        $featuredProduct = $product;
    }
}
$productResult->free();

if ($featuredProduct === null) {
    foreach ($categoryProducts as $products) {
        if (!empty($products)) {
            $featuredProduct = $products[0];
            break;
        }
    }
}

require_once '/var/www/src/includes/frontend/header.php';
require_once '/var/www/src/includes/frontend/navbar.php';
?>

<main class="store-home">
    <section class="store-home-hero">
        <div class="container">
            <div class="store-home-hero-panel">
                <div class="row align-items-center g-0">
                    <div class="col-lg-7">
                        <div class="store-home-hero-copy">
                            <div class="store-eyebrow mb-3">
                                <i class="fa-solid fa-bolt me-2" aria-hidden="true"></i>Góc công nghệ của bạn
                            </div>
                            <h1>Thiết bị phù hợp<br class="d-none d-md-block"> cho mỗi ngày.</h1>
                            <p>
                                Khám phá điện thoại, máy tính và phụ kiện theo nhu cầu.
                                Xem thông tin, giá bán và tình trạng sản phẩm ngay trên cửa hàng.
                            </p>

                            <form action="/products.php" method="get" class="store-home-search">
                                <label class="visually-hidden" for="home-keyword">Tìm sản phẩm</label>
                                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                <input
                                    type="search"
                                    name="keyword"
                                    id="home-keyword"
                                    placeholder="Bạn đang tìm sản phẩm nào?"
                                >
                                <button type="submit" class="btn btn-primary">
                                    Tìm kiếm
                                </button>
                            </form>

                            <a href="/products.php" class="store-home-all-link">
                                Xem tất cả sản phẩm
                                <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="store-home-hero-visual">
                            <?php if ($featuredProduct !== null && !empty($featuredProduct['ImageFile'])): ?>
                                <img
                                    src="/uploads/products/<?= htmlspecialchars($featuredProduct['ImageFile']) ?>"
                                    alt="<?= htmlspecialchars($featuredProduct['ProductName']) ?>"
                                >
                            <?php else: ?>
                                <div class="store-home-hero-placeholder">
                                    <i class="fa-solid fa-laptop" aria-hidden="true"></i>
                                    <span>Khám phá thiết bị mới</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($categories)): ?>
        <nav class="container store-home-category-nav" aria-label="Danh mục sản phẩm">
            <div class="store-home-category-nav-title">
                <i class="fa-solid fa-layer-group" aria-hidden="true"></i>
                <span>Danh mục</span>
            </div>
            <div class="store-home-category-links">
                <?php foreach ($categories as $category): ?>
                    <?php
                    $categoryName = mb_strtolower($category['CategoryName']);
                    $categoryIcon = match (true) {
                        str_contains($categoryName, 'điện thoại') => 'fa-mobile-screen-button',
                        str_contains($categoryName, 'máy tính') => 'fa-laptop',
                        str_contains($categoryName, 'phụ kiện') => 'fa-headphones',
                        default => 'fa-box'
                    };
                    ?>
                    <a href="#category-<?= (int) $category['CategoryID'] ?>">
                        <i class="fa-solid <?= $categoryIcon ?>" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($category['CategoryName']) ?></span>
                        <small><?= (int) $category['ProductCount'] ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>
    <?php endif; ?>

    <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $category): ?>
            <?php
            $categoryID = (int) $category['CategoryID'];
            $products = array_slice($categoryProducts[$categoryID] ?? [], 0, 4);
            $categoryName = mb_strtolower($category['CategoryName']);
            $categoryIcon = match (true) {
                str_contains($categoryName, 'điện thoại') => 'fa-mobile-screen-button',
                str_contains($categoryName, 'máy tính') => 'fa-laptop',
                str_contains($categoryName, 'phụ kiện') => 'fa-headphones',
                default => 'fa-box'
            };
            ?>
            <section class="container store-home-product-section" id="category-<?= $categoryID ?>">
                <div class="store-home-section-heading">
                    <div>
                        <span class="store-home-section-icon">
                            <i class="fa-solid <?= $categoryIcon ?>" aria-hidden="true"></i>
                        </span>
                        <div>
                            <h2><?= htmlspecialchars($category['CategoryName']) ?></h2>
                        </div>
                    </div>
                    <a
                        href="/products.php?category=<?= $categoryID ?>"
                        class="store-home-section-link"
                    >
                        Xem tất cả
                        <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>

                <?php if (!empty($products)): ?>
                    <div class="row g-3 g-lg-4">
                        <?php foreach ($products as $product): ?>
                            <div class="col-6 col-lg-3">
                                <article class="store-product-card">
                                    <a
                                        class="store-product-image"
                                        href="/product-detail.php?id=<?= (int) $product['ProductID'] ?>"
                                        aria-label="Xem <?= htmlspecialchars($product['ProductName']) ?>"
                                    >
                                        <?php if (!empty($product['ImageFile'])): ?>
                                            <img
                                                src="/uploads/products/<?= htmlspecialchars($product['ImageFile']) ?>"
                                                alt="<?= htmlspecialchars($product['ProductName']) ?>"
                                                loading="lazy"
                                            >
                                        <?php else: ?>
                                            <span class="store-product-placeholder">
                                                <i class="fa-regular fa-image" aria-hidden="true"></i>
                                                Chưa có hình ảnh
                                            </span>
                                        <?php endif; ?>
                                    </a>
                                    <div class="store-product-body">
                                        <span class="store-product-code">
                                            <?= htmlspecialchars($product['ProductCode']) ?>
                                        </span>
                                        <h3>
                                            <a href="/product-detail.php?id=<?= (int) $product['ProductID'] ?>">
                                                <?= htmlspecialchars($product['ProductName']) ?>
                                            </a>
                                        </h3>
                                        <div class="store-product-meta">
                                            <?php if ($product['StockQuantity'] > 0): ?>
                                                <span class="store-stock-in">
                                                    <i class="fa-solid fa-check" aria-hidden="true"></i> Còn hàng
                                                </span>
                                            <?php else: ?>
                                                <span class="store-stock-out">Tạm hết hàng</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="store-product-price">
                                            <?= number_format((float) $product['Price'], 0, ',', '.') ?> đ
                                        </div>
                                        <a
                                            href="/product-detail.php?id=<?= (int) $product['ProductID'] ?>"
                                            class="btn btn-outline-store w-100"
                                        >
                                            Xem sản phẩm
                                            <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="store-home-empty">
                        Sản phẩm trong danh mục này đang được cập nhật.
                    </div>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <section class="container store-home-empty">
            Danh mục sản phẩm đang được cập nhật.
            <a href="/products.php">Xem danh sách sản phẩm</a>
        </section>
    <?php endif; ?>

    <section class="container store-home-about">
        <div>
            <span class="store-eyebrow">CellPT</span>
            <h2>Mua sắm thiết bị công nghệ dễ dàng hơn.</h2>
            <p>
                Tìm kiếm theo danh mục, tham khảo giá và tình trạng hàng,
                sau đó đặt sản phẩm trực tiếp từ cửa hàng trực tuyến.
            </p>
        </div>
        <a href="/products.php" class="btn btn-primary">
            <i class="fa-solid fa-bag-shopping me-2" aria-hidden="true"></i>
            Khám phá sản phẩm
        </a>
    </section>
</main>

<?php
require_once '/var/www/src/includes/frontend/footer.php';
?>
