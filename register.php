<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

$error = '';
$success = '';

$prefill_national_id = trim((string)($_GET['national_id'] ?? ''));
$prefill_phone = trim((string)($_GET['phone'] ?? ''));

if (is_post()) {
    $national_id = trim((string)($_POST['national_id'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $name = trim((string)($_POST['name'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $password2 = (string)($_POST['password2'] ?? '');

    if ($national_id === '' || $phone === '' || $password === '' || $password2 === '') {
        $error = 'الرجاء تعبئة جميع الحقول المطلوبة';
    } elseif ($password !== $password2) {
        $error = 'كلمتا المرور غير متطابقتين';
    } elseif (mb_strlen($password) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
    } else {
        $pdo = db();

        $stmt = $pdo->prepare('SELECT customer_id, phone, password_hash FROM customers WHERE national_id = ?');
        $stmt->execute([$national_id]);
        $row = $stmt->fetch();

        $hash = password_hash($password, PASSWORD_DEFAULT);

        if ($row) {
            $customer_id = (int)$row['customer_id'];
            $existing_phone = (string)$row['phone'];
            $existing_hash = $row['password_hash'] ?? null;

            if ($existing_phone !== $phone) {
                $error = 'رقم الجوال لا يطابق الحساب المسجل';
            } elseif (!empty($existing_hash)) {
                $error = 'الحساب موجود بالفعل. الرجاء تسجيل الدخول.';
            } else {
                $upd = $pdo->prepare('UPDATE customers SET name=?, phone=?, address=?, password_hash=? WHERE customer_id=?');
                $upd->execute([
                    $name !== '' ? $name : null,
                    $phone,
                    $address !== '' ? $address : null,
                    $hash,
                    $customer_id,
                ]);

                $success = 'تم تفعيل الحساب بنجاح. يمكنك تسجيل الدخول الآن.';
            }
        } else {
            $ins = $pdo->prepare('INSERT INTO customers (national_id, name, phone, address, password_hash) VALUES (?, ?, ?, ?, ?)');
            $ins->execute([
                $national_id,
                $name !== '' ? $name : null,
                $phone,
                $address !== '' ? $address : null,
                $hash,
            ]);

            $success = 'تم إنشاء الحساب بنجاح. يمكنك تسجيل الدخول الآن.';
        }

        $prefill_national_id = $national_id;
        $prefill_phone = $phone;
    }
}

$title = 'إنشاء حساب';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-7 col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h4 mb-3">إنشاء حساب</h1>

        <?php if ($error !== ''): ?>
          <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($success !== ''): ?>
          <div class="alert alert-success d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div><?= e($success) ?></div>
            <a class="btn btn-sm btn-outline-success" href="login.php">تسجيل الدخول</a>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">رقم الهوية</label>
              <input class="form-control" name="national_id" value="<?= e($prefill_national_id) ?>" required />
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">رقم الجوال</label>
              <input class="form-control" name="phone" value="<?= e($prefill_phone) ?>" required />
            </div>
            <div class="col-12">
              <label class="form-label">الاسم (اختياري)</label>
              <input class="form-control" name="name" />
            </div>
            <div class="col-12">
              <label class="form-label">العنوان (اختياري)</label>
              <input class="form-control" name="address" />
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">كلمة المرور</label>
              <input class="form-control" type="password" name="password" required />
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">تأكيد كلمة المرور</label>
              <input class="form-control" type="password" name="password2" required />
            </div>
          </div>

          <div class="d-grid mt-4">
            <button class="btn btn-primary" type="submit">إنشاء / تفعيل الحساب</button>
          </div>
        </form>

        <hr class="my-4" />
        <div class="small text-muted">
          لديك حساب؟ <a href="login.php">تسجيل الدخول</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
