<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();

$order_id = (int)($_GET['id'] ?? 0);
if ($order_id <= 0) {
    redirect('orders.php');
}

if (is_post()) {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'status') {
        $status = (string)($_POST['status'] ?? 'new');
        if (!in_array($status, ['new', 'processing', 'delivered'], true)) {
            $status = 'new';
        }
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE order_id = ?');
        $stmt->execute([$status, $order_id]);
        redirect('order_view.php?id=' . $order_id);
    }
}

$stmt = $pdo->prepare('SELECT o.*, c.national_id, c.phone, c.name, c.address, d.phone AS d_phone, d.location, d.status AS d_status
  FROM orders o
  JOIN customers c ON c.customer_id = o.customer_id
  LEFT JOIN deliveries d ON d.delivery_id = o.delivery_id
  WHERE o.order_id = ?');
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('orders.php');
}

$items = $pdo->prepare('SELECT item_type, ref_id, description, quantity, price FROM order_items WHERE order_id = ? ORDER BY item_id ASC');
$items->execute([$order_id]);
$rows = $items->fetchAll();

$title = 'تفاصيل الطلب #' . $order_id;
$active = 'orders';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
  <div>
    <div class="h5 m-0">تفاصيل الطلب #<?= e((string)$order_id) ?></div>
    <div class="text-muted small"><?= e((string)$order['order_date']) ?> | العميل: <?= e((string)$order['national_id']) ?> | <?= e((string)$order['phone']) ?></div>
  </div>
  <a class="btn btn-outline-secondary btn-sm" href="orders.php">رجوع</a>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="fw-semibold mb-2">الحالة</div>
        <form method="post" class="d-flex gap-2">
          <input type="hidden" name="action" value="status" />
          <select class="form-select" name="status">
            <option value="new" <?= ((string)$order['status']==='new')?'selected':'' ?>>جديد</option>
            <option value="processing" <?= ((string)$order['status']==='processing')?'selected':'' ?>>قيد التنفيذ</option>
            <option value="delivered" <?= ((string)$order['status']==='delivered')?'selected':'' ?>>تم التوصيل</option>
          </select>
          <button class="btn btn-primary" type="submit">تحديث</button>
        </form>

        <hr />
        <div class="d-flex justify-content-between">
          <div class="text-muted">الإجمالي</div>
          <div class="fw-semibold"><?= e(number_format((float)$order['total_price'], 2)) ?> ر.س</div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm mt-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">بيانات التوصيل</div>
        <?php if (empty($order['delivery_id'])): ?>
          <div class="text-muted">لا توجد بيانات توصيل مرتبطة.</div>
        <?php else: ?>
          <div class="mb-1"><span class="text-muted">الجوال:</span> <?= e((string)$order['d_phone']) ?></div>
          <div class="mb-1"><span class="text-muted">الموقع:</span> <?= e((string)$order['location']) ?></div>
          <div class="mb-0"><span class="text-muted">الحالة:</span> <?= e((string)$order['d_status']) ?></div>
          <div class="mt-2">
            <a class="btn btn-sm btn-outline-dark" href="deliveries.php">إدارة التوصيل</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-8">
    <div class="card shadow-sm">
      <div class="card-header bg-white fw-semibold">عناصر الطلب</div>
      <div class="table-responsive">
        <table class="table table-hover m-0">
          <thead>
            <tr>
              <th>الوصف</th>
              <th>النوع</th>
              <th>الكمية</th>
              <th>السعر</th>
              <th>الإجمالي</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $it): ?>
              <?php $line = (float)$it['price'] * (int)$it['quantity']; ?>
              <tr>
                <td><?= e((string)$it['description']) ?></td>
                <td><?= e((string)$it['item_type']) ?></td>
                <td><?= e((string)$it['quantity']) ?></td>
                <td><?= e(number_format((float)$it['price'], 2)) ?></td>
                <td><?= e(number_format((float)$line, 2)) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card shadow-sm mt-3">
      <div class="card-header bg-white fw-semibold">بيانات العميل</div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-12 col-md-6"><span class="text-muted">الاسم:</span> <?= e((string)($order['name'] ?? '')) ?></div>
          <div class="col-12 col-md-6"><span class="text-muted">رقم الهوية:</span> <?= e((string)$order['national_id']) ?></div>
          <div class="col-12 col-md-6"><span class="text-muted">الجوال:</span> <?= e((string)$order['phone']) ?></div>
          <div class="col-12 col-md-6"><span class="text-muted">العنوان:</span> <?= e((string)($order['address'] ?? '')) ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
