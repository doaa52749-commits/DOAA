<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();

$orders = $pdo->query('SELECT o.order_id, o.total_price, o.order_date, o.status, c.national_id, c.phone FROM orders o JOIN customers c ON c.customer_id = o.customer_id ORDER BY o.order_id DESC')->fetchAll();

$title = 'الطلبات';
$active = 'orders';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover m-0">
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
        <?php foreach ($orders as $o): ?>
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

<?php require_once __DIR__ . '/includes/layout_footer.php';
