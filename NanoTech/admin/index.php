<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();

$customers = (int)$pdo->query('SELECT COUNT(*) AS c FROM customers')->fetch()['c'];
$washers = (int)$pdo->query('SELECT COUNT(*) AS c FROM washers')->fetch()['c'];
$parts = (int)$pdo->query('SELECT COUNT(*) AS c FROM parts')->fetch()['c'];
$orders = (int)$pdo->query('SELECT COUNT(*) AS c FROM orders')->fetch()['c'];
$sales = (float)$pdo->query('SELECT COALESCE(SUM(total_price), 0) AS s FROM orders')->fetch()['s'];
$new_orders = (int)$pdo->query("SELECT COUNT(*) AS c FROM orders WHERE status='new'")->fetch()['c'];

$latest_orders = $pdo->query('SELECT o.order_id, o.total_price, o.order_date, o.status, c.national_id, c.phone FROM orders o JOIN customers c ON c.customer_id = o.customer_id ORDER BY o.order_id DESC LIMIT 8')->fetchAll();

$title = 'Dashboard';
$active = 'dashboard';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row g-3">
  <div class="col-12">
    <?php if ($new_orders > 0): ?>
      <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div><i class="bi bi-bell"></i> لديك <strong><?= e((string)$new_orders) ?></strong> طلب/طلبات جديدة</div>
        <a class="btn btn-sm btn-dark" href="orders.php">عرض الطلبات</a>
      </div>
    <?php endif; ?>
  </div>

  <div class="col-12 col-xl-3">
    <div class="p-3 kpi-card">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="text-muted">العملاء</div>
          <div class="h3 m-0"><?= e((string)$customers) ?></div>
        </div>
        <div class="kpi-icon"><i class="bi bi-people"></i></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-3">
    <div class="p-3 kpi-card">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="text-muted">الطلبات</div>
          <div class="h3 m-0"><?= e((string)$orders) ?></div>
        </div>
        <div class="kpi-icon"><i class="bi bi-bag-check"></i></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-3">
    <div class="p-3 kpi-card">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="text-muted">إجمالي المبيعات</div>
          <div class="h3 m-0"><?= e(number_format($sales, 2)) ?></div>
        </div>
        <div class="kpi-icon"><i class="bi bi-cash-coin"></i></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-3">
    <div class="p-3 kpi-card">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="text-muted">المنتجات</div>
          <div class="h3 m-0"><?= e((string)($washers + $parts)) ?></div>
        </div>
        <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
      </div>
      <div class="small text-muted mt-2">مغاسل: <?= e((string)$washers) ?> | قطع: <?= e((string)$parts) ?></div>
    </div>
  </div>

  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div class="fw-semibold">أحدث الطلبات</div>
        <a class="btn btn-sm btn-outline-primary" href="orders.php">عرض الكل</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover m-0">
          <thead>
            <tr>
              <th>رقم الطلب</th>
              <th>العميل</th>
              <th>الجوال</th>
              <th>المجموع</th>
              <th>التاريخ</th>
              <th>الحالة</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($latest_orders as $o): ?>
              <tr>
                <td><?= e((string)$o['order_id']) ?></td>
                <td><?= e((string)$o['national_id']) ?></td>
                <td><?= e((string)$o['phone']) ?></td>
                <td><?= e(number_format((float)$o['total_price'], 2)) ?></td>
                <td><?= e((string)$o['order_date']) ?></td>
                <td>
                  <?php
                    $st = (string)$o['status'];
                    $badge = 'secondary';
                    if ($st === 'new') $badge = 'warning';
                    if ($st === 'processing') $badge = 'info';
                    if ($st === 'delivered') $badge = 'success';
                  ?>
                  <span class="badge text-bg-<?= e($badge) ?>"><?= e($st) ?></span>
                </td>
                <td class="text-end"><a class="btn btn-sm btn-outline-dark" href="order_view.php?id=<?= e((string)$o['order_id']) ?>">تفاصيل</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
