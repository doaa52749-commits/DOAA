<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_customer();
require_once __DIR__ . '/includes/cart.php';

$pdo = db();
cart_init();

$cart = $_SESSION['cart'];

function fetch_by_ids(PDO $pdo, string $table, string $idCol, array $ids): array
{
    if (!$ids) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE {$idCol} IN ({$placeholders})");
    $stmt->execute(array_values($ids));

    $rows = [];
    foreach ($stmt->fetchAll() as $r) {
        $rows[(int)$r[$idCol]] = $r;
    }
    return $rows;
}

$washerIds = array_map('intval', array_keys($cart['washers'] ?? []));
$partIds = array_map('intval', array_keys($cart['parts'] ?? []));
$addIds = array_map('intval', array_keys($cart['additions'] ?? []));

$washers = fetch_by_ids($pdo, 'washers', 'washer_id', $washerIds);
$parts = fetch_by_ids($pdo, 'parts', 'part_id', $partIds);
$adds = fetch_by_ids($pdo, 'additions', 'add_id', $addIds);

$items = [];
$total = 0.0;

foreach (($cart['washers'] ?? []) as $id => $qty) {
    $id = (int)$id;
    $qty = (int)$qty;
    if (!isset($washers[$id])) {
        continue;
    }
    $price = (float)$washers[$id]['price'];
    $line = $price * $qty;
    $items[] = ['type' => 'washer', 'label' => $washers[$id]['type'] . ' - ' . $washers[$id]['version'], 'qty' => $qty, 'price' => $price, 'line' => $line, 'ref_id' => $id];
    $total += $line;
}

foreach (($cart['parts'] ?? []) as $id => $qty) {
    $id = (int)$id;
    $qty = (int)$qty;
    if (!isset($parts[$id])) {
        continue;
    }
    $price = (float)$parts[$id]['price'];
    $line = $price * $qty;
    $items[] = ['type' => 'part', 'label' => $parts[$id]['type'] . ' - ' . $parts[$id]['model'], 'qty' => $qty, 'price' => $price, 'line' => $line, 'ref_id' => $id];
    $total += $line;
}

foreach (($cart['additions'] ?? []) as $id => $qty) {
    $id = (int)$id;
    $qty = (int)$qty;
    if (!isset($adds[$id])) {
        continue;
    }
    $price = (float)$adds[$id]['price'];
    $line = $price * $qty;
    $items[] = ['type' => 'addition', 'label' => $adds[$id]['description'], 'qty' => $qty, 'price' => $price, 'line' => $line, 'ref_id' => $id];
    $total += $line;
}

$created_order_id = null;
$error = '';

if (is_post()) {
    if (!$items) {
        $error = 'السلة فارغة';
    } else {
        $pdo->beginTransaction();
        try {
            $delivery_id = !empty($_SESSION['delivery_id']) ? (int)$_SESSION['delivery_id'] : null;

            $stmt = $pdo->prepare('INSERT INTO orders (customer_id, total_price, delivery_id) VALUES (?, ?, ?)');
            $stmt->execute([(int)$_SESSION['customer_id'], $total, $delivery_id]);
            $order_id = (int)$pdo->lastInsertId();

            $insItem = $pdo->prepare('INSERT INTO order_items (order_id, item_type, ref_id, description, quantity, price) VALUES (?, ?, ?, ?, ?, ?)');

            foreach ($items as $it) {
                $insItem->execute([
                    $order_id,
                    $it['type'],
                    $it['ref_id'],
                    $it['label'],
                    $it['qty'],
                    $it['price'],
                ]);

                if ($it['type'] === 'washer') {
                    $upd = $pdo->prepare('UPDATE washers SET quantity = GREATEST(quantity - ?, 0) WHERE washer_id = ?');
                    $upd->execute([$it['qty'], $it['ref_id']]);
                }
                if ($it['type'] === 'part') {
                    $upd = $pdo->prepare('UPDATE parts SET quantity = GREATEST(quantity - ?, 0) WHERE part_id = ?');
                    $upd->execute([$it['qty'], $it['ref_id']]);
                }
            }

            $pdo->commit();
            cart_clear();
            $created_order_id = $order_id;
        } catch (Throwable $t) {
            $pdo->rollBack();
            $error = 'حدث خطأ أثناء إنشاء الطلب';
        }
    }
}

$title = 'الإجمالي';
$active = 'total';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 m-0">الإجمالي</h1>
  <a class="btn btn-outline-secondary btn-sm" href="index.php">العودة</a>
</div>

<?php if ($created_order_id): ?>
  <div class="alert alert-success">
    تم تأكيد الطلب بنجاح. رقم الطلب: <strong><?= e((string)$created_order_id) ?></strong>
  </div>
<?php endif; ?>

<?php if ($error !== ''): ?>
  <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover m-0">
      <thead>
        <tr>
          <th>الصنف</th>
          <th>الكمية</th>
          <th>السعر</th>
          <th>الإجمالي</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$items): ?>
          <tr><td colspan="4" class="text-center text-muted py-4">لا توجد عناصر</td></tr>
        <?php endif; ?>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?= e($it['label']) ?></td>
            <td><?= e((string)$it['qty']) ?></td>
            <td><?= e(number_format((float)$it['price'], 2)) ?></td>
            <td><?= e(number_format((float)$it['line'], 2)) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="text-end">المجموع</th>
          <th><?= e(number_format((float)$total, 2)) ?> ر.س</th>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="card-body">
    <form method="post">
      <div class="d-grid d-md-flex justify-content-md-end">
        <button class="btn btn-primary" type="submit">تأكيد الطلب</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
