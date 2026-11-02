<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'Quản lý bán hàng') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --admin-blue: #4a90c2;
            --admin-blue-dark: #2e6f9b;
            --admin-blue-soft: #eaf4fb;
            --admin-ink: #243746;
            --admin-muted: #687b89;
            --admin-border: #e1eaf0;
            --admin-background: #f5f9fc;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--admin-ink);
            background: var(--admin-background);
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .admin-heading {
            position: relative;
            overflow: hidden;
            padding: 30px 0 26px;
            color: var(--admin-ink);
            background:
                linear-gradient(90deg, rgba(255, 255, 255, .94), rgba(255, 255, 255, .79)),
                url("https://wallpapers.com/images/hd/light-color-background-vmkuihk29pi1xq65.jpg") center 48% / cover no-repeat;
            border-bottom: 1px solid var(--admin-border);
        }

        .admin-heading::after {
            position: absolute;
            top: -110px;
            right: 7%;
            width: 290px;
            height: 290px;
            border: 1px solid rgba(46, 111, 155, .12);
            border-radius: 50%;
            box-shadow: 0 0 0 32px rgba(46, 111, 155, .035), 0 0 0 66px rgba(46, 111, 155, .025);
            content: "";
            pointer-events: none;
        }

        .admin-heading .container {
            position: relative;
            z-index: 1;
            padding-left: 12px;
            padding-right: 12px;
        }

        .admin-heading h1 {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
            font-size: clamp(1.35rem, 2.4vw, 1.8rem);
            font-weight: 650;
            letter-spacing: -.02em;
        }

        .admin-brand-mark {
            display: inline-grid;
            width: 42px;
            height: 42px;
            flex: 0 0 auto;
            place-items: center;
            color: #fff;
            background: var(--admin-blue-dark);
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(27, 55, 75, .12);
            font-size: 1.25rem;
            font-weight: 800;
        }

        .admin-heading p {
            margin: 8px 0 0 54px;
            color: var(--admin-muted);
            font-size: .95rem;
        }

        .admin-navbar {
            padding: 0;
            background: #fff;
            border-bottom: 1px solid var(--admin-border);
            box-shadow: 0 2px 8px rgba(27, 55, 32, .04);
        }

        .admin-navbar .navbar-nav {
            gap: 4px;
        }

        .admin-navbar .nav-link {
            padding: 19px 13px;
            color: #4b584e;
            font-size: .94rem;
            font-weight: 550;
            border-bottom: 2px solid transparent;
            transition: color .15s ease, background-color .15s ease;
        }

        .admin-navbar .nav-link:hover,
        .admin-navbar .nav-link.active {
            color: var(--admin-blue-dark);
            background: var(--admin-blue-soft);
            border-bottom-color: var(--admin-blue);
        }

        .admin-navbar .navbar-toggler {
            margin: 8px 0;
            border-color: var(--admin-border);
        }

        body > .container,
        body > main.container {
            width: min(100% - 32px, 1320px);
            max-width: 1320px;
            margin-top: 32px !important;
            margin-bottom: 36px;
            padding: 26px;
            background: #fff;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            box-shadow: 0 3px 14px rgba(27, 55, 32, .045);
        }

        body > main.container {
            padding-top: 36px !important;
            padding-bottom: 36px !important;
        }

        body > .container > h2,
        body > main.container h1,
        body > main.container h2 {
            color: var(--admin-ink);
            font-weight: 650;
            letter-spacing: -.02em;
        }

        .admin-navbar + .container,
        .admin-navbar + main.container {
            margin-top: 32px;
        }

        .table-responsive {
            border: 1px solid var(--admin-border);
            border-radius: 7px;
        }

        .table {
            margin-bottom: 0;
            --bs-table-striped-bg: #f7fafc;
            --bs-table-hover-bg: #eff6fb;
        }

        .table > :not(caption) > * > * {
            padding: 12px 14px;
            border-color: var(--admin-border);
        }

        .table thead.table-dark th {
            color: #fff;
            background: var(--admin-blue-dark);
            border-color: rgba(255, 255, 255, .14);
            font-size: .88rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .table tbody td {
            white-space: nowrap;
            vertical-align: middle;
        }

        .btn {
            --bs-btn-border-radius: 5px;
            font-weight: 550;
        }

        .btn-primary {
            --bs-btn-bg: var(--admin-blue-dark);
            --bs-btn-border-color: var(--admin-blue-dark);
            --bs-btn-hover-bg: #245a80;
            --bs-btn-hover-border-color: #245a80;
            --bs-btn-active-bg: #245a80;
            --bs-btn-active-border-color: #245a80;
            --bs-btn-focus-shadow-rgb: 74, 144, 194;
        }

        .btn-success {
            --bs-btn-bg: var(--admin-blue-dark);
            --bs-btn-border-color: var(--admin-blue-dark);
            --bs-btn-hover-bg: #245a80;
            --bs-btn-hover-border-color: #245a80;
            --bs-btn-focus-shadow-rgb: 74, 144, 194;
        }

        .btn-outline-primary {
            --bs-btn-color: var(--admin-blue-dark);
            --bs-btn-border-color: var(--admin-blue);
            --bs-btn-hover-bg: var(--admin-blue-dark);
            --bs-btn-hover-border-color: var(--admin-blue-dark);
            --bs-btn-focus-shadow-rgb: 74, 144, 194;
        }

        .btn-warning {
            --bs-btn-bg: #f2c45c;
            --bs-btn-border-color: #f2c45c;
            --bs-btn-hover-bg: #e5b448;
            --bs-btn-hover-border-color: #e5b448;
            color: #34280f;
        }

        .form-label {
            color: #3d4b40;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-color: #d4e0e9;
            border-radius: 5px;
        }

        .form-control:focus,
        .form-select:focus,
        .form-check-input:focus {
            border-color: #86b5d5;
            box-shadow: 0 0 0 .2rem rgba(74, 144, 194, .16);
        }

        .form-check-input:checked {
            background-color: var(--admin-blue-dark);
            border-color: var(--admin-blue-dark);
        }

        .card {
            border-color: var(--admin-border);
            border-radius: 7px;
            box-shadow: 0 2px 9px rgba(27, 55, 32, .04);
        }

        .card-header {
            color: var(--admin-ink);
            background: #f7fafc;
            border-bottom-color: var(--admin-border);
            font-weight: 600;
        }

        .alert {
            border-radius: 6px;
        }

        .badge.bg-success {
            background-color: var(--admin-blue-dark) !important;
        }

        .admin-footer {
            margin-top: auto;
            padding: 18px 16px;
            color: var(--admin-muted);
            background: #fff;
            border-top: 1px solid var(--admin-border);
            text-align: center;
            font-size: .9rem;
        }

        @media (max-width: 767.98px) {
            .admin-navbar .navbar-collapse {
                padding: 0 0 10px;
            }

            .admin-navbar .nav-link {
                padding: 10px 12px;
                border-bottom: 0;
                border-left: 2px solid transparent;
            }

            .admin-navbar .nav-link:hover,
            .admin-navbar .nav-link.active {
                border-left-color: var(--admin-blue);
            }
        }

        @media (max-width: 575.98px) {
            body > .container,
            body > main.container {
                width: min(100% - 20px, 1320px);
                margin-top: 18px !important;
                padding: 16px;
            }

            .admin-heading {
                padding: 22px 0 18px;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="admin-heading">
        <div class="container">
            <h1><span class="admin-brand-mark" aria-hidden="true">C</span>CellPT</h1>
            <p>Khu vực quản trị và vận hành cửa hàng</p>
        </div>
    </div>