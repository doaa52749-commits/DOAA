<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_customer();
require_once __DIR__ . '/includes/cart.php';

$pdo = db();

if (is_post()) {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'create') {
        $description = trim((string)($_POST['description'] ?? ''));
        $price = (float)($_POST['price'] ?? 0);

        if ($description !== '' && $price >= 0) {
            $stmt = $pdo->prepare('INSERT INTO additions (description, price) VALUES (?, ?)');
            $stmt->execute([$description, $price]);
        }

        redirect('additions.php');
    }

    if ($action === 'add_to_cart') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            cart_add('additions', $id, 1);
        }
        redirect('additions.php');
    }

    if ($action === 'remove_from_cart') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            cart_remove('additions', $id, 1);
        }
        redirect('additions.php');
    }
}

$items = $pdo->query('SELECT add_id, description, price FROM additions ORDER BY add_id DESC')->fetchAll();

$title = 'الإضافات';
$active = 'additions';
require_once __DIR__ . '/includes/layout_header.php';

cart_init();
$cart = $_SESSION['cart']['additions'] ?? [];
?>

<div class="row g-3">
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="h5 mb-3">إضافة طلب خاص</div>
        <form method="post">
          <input type="hidden" name="action" value="create" />
          <div class="mb-3">
            <label class="form-label">وصف الإضافة</label>
            <input class="form-control" name="description" required />
          </div>
          <div class="mb-3">
            <label class="form-label">السعر</label>
            <input class="form-control" type="number" step="0.01" min="0" name="price" value="0" required />
          </div>
          <div class="d-grid">
            <button class="btn btn-primary" type="submit">حفظ</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="h5 m-0">الإضافات المتوفرة</div>
      <a class="btn btn-outline-primary btn-sm" href="total.php">عرض الإجمالي</a>
    </div>

    <div class="card shadow-sm">
      <div class="table-responsive">
        <table class="table table-striped table-hover m-0">
          <thead>
            <tr>
              <th>الوصف</th>
              <th>السعر</th>
              <th>بالسلة</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <?php $id = (int)$it['add_id']; $inCart = (int)($cart[$id] ?? 0); ?>
              <tr>
                <td><?= e($it['description']) ?></td>
                <td><?= e(number_format((float)$it['price'], 2)) ?> ر.س</td>
                <td><?= e((string)$inCart) ?></td>
                <td class="text-end">
                  <form class="d-inline" method="post">
                    <input type="hidden" name="id" value="<?= e((string)$id) ?>" />
                    <button class="btn btn-sm btn-outline-secondary" name="action" value="remove_from_cart" type="submit">-</button>
                    <button class="btn btn-sm btn-primary" name="action" value="add_to_cart" type="submit">+</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
