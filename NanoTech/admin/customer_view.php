<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('customers.php');
}

$stmt = $pdo->prepare('SELECT * FROM customers WHERE customer_id = ?');
$stmt->execute([$id]);
$customer = $stmt->fetch();

if (!$customer) {
    redirect('customers.php');
}

$orders = $pdo->prepare('SELECT order_id, total_price, order_date, status FROM orders WHERE customer_id = ? ORDER BY order_id DESC');
$orders->execute([$id]);
$rows = $orders->fetchAll();

$title = 'سجل الطلبات';
$active = 'customers';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <div class="h5 m-0">سجل طلبات العميل</div>
    <div class="text-muted small"><?= e((string)$customer['national_id']) ?> | <?= e((string)$customer['phone']) ?></div>
  </div>
  <a class="btn btn-outline-secondary btn-sm" href="customers.php">رجوع</a>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover m-0">
      <thead>
        <tr>
          <th>رقم الطلب</th>
          <th>المجموع</th>
          <th>التاريخ</th>
          <th>الحالة</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $o): ?>
          <tr>
            <td><?= e((string)$o['order_id']) ?></td>
            <td><?= e(number_format((float)$o['total_price'], 2)) ?></td>
            <td><?= e((string)$o['order_date']) ?></td>
            <td><?= e((string)$o['status']) ?></td>
            <td class="text-end"><a class="btn btn-sm btn-outline-dark" href="order_view.php?id=<?= e((string)$o['order_id']) ?>">تفاصيل</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
