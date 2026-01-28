<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();

$edit_id = (int)($_GET['id'] ?? 0);
$editing = null;

if ($edit_id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM additions WHERE add_id = ?');
    $stmt->execute([$edit_id]);
    $editing = $stmt->fetch();
}

if (is_post()) {
    $action = (string)($_POST['action'] ?? 'save');

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('DELETE FROM additions WHERE add_id = ?');
            $stmt->execute([$id]);
        }
        redirect('additions.php');
    }

    $id = (int)($_POST['id'] ?? 0);
    $description = trim((string)($_POST['description'] ?? ''));
    $price = (float)($_POST['price'] ?? 0);

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE additions SET description=?, price=? WHERE add_id=?');
        $stmt->execute([$description, $price, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO additions (description, price) VALUES (?, ?)');
        $stmt->execute([$description, $price]);
    }

    redirect('additions.php');
}

$items = $pdo->query('SELECT * FROM additions ORDER BY add_id DESC')->fetchAll();

$title = 'إدارة الإضافات';
$active = 'additions';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row g-3">
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="h5 mb-3"><?= $editing ? 'تعديل إضافة' : 'إضافة جديدة' ?></div>
        <form method="post">
          <input type="hidden" name="id" value="<?= e((string)($editing['add_id'] ?? 0)) ?>" />
          <div class="mb-2">
            <label class="form-label">الوصف</label>
            <input class="form-control" name="description" value="<?= e((string)($editing['description'] ?? '')) ?>" required />
          </div>
          <div class="mb-3">
            <label class="form-label">السعر</label>
            <input class="form-control" type="number" step="0.01" min="0" name="price" value="<?= e((string)($editing['price'] ?? '0')) ?>" required />
          </div>
          <div class="d-grid">
            <button class="btn btn-primary" type="submit">حفظ</button>
          </div>
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
              <th>الوصف</th>
              <th>السعر</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <tr>
                <td><?= e((string)$it['add_id']) ?></td>
                <td><?= e((string)$it['description']) ?></td>
                <td><?= e(number_format((float)$it['price'], 2)) ?></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary" href="additions.php?id=<?= e((string)$it['add_id']) ?>">تعديل</a>
                  <form class="d-inline" method="post" onsubmit="return confirm('حذف العنصر؟');">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id" value="<?= e((string)$it['add_id']) ?>" />
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
