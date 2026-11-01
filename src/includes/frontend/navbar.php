<?php
$cartCount = array_sum($_SESSION['cart'] ?? []);
$isLoggedIn = isset($_SESSION['customer_id']);

$customerName =
    $_SESSION['customer_name'] ?? '';

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>

<nav class="navbar navbar-expand-lg store-navbar">

    <div class="container">

        <a class="navbar-brand" href="/" aria-label="CellPT - Trang chủ">
            <span class="store-brand-mark" aria-hidden="true">C</span>
            <span>CellPT</span>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#frontendNavbar"
            aria-controls="frontendNavbar"
            aria-expanded="false"
            aria-label="Mở menu điều hướng"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div
            class="collapse navbar-collapse"
            id="frontendNavbar"
        >

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link <?= $currentPath === '/' ? 'active' : '' ?>" href="/" <?= $currentPath === '/' ? 'aria-current="page"' : '' ?>>
                        <i class="fa-solid fa-house me-1" aria-hidden="true"></i>
                        Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= in_array($currentPath, ['/products.php', '/products-api.php', '/product-detail.php'], true) ? 'active' : '' ?>" href="/products.php" <?= in_array($currentPath, ['/products.php', '/products-api.php', '/product-detail.php'], true) ? 'aria-current="page"' : '' ?>>
                        <i class="fa-solid fa-mobile-screen-button me-1" aria-hidden="true"></i>
                        Sản phẩm
                    </a>
                </li>

            </ul>

            <div class="d-flex gap-2">

                <a
                    class="btn btn-outline-store btn-sm"
                    href="/cart.php"
                >
                    <i class="fa-solid fa-cart-shopping me-1" aria-hidden="true"></i>
                    Giỏ hàng <span class="badge text-bg-primary rounded-pill"><?= (int) $cartCount ?></span>
                </a>
<?php if ($isLoggedIn): ?>

    <span class="align-self-center text-secondary small">
        <?= htmlspecialchars($customerName) ?>
    </span>

    <a
        class="btn btn-outline-secondary btn-sm"
        href="/logout.php"
    >
        <i class="fa-solid fa-arrow-right-from-bracket me-1" aria-hidden="true"></i>
        Đăng xuất
    </a>

<?php else: ?>
    <div class="dropdown">
        <button
            class="btn btn-store btn-sm dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            <i class="fa-regular fa-user me-1" aria-hidden="true"></i>
            Đăng nhập
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="/login.php">
                    <i class="fa-solid fa-right-to-bracket me-2 text-primary" aria-hidden="true"></i>
                    Đăng nhập
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="/register.php">
                    <i class="fa-solid fa-user-plus me-2 text-primary" aria-hidden="true"></i>
                    Đăng ký tài khoản
                </a>
            </li>
        </ul>
    </div>

<?php endif; ?>

            </div>

        </div>

    </div>

</nav>