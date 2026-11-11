<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= htmlspecialchars($pageTitle ?? 'Thiết bị Việt') ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        rel="stylesheet"
    >
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --store-blue: #4a90c2;
            --store-blue-dark: #2e6f9b;
            --store-blue-soft: #eaf4fb;
            --store-ink: #243746;
            --store-muted: #687b89;
            --store-border: #e1eaf0;
            --store-surface: #fff;
            --store-background: #f5f9fc;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--store-ink);
            background: var(--store-background);
            font-family: "Segoe UI", Arial, sans-serif;
        }

        main {
            flex: 1 0 auto;
        }

        .store-home {
            padding-bottom: 28px;
        }

        .store-home-hero {
            padding: 28px 0 20px;
            background: #fff;
        }

        .store-home-hero-panel {
            overflow: hidden;
            background:
                linear-gradient(90deg, rgba(234, 244, 251, .96) 0%, rgba(245, 249, 252, .9) 48%, rgba(225, 239, 248, .35) 100%),
                url("https://img.freepik.com/free-vector/cerulean-blue-curve-frame-template_53876-99029.jpg?w=2000") center / cover no-repeat;
            border: 1px solid var(--store-border);
            border-radius: 12px;
        }

        .store-home-hero-copy {
            padding: clamp(26px, 5vw, 58px);
        }

        .store-home-hero-copy h1 {
            margin: 0;
            color: var(--store-ink);
            font-size: clamp(2.15rem, 4vw, 3.35rem);
            font-weight: 750;
            letter-spacing: -.05em;
            line-height: 1.08;
        }

        .store-home-hero-copy > p {
            max-width: 560px;
            margin: 18px 0 22px;
            color: #526d80;
            font-size: 1.02rem;
            line-height: 1.7;
        }

        .store-home-search {
            display: flex;
            max-width: 570px;
            align-items: center;
            gap: 12px;
            padding: 6px;
            background: #fff;
            border: 1px solid #d6e5ef;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(37, 91, 128, .06);
        }

        .store-home-search > i {
            margin-left: 10px;
            color: var(--store-muted);
        }

        .store-home-search input {
            width: 100%;
            min-width: 0;
            padding: 9px 0;
            color: var(--store-ink);
            background: transparent;
            border: 0;
            outline: 0;
        }

        .store-home-search input:focus {
            box-shadow: none;
        }

        .store-home-search button {
            flex: 0 0 auto;
        }

        .store-home-all-link {
            display: inline-block;
            margin-top: 15px;
            color: var(--store-blue-dark);
            font-size: .92rem;
            font-weight: 650;
            text-decoration: none;
        }

        .store-home-all-link:hover,
        .store-home-section-link:hover {
            color: #245a80;
            text-decoration: underline;
        }

        .store-home-hero-visual {
            position: relative;
            display: flex;
            min-height: 350px;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .store-home-hero-visual > img {
            width: min(100%, 440px);
            max-height: 340px;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .store-home-hero-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            color: var(--store-blue-dark);
            font-size: 1.05rem;
            font-weight: 650;
        }

        .store-home-hero-placeholder i {
            font-size: 6rem;
        }

        .store-home-category-nav {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-top: 18px;
            padding: 17px 20px;
            background: #fff;
            border: 1px solid var(--store-border);
            border-radius: 9px;
        }

        .store-home-category-nav-title {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 9px;
            color: var(--store-ink);
            font-weight: 700;
        }

        .store-home-category-nav-title i {
            color: var(--store-blue-dark);
        }

        .store-home-category-links {
            display: flex;
            width: 100%;
            flex-wrap: wrap;
            gap: 9px;
        }

        .store-home-category-links a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 13px;
            color: #435b6c;
            background: #f7fafc;
            border: 1px solid var(--store-border);
            border-radius: 6px;
            font-size: .9rem;
            text-decoration: none;
            transition: color .15s ease, border-color .15s ease, background .15s ease;
        }

        .store-home-category-links a:hover {
            color: var(--store-blue-dark);
            background: var(--store-blue-soft);
            border-color: #b6d2e5;
        }

        .store-home-category-links a > i {
            color: var(--store-blue-dark);
        }

        .store-home-category-links small {
            color: var(--store-muted);
        }

        .store-home-product-section {
            margin-top: 42px;
            scroll-margin-top: 24px;
        }

        .store-home-section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .store-home-section-heading > div {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .store-home-section-heading h2 {
            margin: 3px 0 0;
            font-size: clamp(1.3rem, 2.4vw, 1.75rem);
            font-weight: 700;
            letter-spacing: -.03em;
        }

        .store-home-section-icon {
            display: inline-grid;
            width: 46px;
            height: 46px;
            flex: 0 0 auto;
            place-items: center;
            color: var(--store-blue-dark);
            background: var(--store-blue-soft);
            border-radius: 8px;
            font-size: 1.15rem;
        }

        .store-home-section-link {
            color: var(--store-blue-dark);
            font-size: .9rem;
            font-weight: 650;
            text-decoration: none;
            white-space: nowrap;
        }

        .store-home-empty {
            padding: 24px;
            color: var(--store-muted);
            background: #fff;
            border: 1px dashed #cbdbe6;
            border-radius: 8px;
        }

        .store-home-about {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
            margin-top: 52px;
            padding: 32px clamp(22px, 4vw, 42px);
            background: #eaf4fb;
            border: 1px solid #dceaf3;
            border-radius: 10px;
        }

        .store-home-about h2 {
            margin: 6px 0 8px;
            font-size: clamp(1.35rem, 2.5vw, 1.85rem);
            font-weight: 700;
        }

        .store-home-about p {
            max-width: 680px;
            margin: 0;
            color: var(--store-muted);
            line-height: 1.65;
        }

        body > .container.py-4,
        body > main.container.py-5 {
            width: min(100% - 32px, 1180px);
            max-width: 1180px;
            margin: 32px auto 40px;
            padding: 28px !important;
            background: var(--store-surface);
            border: 1px solid var(--store-border);
            border-radius: 9px;
            box-shadow: 0 4px 16px rgba(27, 55, 32, .045);
        }

        body > main.container.py-5 {
            flex: 1 0 auto;
        }

        body > .container.py-4 > h1,
        body > main.container.py-5 > h1 {
            font-weight: 700;
            letter-spacing: -.035em;
        }

        .store-navbar {
            background: var(--store-surface);
            border-bottom: 1px solid var(--store-border);
        }

        .store-navbar .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--store-ink);
            font-weight: 750;
            letter-spacing: -.03em;
        }

        .store-brand-mark {
            display: inline-grid;
            width: 36px;
            height: 36px;
            place-items: center;
            color: #fff;
            background: var(--store-blue);
            border-radius: 9px;
            font-size: 1.15rem;
            font-weight: 800;
        }

        .store-navbar .nav-link {
            padding: 12px 14px !important;
            color: #435047;
            font-weight: 550;
        }

        .store-navbar .nav-link:hover,
        .store-navbar .nav-link.active {
            color: var(--store-blue-dark);
        }

        .store-navbar .navbar-toggler {
            border-color: var(--store-border);
        }

        .btn-store {
            --bs-btn-color: #fff;
            --bs-btn-bg: var(--store-blue-dark);
            --bs-btn-border-color: var(--store-blue-dark);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #245a80;
            --bs-btn-hover-border-color: #245a80;
            --bs-btn-active-bg: #245a80;
            --bs-btn-active-border-color: #245a80;
            --bs-btn-focus-shadow-rgb: 74, 144, 194;
            border-radius: 6px;
            font-weight: 600;
        }

        .btn-outline-store {
            --bs-btn-color: var(--store-blue-dark);
            --bs-btn-border-color: #a9c8df;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: var(--store-blue-dark);
            --bs-btn-hover-border-color: var(--store-blue-dark);
            --bs-btn-focus-shadow-rgb: 74, 144, 194;
            border-radius: 6px;
            font-weight: 600;
        }

        .btn-primary {
            --bs-btn-bg: var(--store-blue-dark);
            --bs-btn-border-color: var(--store-blue-dark);
            --bs-btn-hover-bg: #245a80;
            --bs-btn-hover-border-color: #245a80;
            --bs-btn-focus-shadow-rgb: 74, 144, 194;
        }

        .btn-success {
            --bs-btn-bg: var(--store-blue-dark);
            --bs-btn-border-color: var(--store-blue-dark);
            --bs-btn-hover-bg: #245a80;
            --bs-btn-hover-border-color: #245a80;
            --bs-btn-focus-shadow-rgb: 74, 144, 194;
        }

        .btn-outline-primary {
            --bs-btn-color: var(--store-blue-dark);
            --bs-btn-border-color: var(--store-blue);
            --bs-btn-hover-bg: var(--store-blue-dark);
            --bs-btn-hover-border-color: var(--store-blue-dark);
            --bs-btn-focus-shadow-rgb: 74, 144, 194;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b5d5;
            box-shadow: 0 0 0 .2rem rgba(74, 144, 194, .16);
        }

        .store-hero {
            overflow: hidden;
            position: relative;
            color: var(--store-ink);
            background: linear-gradient(115deg, #e6f2fa 0%, #f2f8fc 58%, #deedf7 100%);
        }

        .store-hero::after {
            position: absolute;
            right: -100px;
            bottom: -240px;
            width: 520px;
            height: 520px;
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 50%;
            box-shadow: 0 0 0 48px rgba(255, 255, 255, .035), 0 0 0 96px rgba(255, 255, 255, .025);
            content: "";
            pointer-events: none;
        }

        .store-hero-content {
            position: relative;
            z-index: 1;
        }

        .store-hero-visual {
            display: flex;
            min-height: 320px;
            align-items: center;
            justify-content: center;
        }

        .store-hero-visual img {
            width: min(100%, 410px);
            max-height: 340px;
            object-fit: contain;
            filter: drop-shadow(0 22px 26px rgba(0, 0, 0, .2));
        }

        .store-eyebrow {
            color: var(--store-blue-dark);
            font-size: .78rem;
            font-weight: 750;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .store-hero .store-eyebrow {
            color: var(--store-blue-dark);
        }

        .store-hero h1 {
            max-width: 660px;
            font-size: clamp(2.2rem, 5vw, 3.8rem);
            font-weight: 750;
            letter-spacing: -.045em;
            line-height: 1.08;
        }

        .store-hero p {
            max-width: 570px;
            color: #526d80;
            font-size: 1.08rem;
            line-height: 1.7;
        }

        .store-section {
            padding-top: 68px;
            padding-bottom: 68px;
        }

        .store-section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 26px;
        }

        .store-section-heading h2 {
            margin: 0;
            font-size: clamp(1.55rem, 3vw, 2rem);
            font-weight: 700;
            letter-spacing: -.035em;
        }

        .store-section-heading p {
            margin: 7px 0 0;
            color: var(--store-muted);
        }

        .store-benefit {
            height: 100%;
            padding: 22px;
            background: #fff;
            border: 1px solid var(--store-border);
            border-radius: 8px;
        }

        .store-benefit-mark {
            display: inline-grid;
            width: 42px;
            height: 42px;
            margin-bottom: 15px;
            place-items: center;
            color: var(--store-blue-dark);
            background: var(--store-blue-soft);
            border-radius: 8px;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .store-benefit h3 {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .store-benefit p {
            margin: 0;
            color: var(--store-muted);
            font-size: .94rem;
            line-height: 1.6;
        }

        .store-category-card {
            display: block;
            height: 100%;
            padding: 22px;
            color: var(--store-ink);
            background: #fff;
            border: 1px solid var(--store-border);
            border-radius: 8px;
            text-decoration: none;
            transition: border-color .16s ease, transform .16s ease, box-shadow .16s ease;
        }

        .store-category-card:hover {
            color: var(--store-blue-dark);
            border-color: #abcce1;
            box-shadow: 0 8px 22px rgba(37, 91, 128, .08);
            transform: translateY(-2px);
        }

        .store-category-card span {
            display: block;
            margin-bottom: 6px;
            color: var(--store-blue-dark);
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .store-category-card strong {
            font-size: 1.08rem;
        }

        .store-product-card {
            overflow: hidden;
            height: 100%;
            background: #fff;
            border: 1px solid var(--store-border);
            border-radius: 8px;
            transition: box-shadow .16s ease, transform .16s ease;
        }

        .store-product-card:hover {
            box-shadow: 0 10px 24px rgba(37, 91, 128, .09);
            transform: translateY(-2px);
        }

        .store-product-image {
            display: flex;
            height: 230px;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: #f7fafc;
        }

        .store-product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .store-product-body {
            display: flex;
            min-height: 190px;
            flex-direction: column;
            padding: 18px;
        }

        .store-product-code {
            margin-bottom: 7px;
            color: var(--store-muted);
            font-size: .76rem;
            letter-spacing: .03em;
        }

        .store-product-category {
            margin-bottom: 7px;
            color: var(--store-muted);
            font-size: .82rem;
        }

        .store-product-body h3 {
            font-size: 1.05rem;
            font-weight: 650;
            line-height: 1.4;
        }

        .store-product-body h3 a {
            color: var(--store-ink);
            text-decoration: none;
        }

        .store-product-body h3 a:hover {
            color: var(--store-blue-dark);
        }

        .store-product-meta {
            margin-bottom: 12px;
            font-size: .78rem;
        }

        .store-stock-in {
            color: #31805a;
        }

        .store-stock-out {
            color: #9a6b22;
        }

        .store-product-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--store-muted);
            font-size: .85rem;
        }

        .store-product-placeholder i {
            font-size: 2rem;
        }

        .store-product-price {
            margin: auto 0 14px;
            color: var(--store-blue-dark);
            font-size: 1.2rem;
            font-weight: 750;
        }

        .store-about {
            color: var(--store-ink);
            background: #eaf3f9;
        }

        .store-about-panel {
            padding: clamp(24px, 5vw, 48px);
            background: #fff;
            border: 1px solid var(--store-border);
            border-radius: 9px;
        }

        .store-about-panel p {
            color: var(--store-muted);
            font-size: 1rem;
            line-height: 1.8;
        }

        .store-footer {
            margin-top: auto;
            color: #dce8df;
            background: #17231a;
        }

        .store-footer a {
            color: #dce8df;
            text-decoration: none;
        }

        .store-footer a:hover {
            color: #fff;
            text-decoration: underline;
        }

        .store-footer-bottom {
            padding: 16px 0;
            color: #aebbb1;
            border-top: 1px solid rgba(255, 255, 255, .12);
            font-size: .88rem;
        }

        .card {
            border-color: var(--store-border);
            border-radius: 8px;
            box-shadow: 0 3px 12px rgba(27, 55, 32, .045);
        }

        .card .card-title {
            font-weight: 650;
        }

        .table {
            --bs-table-striped-bg: #f7fafc;
            --bs-table-hover-bg: #eff6fb;
        }

        .table > :not(caption) > * > * {
            padding: 12px;
            border-color: var(--store-border);
        }

        .badge.bg-success {
            background-color: var(--store-blue-dark) !important;
        }

        @media (max-width: 991.98px) {
            .store-navbar .navbar-collapse {
                padding: 8px 0 14px;
            }

            .store-navbar .navbar-nav {
                margin-bottom: 10px;
            }

            .store-hero-visual {
                min-height: 250px;
            }
        }

        @media (max-width: 575.98px) {
            .store-home-hero {
                padding-top: 14px;
            }

            .store-home-hero-copy {
                padding: 25px 20px 12px;
            }

            .store-home-hero-copy h1 {
                font-size: 2.25rem;
            }

            .store-home-search {
                gap: 7px;
            }

            .store-home-search button {
                padding-right: 10px;
                padding-left: 10px;
            }

            .store-home-hero-visual {
                min-height: 260px;
                padding: 18px;
            }

            .store-home-hero-visual > img {
                max-height: 250px;
            }

            .store-home-category-nav {
                align-items: flex-start;
                flex-direction: column;
                gap: 12px;
                padding: 15px;
            }

            .store-home-category-links {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 3px;
            }

            .store-home-category-links a {
                flex: 0 0 auto;
            }

            .store-home-product-section {
                margin-top: 32px;
            }

            .store-home-section-heading {
                align-items: flex-start;
            }

            .store-home-section-icon {
                width: 40px;
                height: 40px;
            }

            .store-home-section-link {
                font-size: .82rem;
            }

            .store-product-image {
                height: 165px;
                padding: 12px;
            }

            .store-product-body {
                min-height: 190px;
                padding: 13px;
            }

            .store-product-body h3 {
                font-size: .94rem;
            }

            .store-product-price {
                font-size: 1.05rem;
            }

            .store-home-about {
                align-items: flex-start;
                flex-direction: column;
                margin-top: 36px;
                padding: 24px 20px;
            }

            body > .container.py-4,
            body > main.container.py-5 {
                width: min(100% - 20px, 1180px);
                margin-top: 20px;
                margin-bottom: 28px;
                padding: 18px !important;
            }

            .store-section {
                padding-top: 48px;
                padding-bottom: 48px;
            }

            .store-section-heading {
                align-items: start;
                flex-direction: column;
            }

            .store-hero-visual {
                min-height: 190px;
            }

            .store-hero-visual img {
                max-height: 210px;
            }
        }
        /* Frontend visual system */
        :root {
            --store-blue: #2563eb;
            --store-blue-dark: #1d4ed8;
            --store-blue-soft: #eff6ff;
            --store-ink: #0f172a;
            --store-muted: #64748b;
            --store-border: #e2e8f0;
            --store-background: #f8fafc;
        }

        html { scroll-behavior: smooth; }
        body {
            color: var(--store-ink);
            background: var(--store-background);
            font-family: "Manrope", "Segoe UI", Arial, sans-serif;
            font-size: .94rem;
        }
        .store-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255,255,255,.94);
            border-bottom: 1px solid var(--store-border);
            box-shadow: 0 1px 12px rgba(15,23,42,.04);
            backdrop-filter: blur(14px);
        }
        .store-navbar .container { min-height: 68px; }
        .store-navbar .navbar-brand { color: var(--store-ink); font-weight: 800; }
        .store-brand-mark {
            width: 34px; height: 34px; border-radius: 10px;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            box-shadow: 0 7px 16px rgba(37,99,235,.22);
        }
        .store-navbar .nav-link { color: #475569; font-size: .86rem; font-weight: 700; }
        .store-navbar .nav-link:hover, .store-navbar .nav-link.active { color: var(--store-blue); }
        .store-navbar .nav-link.active { position: relative; }
        .store-navbar .nav-link.active::after {
            position: absolute; right: 14px; bottom: 3px; left: 14px; height: 2px;
            background: var(--store-blue); border-radius: 99px; content: "";
        }
        .btn, .form-control, .form-select { border-radius: 10px; }
        .btn { font-weight: 700; }
        .btn-store, .btn-primary, .btn-success {
            --bs-btn-bg: var(--store-blue); --bs-btn-border-color: var(--store-blue);
            --bs-btn-hover-bg: var(--store-blue-dark); --bs-btn-hover-border-color: var(--store-blue-dark);
        }
        .btn-outline-store, .btn-outline-primary {
            --bs-btn-color: var(--store-blue); --bs-btn-border-color: #bfdbfe;
            --bs-btn-hover-bg: var(--store-blue); --bs-btn-hover-border-color: var(--store-blue);
        }
        body > .container.py-4, body > main.container.py-5 {
            margin-top: 28px; padding: clamp(20px, 3vw, 36px) !important;
            border: 1px solid var(--store-border); border-radius: 18px;
            box-shadow: 0 12px 35px rgba(15,23,42,.045);
        }
        h1, h2, h3, h4, h5, h6 { letter-spacing: -.035em; }
        .card { border: 1px solid var(--store-border); border-radius: 16px; box-shadow: 0 8px 24px rgba(15,23,42,.045); }
        .card img, .bg-light { border-color: var(--store-border) !important; }
        .form-control, .form-select { border-color: #dbe3ee; padding: .72rem .85rem; }
        .store-footer { color: #94a3b8; background: #0f172a; }
        .store-footer a { color: #cbd5e1; }
        .store-footer a:hover { color: #fff; }
        .store-footer .store-brand-mark { color: #fff; }
        .store-footer-bottom { color: #64748b; border-top-color: rgba(148,163,184,.18); }
        .store-home-hero { padding: 32px 0 24px; background: var(--store-background); }
        .store-home-hero-panel, .store-hero { border: 0; border-radius: 24px; background: linear-gradient(115deg,#eff6ff 0%,#f8fafc 60%,#e0f2fe 100%); box-shadow: 0 18px 45px rgba(37,99,235,.08); }
        .store-home-category-nav, .store-home-about, .store-about-panel { border-radius: 16px; }
        .store-product-card { border-radius: 16px; border-color: var(--store-border); }
        .store-product-image { background: #f8fafc; }
        .store-product-price { color: var(--store-blue); }
        .pagination .page-link { color: var(--store-blue); border-color: var(--store-border); }
        .pagination .active .page-link { background: var(--store-blue); border-color: var(--store-blue); }
        @media (max-width: 991.98px) {
            .store-navbar .nav-link.active::after { display: none; }
            .store-navbar .navbar-collapse { padding: 10px 0 14px; }
        }
    </style>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>
</head>

<body>
