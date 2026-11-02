<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>

<nav class="navbar navbar-expand-md navbar-light admin-navbar">
    <div class="container">
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Mở menu điều hướng"
        >
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPath === '/admin/' ? 'active' : '' ?>" href="/admin/" <?= $currentPath === '/admin/' ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-gauge-high me-1" aria-hidden="true"></i>Tổng quan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentPath, '/admin/categories') ? 'active' : '' ?>" href="/admin/categories/" <?= str_starts_with($currentPath, '/admin/categories') ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-tags me-1" aria-hidden="true"></i>Danh mục</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentPath, '/admin/products') ? 'active' : '' ?>" href="/admin/products/" <?= str_starts_with($currentPath, '/admin/products') ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-box-open me-1" aria-hidden="true"></i>Sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentPath, '/admin/suppliers') ? 'active' : '' ?>" href="/admin/suppliers/" <?= str_starts_with($currentPath, '/admin/suppliers') ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-truck-ramp-box me-1" aria-hidden="true"></i>Nhà cung cấp</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentPath, '/admin/orders') ? 'active' : '' ?>" href="/admin/orders/" <?= str_starts_with($currentPath, '/admin/orders') ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-clipboard-list me-1" aria-hidden="true"></i>Đơn hàng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentPath, '/admin/shippers') ? 'active' : '' ?>" href="/admin/shippers/" <?= str_starts_with($currentPath, '/admin/shippers') ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-truck-fast me-1" aria-hidden="true"></i>Người giao hàng</a>
                </li>
            </ul>
            <a class="btn btn-outline-primary btn-sm" href="/"><i class="fa-solid fa-arrow-up-right-from-square me-1" aria-hidden="true"></i>Xem cửa hàng</a>
        </div>
    </div>
</nav>