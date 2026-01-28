<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();

if (is_post()) {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'status') {
        $delivery_id = (int)($_POST['delivery_id'] ?? 0);
        $status = (string)($_POST['status'] ?? 'new');
        if (!in_array($status, ['new', 'processing', 'delivered'], true)) {
            $status = 'new';
        }
        if ($delivery_id > 0) {
            $stmt = $pdo->prepare('INSERT deliveries SET status = ? WHERE delivery_id = ?');
            $stmt->execute([$status, $delivery_id]);
        }
        redirect('deliveries.php');
    }
}

$rows = $pdo->query('SELECT d.delivery_id, d.phone, d.location, d.status, d.created_at, c.national_id, o.order_id
  FROM deliveries d
  LEFT JOIN customers c ON c.customer_id = d.customer_id
  LEFT JOIN orders o ON o.delivery_id = d.delivery_id
  ORDER BY d.delivery_id DESC')->fetchAll();

$title = 'إدارة التوصيل';
$active = 'deliveries';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover m-0">
      <thead>
        <tr>
          <th>ID</th>
          <th>العميل</th>
          <th>رقم الطلب</th>
          <th>الجوال</th>
          <th>الموقع</th>
          <th>الحالة</th>
          <th>تحديث</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $d): ?>
          <tr>
            <td><?= e((string)$d['delivery_id']) ?></td>
            <td><?= e((string)($d['national_id'] ?? '')) ?></td>
            <td>
              <?php if (!empty($d['order_id'])): ?>
                <a href="order_view.php?id=<?= e((string)$d['order_id']) ?>"><?= e((string)$d['order_id']) ?></a>
              <?php endif; ?>
            </td>
            <td><?= e((string)$d['phone']) ?></td>
            <td><?= e((string)$d['location']) ?></td>
            <td><span class="badge text-bg-secondary"><?= e((string)$d['status']) ?></span></td>
            <td>
              <form class="d-flex gap-2" method="post">
                <input type="hidden" name="action" value="status" />
                <input type="hidden" name="delivery_id" value="<?= e((string)$d['delivery_id']) ?>" />
                <select class="form-select form-select-sm" name="status">
                  <option value="new" <?= ((string)$d['status']==='new')?'selected':'' ?>>جديد</option>
                  <option value="processing" <?= ((string)$d['status']==='processing')?'selected':'' ?>>قيد التنفيذ</option>
                  <option value="delivered" <?= ((string)$d['status']==='delivered')?'selected':'' ?>>تم التوصيل</option>
                </select>
                <button class="btn btn-sm btn-primary" type="submit">حفظ</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
