<?php
$pageTitle = 'CellPT | Tổng quan kinh doanh';

require_once '/var/www/src/config/database.php';

$todayResult = $conn->query('SELECT CURRENT_DATE() AS Today');
$todayRow = $todayResult->fetch_assoc();
$todayResult->free();
$currentMonth = new DateTimeImmutable($todayRow['Today']);
$firstMonth = $currentMonth->modify('first day of this month')->modify('-11 months');
$chartStart = $firstMonth->format('Y-m-d');

$summaryResult = $conn->query("
    SELECT
        COUNT(*) AS OrderCount,
        COALESCE(SUM(TotalAmount), 0) AS Revenue,
        COALESCE(AVG(TotalAmount), 0) AS AverageOrder
    FROM orders
    WHERE Status <> 'Cancelled'
");
$summary = $summaryResult->fetch_assoc();
$summaryResult->free();

$processingResult = $conn->query("
    SELECT COUNT(*) AS ProcessingCount
    FROM orders
    WHERE Status NOT IN ('Completed', 'Cancelled')
");
$processingCount = (int) $processingResult->fetch_assoc()['ProcessingCount'];
$processingResult->free();

$statusResult = $conn->query("
    SELECT Status, COUNT(*) AS StatusCount
    FROM orders
    GROUP BY Status
");
$statusCounts = [];
while ($row = $statusResult->fetch_assoc()) {
    $statusCounts[$row['Status']] = (int) $row['StatusCount'];
}
$statusResult->free();

$monthlyResult = $conn->prepare("
    SELECT
        DATE_FORMAT(OrderDate, '%Y-%m') AS MonthKey,
        COALESCE(SUM(TotalAmount), 0) AS Revenue
    FROM orders
    WHERE OrderDate >= ?
      AND OrderDate < DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY)
      AND Status <> 'Cancelled'
    GROUP BY DATE_FORMAT(OrderDate, '%Y-%m')
    ORDER BY MonthKey
");
$monthlyResult->bind_param('s', $chartStart);
$monthlyResult->execute();
$monthlyRows = $monthlyResult->get_result();
$monthlyRevenue = [];
while ($row = $monthlyRows->fetch_assoc()) {
    $monthlyRevenue[$row['MonthKey']] = (float) $row['Revenue'];
}
$monthlyResult->close();

$chartLabels = [];
$chartValues = [];
$month = $firstMonth;
for ($index = 0; $index < 12; $index++) {
    $monthKey = $month->format('Y-m');
    $chartLabels[] = $month->format('m/Y');
    $chartValues[] = $monthlyRevenue[$monthKey] ?? 0;
    $month = $month->modify('+1 month');
}

$recentResult = $conn->query("
    SELECT
        o.OrderID,
        o.OrderDate,
        o.TotalAmount,
        o.Status,
        c.CustomerName
    FROM orders o
    INNER JOIN customers c ON c.CustomerID = o.CustomerID
    ORDER BY o.OrderDate DESC, o.OrderID DESC
    LIMIT 5
");

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<style>
    .dashboard-welcome {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
    }

    .dashboard-welcome h2 {
        margin: 0 0 5px;
    }

    .dashboard-welcome p {
        margin: 0;
        color: var(--admin-muted);
    }

    .dashboard-period {
        color: var(--admin-muted);
        font-size: .88rem;
        white-space: nowrap;
    }

    .dashboard-stat {
        height: 100%;
        padding: 20px;
        background: #fff;
        border: 1px solid var(--admin-border);
        border-radius: 9px;
        box-shadow: 0 3px 12px rgba(27, 55, 75, .04);
    }

    .dashboard-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        color: var(--admin-muted);
        font-size: .9rem;
        font-weight: 550;
    }

    .dashboard-stat-icon {
        display: inline-grid;
        width: 40px;
        height: 40px;
        place-items: center;
        color: var(--admin-blue-dark);
        background: var(--admin-blue-soft);
        border-radius: 9px;
        font-size: 1rem;
    }

    .dashboard-stat-value {
        color: var(--admin-ink);
        font-size: clamp(1.35rem, 2.4vw, 1.8rem);
        font-weight: 750;
        letter-spacing: -.035em;
        line-height: 1.2;
    }

    .dashboard-stat-note {
        margin-top: 6px;
        color: var(--admin-muted);
        font-size: .8rem;
    }

    .dashboard-panel {
        height: 100%;
        padding: 22px;
        background: #fff;
        border: 1px solid var(--admin-border);
        border-radius: 9px;
        box-shadow: 0 3px 12px rgba(27, 55, 75, .04);
    }

    .dashboard-panel-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .dashboard-panel-heading h3 {
        margin: 0;
        color: var(--admin-ink);
        font-size: 1.05rem;
        font-weight: 700;
    }

    .dashboard-panel-heading p {
        margin: 5px 0 0;
        color: var(--admin-muted);
        font-size: .84rem;
    }

    .dashboard-chart-wrap {
        position: relative;
        height: 320px;
    }

    .dashboard-status-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 0;
        color: var(--admin-ink);
        border-bottom: 1px solid var(--admin-border);
        text-decoration: none;
    }

    .dashboard-status-link:last-child {
        border-bottom: 0;
    }

    .dashboard-status-link:hover {
        color: var(--admin-blue-dark);
    }

    .dashboard-status-link span {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .dashboard-status-link strong {
        font-size: 1.1rem;
    }

    .dashboard-status-dot {
        width: 9px;
        height: 9px;
        background: var(--admin-blue);
        border-radius: 50%;
    }

    .dashboard-status-dot.pending {
        background: #e5ae38;
    }

    .dashboard-status-dot.shipping {
        background: #55a7c7;
    }

    .dashboard-status-dot.completed {
        background: #5aa77a;
    }

    .dashboard-status-dot.cancelled {
        background: #a4afb7;
    }

    .dashboard-recent-table {
        min-width: 600px;
    }

    .dashboard-recent-table td,
    .dashboard-recent-table th {
        padding: 12px 10px !important;
    }

    @media (max-width: 767.98px) {
        .dashboard-welcome {
            align-items: flex-start;
            flex-direction: column;
        }

        .dashboard-chart-wrap {
            height: 260px;
        }

        .dashboard-panel {
            padding: 17px;
        }
    }
</style>

<main class="container mt-4">
    <div class="dashboard-welcome">
        <div>
            <h2>Tổng quan kinh doanh</h2>
            <p>Các chỉ số chính giúp bạn nắm nhanh tình hình cửa hàng.</p>
        </div>
        <span class="dashboard-period">
            <i class="fa-regular fa-calendar me-1" aria-hidden="true"></i>
            Cập nhật <?= $currentMonth->format('d/m/Y') ?>
        </span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <section class="dashboard-stat">
                <div class="dashboard-stat-top">
                    <span>Tổng doanh thu</span>
                    <span class="dashboard-stat-icon"><i class="fa-solid fa-sack-dollar" aria-hidden="true"></i></span>
                </div>
                <div class="dashboard-stat-value">
                    <?= number_format((float) $summary['Revenue'], 0, ',', '.') ?> đ
                </div>
                <div class="dashboard-stat-note">Từ đơn hàng chưa bị hủy</div>
            </section>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <section class="dashboard-stat">
                <div class="dashboard-stat-top">
                    <span>Đơn hàng hợp lệ</span>
                    <span class="dashboard-stat-icon"><i class="fa-solid fa-receipt" aria-hidden="true"></i></span>
                </div>
                <div class="dashboard-stat-value"><?= number_format((int) $summary['OrderCount']) ?></div>
                <div class="dashboard-stat-note">Không tính đơn đã hủy</div>
            </section>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <section class="dashboard-stat">
                <div class="dashboard-stat-top">
                    <span>Giá trị đơn trung bình</span>
                    <span class="dashboard-stat-icon"><i class="fa-solid fa-chart-simple" aria-hidden="true"></i></span>
                </div>
                <div class="dashboard-stat-value">
                    <?= number_format((float) $summary['AverageOrder'], 0, ',', '.') ?> đ
                </div>
                <div class="dashboard-stat-note">Trên mỗi đơn hàng hợp lệ</div>
            </section>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <section class="dashboard-stat">
                <div class="dashboard-stat-top">
                    <span>Đơn cần xử lý</span>
                    <span class="dashboard-stat-icon"><i class="fa-solid fa-clock" aria-hidden="true"></i></span>
                </div>
                <div class="dashboard-stat-value"><?= number_format($processingCount) ?></div>
                <div class="dashboard-stat-note">Đang chờ hoặc đang giao</div>
            </section>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-8">
            <section class="dashboard-panel">
                <div class="dashboard-panel-heading">
                    <div>
                        <h3>Doanh thu theo tháng</h3>
                        <p>12 tháng gần nhất · Không tính đơn đã hủy</p>
                    </div>
                    <span class="dashboard-stat-icon"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span>
                </div>
                <div class="dashboard-chart-wrap">
                    <canvas id="revenueChart" role="img" aria-label="Biểu đồ doanh thu theo tháng trong 12 tháng gần nhất"></canvas>
                </div>
                <p class="dashboard-stat-note mt-3 mb-0">
                    Doanh thu được tính từ tổng tiền đã lưu trên các đơn hàng chưa bị hủy.
                </p>
            </section>
        </div>
        <div class="col-12 col-xl-4">
            <section class="dashboard-panel">
                <div class="dashboard-panel-heading">
                    <div>
                        <h3>Tình trạng đơn hàng</h3>
                        <p>Số đơn hiện có theo trạng thái</p>
                    </div>
                    <span class="dashboard-stat-icon"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
                </div>
                <?php
                $statusGroups = [
                    ['Pending', 'Chờ xác nhận', 'pending'],
                    ['Confirmed', 'Đã xác nhận', ''],
                    ['Shipping', 'Đang giao', 'shipping'],
                    ['Completed', 'Hoàn thành', 'completed'],
                    ['Cancelled', 'Đã hủy', 'cancelled']
                ];
                ?>
                <?php foreach ($statusGroups as [$status, $label, $dotClass]): ?>
                    <a class="dashboard-status-link" href="/admin/orders/">
                        <span>
                            <i class="dashboard-status-dot <?= $dotClass ?>" aria-hidden="true"></i>
                            <?= htmlspecialchars($label) ?>
                        </span>
                        <strong><?= number_format($statusCounts[$status] ?? 0) ?></strong>
                    </a>
                <?php endforeach; ?>
            </section>
        </div>
    </div>

    <section class="dashboard-panel">
        <div class="dashboard-panel-heading">
            <div>
                <h3>Đơn hàng gần đây</h3>
                <p>5 đơn mới nhất, giúp bạn theo dõi hoạt động gần đây.</p>
            </div>
            <a href="/admin/orders/" class="btn btn-outline-primary btn-sm">
                Xem tất cả <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle dashboard-recent-table">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Khách hàng</th>
                        <th class="text-end">Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentResult->num_rows === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Chưa có đơn hàng.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($order = $recentResult->fetch_assoc()): ?>
                            <?php
                            $statusInfo = match ($order['Status']) {
                                'Pending' => ['Chờ xác nhận', 'bg-warning text-dark'],
                                'Confirmed' => ['Đã xác nhận', 'bg-primary'],
                                'Shipping' => ['Đang giao', 'bg-info text-dark'],
                                'Completed' => ['Hoàn thành', 'bg-success'],
                                'Cancelled' => ['Đã hủy', 'bg-secondary'],
                                default => [$order['Status'], 'bg-secondary']
                            };
                            ?>
                            <tr>
                                <td class="fw-semibold">#<?= (int) $order['OrderID'] ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($order['OrderDate']))) ?></td>
                                <td><?= htmlspecialchars($order['CustomerName']) ?></td>
                                <td class="text-end"><?= number_format((float) $order['TotalAmount'], 0, ',', '.') ?> đ</td>
                                <td><span class="badge <?= $statusInfo[1] ?>"><?= htmlspecialchars($statusInfo[0]) ?></span></td>
                                <td>
                                    <a
                                        href="/admin/orders/detail.php?id=<?= (int) $order['OrderID'] ?>"
                                        class="btn btn-sm btn-outline-primary"
                                        aria-label="Xem đơn hàng #<?= (int) $order['OrderID'] ?>"
                                    >
                                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <p class="text-muted small mt-3 mb-0">
        Đơn đã hủy không được tính vào doanh thu và giá trị đơn trung bình.
    </p>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script>
    const revenueCanvas = document.getElementById('revenueChart');
    const revenueData = {
        labels: <?= json_encode($chartLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
        values: <?= json_encode($chartValues, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
    };

    if (revenueCanvas && window.Chart) {
        new Chart(revenueCanvas, {
            type: 'bar',
            data: {
                labels: revenueData.labels,
                datasets: [{
                    label: 'Doanh thu',
                    data: revenueData.values,
                    backgroundColor: 'rgba(74, 144, 194, 0.72)',
                    borderColor: '#2e6f9b',
                    borderWidth: 1,
                    borderRadius: 5,
                    maxBarThickness: 34
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: context => ' ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#687b89', maxRotation: 0 }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#edf2f6' },
                        ticks: {
                            color: '#687b89',
                            callback: value => new Intl.NumberFormat('vi-VN', {
                                notation: value >= 1000000 ? 'compact' : 'standard',
                                maximumFractionDigits: 1
                            }).format(value)
                        }
                    }
                }
            }
        });
    } else if (revenueCanvas) {
        revenueCanvas.insertAdjacentHTML(
            'afterend',
            '<p class="text-muted text-center mt-3">Không tải được thư viện biểu đồ. Vui lòng tải lại trang để thử lại.</p>'
        );
    }
</script>

<?php
require_once '/var/www/src/includes/admin/footer.php';
$recentResult->free();
$conn->close();
?>
