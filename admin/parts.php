<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

require_once __DIR__ . '/../includes/upload.php';

$pdo = db();

$edit_id = (int)($_GET['id'] ?? 0);
$editing = null;

if ($edit_id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM parts WHERE part_id = ?');
    $stmt->execute([$edit_id]);
    $editing = $stmt->fetch();
}

if (is_post()) {
    $action = (string)($_POST['action'] ?? 'save');

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('DELETE FROM parts WHERE part_id = ?');
            $stmt->execute([$id]);
        }
        redirect('parts.php');
    }

    $id = (int)($_POST['id'] ?? 0);
    $type = trim((string)($_POST['type'] ?? ''));
    $model = trim((string)($_POST['model'] ?? ''));
    $version = trim((string)($_POST['version'] ?? ''));
    $price = (float)($_POST['price'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $image_url = trim((string)($_POST['image_url'] ?? ''));
    $current_image_url = trim((string)($_POST['current_image_url'] ?? ''));

    if ($image_url === '' && $current_image_url !== '') {
        $image_url = $current_image_url;
    }

    try {
        $uploaded = save_uploaded_image('image_file', 'parts');
        if ($uploaded !== null) {
            $image_url = $uploaded;
        }
    } catch (Throwable $t) {
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE parts SET type=?, model=?, version=?, price=?, quantity=?, image_url=? WHERE part_id=?');
        $stmt->execute([$type, $model, $version, $price, $quantity, $image_url, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO parts (type, model, version, price, quantity, image_url) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$type, $model, $version, $price, $quantity, $image_url]);
    }

    redirect('parts.php');
}

$items = $pdo->query('SELECT * FROM parts ORDER BY part_id DESC')->fetchAll();

$title = 'إدارة قطع الغيار';
$active = 'parts';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row g-3">
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="h5 mb-3"><?= $editing ? 'تعديل قطعة' : 'إضافة قطعة' ?></div>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?= e((string)($editing['part_id'] ?? 0)) ?>" />
          <input type="hidden" name="current_image_url" value="<?= e((string)($editing['image_url'] ?? '')) ?>" />
          <div class="mb-2"><label class="form-label">النوع</label><input class="form-control" name="type" value="<?= e((string)($editing['type'] ?? '')) ?>" required /></div>
          <div class="mb-2"><label class="form-label">الموديل</label><input class="form-control" name="model" value="<?= e((string)($editing['model'] ?? '')) ?>" required /></div>
          <div class="mb-2"><label class="form-label">الإصدار</label><input class="form-control" name="version" value="<?= e((string)($editing['version'] ?? '')) ?>" required /></div>
          <div class="mb-2"><label class="form-label">السعر</label><input class="form-control" type="number" step="0.01" min="0" name="price" value="<?= e((string)($editing['price'] ?? '0')) ?>" required /></div>
          <div class="mb-3"><label class="form-label">الكمية</label><input class="form-control" type="number" step="1" min="0" name="quantity" value="<?= e((string)($editing['quantity'] ?? '0')) ?>" required /></div>
          <div class="mb-3"><label class="form-label">رفع صورة</label><input class="form-control" type="file" name="image_file" accept="image/*" /></div>
          <div class="mb-3"><label class="form-label">رابط الصورة (اختياري)</label><input class="form-control" name="image_url" value="<?= e((string)($editing['image_url'] ?? '')) ?>" /></div>

          <?php if (!empty($editing['image_url'])): ?>
            <div class="mb-3">
              <?php $img = (string)$editing['image_url']; $src = str_starts_with($img, 'uploads/') ? ('../' . $img) : $img; ?>
              <img src="<?= e($src) ?>" class="img-fluid rounded" alt="image" />
            </div>
          <?php endif; ?>

          <div class="d-grid"><button class="btn btn-primary" type="submit">حفظ</button></div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="card shadow-sm">
      <div class="table-responsive">
        <table class="table table-striped table-hover m-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>النوع</th>
              <th>الموديل</th>
              <th>السعر</th>
              <th>الكمية</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <tr>
                <td><?= e((string)$it['part_id']) ?></td>
                <td><?= e($it['type']) ?></td>
                <td><?= e($it['model']) ?></td>
                <td><?= e(number_format((float)$it['price'], 2)) ?></td>
                <td><?= e((string)$it['quantity']) ?></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary" href="parts.php?id=<?= e((string)$it['part_id']) ?>">تعديل</a>
                  <form class="d-inline" method="post" onsubmit="return confirm('حذف العنصر؟');">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id" value="<?= e((string)$it['part_id']) ?>" />
                    <button class="btn btn-sm btn-outline-danger" type="submit">حذف</button>
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
