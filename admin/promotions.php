<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

require_once __DIR__ . '/../includes/upload.php';

$pdo = db();

$edit_id = (int)($_GET['id'] ?? 0);
$editing = null;

if ($edit_id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM promotions WHERE promo_id = ?');
    $stmt->execute([$edit_id]);
    $editing = $stmt->fetch();
}

if (is_post()) {
    $action = (string)($_POST['action'] ?? 'save');

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('DELETE FROM promotions WHERE promo_id = ?');
            $stmt->execute([$id]);
        }
        redirect('promotions.php');
    }

    $id = (int)($_POST['id'] ?? 0);
    $title = trim((string)($_POST['title'] ?? ''));
    $content = trim((string)($_POST['content'] ?? ''));
    $image_url = trim((string)($_POST['image_url'] ?? ''));
    $current_image_url = trim((string)($_POST['current_image_url'] ?? ''));
    $is_active = !empty($_POST['is_active']) ? 1 : 0;

    if ($image_url === '' && $current_image_url !== '') {
        $image_url = $current_image_url;
    }

    try {
        $uploaded = save_uploaded_image('image_file', 'promotions');
        if ($uploaded !== null) {
            $image_url = $uploaded;
        }
    } catch (Throwable $t) {
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE promotions SET title=?, content=?, image_url=?, is_active=? WHERE promo_id=?');
        $stmt->execute([$title, $content, $image_url !== '' ? $image_url : null, $is_active, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO promotions (title, content, image_url, is_active) VALUES (?, ?, ?, ?)');
        $stmt->execute([$title, $content, $image_url !== '' ? $image_url : null, $is_active]);
    }

    redirect('promotions.php');
}

$items = $pdo->query('SELECT * FROM promotions ORDER BY promo_id DESC')->fetchAll();

$title = 'العروض والإعلانات';
$active = 'promotions';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row g-3">
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="h5 mb-3"><?= $editing ? 'تعديل' : 'إضافة' ?></div>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?= e((string)($editing['promo_id'] ?? 0)) ?>" />
          <input type="hidden" name="current_image_url" value="<?= e((string)($editing['image_url'] ?? '')) ?>" />
          <div class="mb-2"><label class="form-label">العنوان</label><input class="form-control" name="title" value="<?= e((string)($editing['title'] ?? '')) ?>" required /></div>
          <div class="mb-2"><label class="form-label">المحتوى</label><textarea class="form-control" name="content" rows="4" required><?= e((string)($editing['content'] ?? '')) ?></textarea></div>
          <div class="mb-2"><label class="form-label">رفع صورة</label><input class="form-control" type="file" name="image_file" accept="image/*" /></div>
          <div class="mb-2"><label class="form-label">رابط الصورة (اختياري)</label><input class="form-control" name="image_url" value="<?= e((string)($editing['image_url'] ?? '')) ?>" /></div>

          <?php if (!empty($editing['image_url'])): ?>
            <div class="mb-2">
              <?php $img = (string)$editing['image_url']; $src = str_starts_with($img, 'uploads/') ? ('../' . $img) : $img; ?>
              <img src="<?= e($src) ?>" class="img-fluid rounded" alt="image" />
            </div>
          <?php endif; ?>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="active" <?= (!isset($editing['is_active']) || (int)$editing['is_active']===1) ? 'checked' : '' ?> />
            <label class="form-check-label" for="active">نشط</label>
          </div>
          <div class="d-grid"><button class="btn btn-primary" type="submit">حفظ</button></div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="card shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover m-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>العنوان</th>
              <th>نشط</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <tr>
                <td><?= e((string)$it['promo_id']) ?></td>
                <td><?= e((string)$it['title']) ?></td>
                <td><?= (int)$it['is_active'] === 1 ? '<span class="badge text-bg-success">نعم</span>' : '<span class="badge text-bg-secondary">لا</span>' ?></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary" href="promotions.php?id=<?= e((string)$it['promo_id']) ?>">تعديل</a>
                  <form class="d-inline" method="post" onsubmit="return confirm('حذف؟');">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id" value="<?= e((string)$it['promo_id']) ?>" />
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
