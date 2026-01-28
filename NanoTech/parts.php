<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_customer();
require_once __DIR__ . '/includes/cart.php';

$pdo = db();

if (is_post()) {
    $id = (int)($_POST['id'] ?? 0);
    $action = (string)($_POST['action'] ?? '');

    if ($id > 0 && in_array($action, ['add', 'remove'], true)) {
        if ($action === 'add') {
            cart_add('parts', $id, 1);
        } else {
            cart_remove('parts', $id, 1);
        }
    }

    redirect('parts.php');
}

$items = $pdo->query('SELECT part_id, type, model, version, price, quantity, image_url FROM parts ORDER BY part_id DESC')->fetchAll();

$title = 'قطع الغيار';
$active = 'parts';
require_once __DIR__ . '/includes/layout_header.php';

cart_init();
$cart = $_SESSION['cart']['parts'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 m-0">قطع الغيار</h1>
  <a class="btn btn-outline-primary btn-sm" href="total.php">عرض الإجمالي</a>
</div>

<div class="row g-3">
  <?php foreach ($items as $it): ?>
    <?php $cid = (int)$it['part_id']; $inCart = (int)($cart[$cid] ?? 0); ?>
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card shadow-sm h-100">
        <?php if (!empty($it['image_url'])): ?>
          <img src="<?= e((string)$it['image_url']) ?>" class="card-img-top" alt="image" style="object-fit:cover;max-height:180px;" />
        <?php endif; ?>
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <div class="fw-semibold"><?= e($it['type']) ?> - <?= e($it['model']) ?></div>
              <div class="text-muted small">الإصدار: <?= e($it['version']) ?></div>
            </div>
            <div class="text-end">
              <div class="fw-semibold"><?= e(number_format((float)$it['price'], 2)) ?> ر.س</div>
              <div class="text-muted small">المتوفر: <?= e((string)$it['quantity']) ?></div>
            </div>
          </div>

          <hr />

          <div class="d-flex justify-content-between align-items-center">
            <div class="small text-muted">بالسلة: <?= e((string)$inCart) ?></div>
            <form class="d-flex gap-2" method="post">
              <input type="hidden" name="id" value="<?= e((string)$cid) ?>" />
              <button class="btn btn-sm btn-outline-secondary" name="action" value="remove" type="submit">-</button>
              <button class="btn btn-sm btn-primary" name="action" value="add" type="submit">+</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
