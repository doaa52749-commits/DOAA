<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();

$q = trim((string)($_GET['q'] ?? ''));

if (is_post()) {
    $action = (string)($_POST['action'] ?? 'save');

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('DELETE FROM customers WHERE customer_id = ?');
            $stmt->execute([$id]);
        }
        redirect('customers.php');
    }

    $id = (int)($_POST['id'] ?? 0);
    $name = trim((string)($_POST['name'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE customers SET name=?, phone=?, address=? WHERE customer_id=?');
        $stmt->execute([$name !== '' ? $name : null, $phone, $address !== '' ? $address : null, $id]);
        redirect('customers.php');
    }
}

$edit_id = (int)($_GET['id'] ?? 0);
$editing = null;

if ($edit_id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM customers WHERE customer_id = ?');
    $stmt->execute([$edit_id]);
    $editing = $stmt->fetch();
}

if ($q !== '') {
    $stmt = $pdo->prepare('SELECT * FROM customers WHERE national_id LIKE ? OR phone LIKE ? ORDER BY customer_id DESC');
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like]);
    $customers = $stmt->fetchAll();
} else {
    $customers = $pdo->query('SELECT * FROM customers ORDER BY customer_id DESC')->fetchAll();
}

$title = 'إدارة العملاء';
$active = 'customers';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row g-3">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">
        <form class="row g-2 align-items-center" method="get">
          <div class="col-12 col-md-6">
            <input class="form-control" name="q" placeholder="بحث برقم الهوية أو رقم الجوال" value="<?= e($q) ?>" />
          </div>
          <div class="col-12 col-md-3 d-grid">
            <button class="btn btn-primary" type="submit">بحث</button>
          </div>
          <div class="col-12 col-md-3 d-grid">
            <a class="btn btn-outline-secondary" href="customers.php">إلغاء</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="h5 mb-3"><?= $editing ? 'تعديل بيانات العميل' : 'اختر عميل للتعديل' ?></div>

        <?php if (!$editing): ?>
          <div class="text-muted">من الجدول، اضغط "تعديل" لعرض نموذج تعديل العميل.</div>
        <?php else: ?>
          <form method="post">
            <input type="hidden" name="id" value="<?= e((string)$editing['customer_id']) ?>" />
            <div class="mb-2">
              <label class="form-label">رقم الهوية</label>
              <input class="form-control" value="<?= e((string)$editing['national_id']) ?>" disabled />
            </div>
            <div class="mb-2">
              <label class="form-label">الاسم</label>
              <input class="form-control" name="name" value="<?= e((string)($editing['name'] ?? '')) ?>" />
            </div>
            <div class="mb-2">
              <label class="form-label">رقم الجوال</label>
              <input class="form-control" name="phone" value="<?= e((string)$editing['phone']) ?>" required />
            </div>
            <div class="mb-3">
              <label class="form-label">العنوان</label>
              <input class="form-control" name="address" value="<?= e((string)($editing['address'] ?? '')) ?>" />
            </div>
            <div class="d-grid">
              <button class="btn btn-primary" type="submit">حفظ</button>
            </div>
          </form>
        <?php endif; ?>
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
              <th>رقم الهوية</th>
              <th>الجوال</th>
              <th>الاسم</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($customers as $c): ?>
              <tr>
                <td><?= e((string)$c['customer_id']) ?></td>
                <td><?= e((string)$c['national_id']) ?></td>
                <td><?= e((string)$c['phone']) ?></td>
                <td><?= e((string)($c['name'] ?? '')) ?></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary" href="customers.php?id=<?= e((string)$c['customer_id']) ?>">تعديل</a>
                  <a class="btn btn-sm btn-outline-dark" href="customer_view.php?id=<?= e((string)$c['customer_id']) ?>">سجل الطلبات</a>
                  <form class="d-inline" method="post" onsubmit="return confirm('حذف العميل؟ سيتم حذف بياناته فقط إذا لم يكن مرتبطًا بطلبات.');">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id" value="<?= e((string)$c['customer_id']) ?>" />
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
