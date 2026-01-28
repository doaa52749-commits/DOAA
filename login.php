<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

$error = '';

if (is_post()) {
    $national_id = trim((string)($_POST['national_id'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $mode = (string)($_POST['mode'] ?? 'browse');

    if ($national_id === '' || $phone === '' || $password === '') {
        $error = 'الرجاء إدخال رقم الهوية ورقم الجوال وكلمة المرور';
    } else {
        $pdo = db();

        $stmt = $pdo->prepare('SELECT customer_id, phone, password_hash FROM customers WHERE national_id = ?');
        $stmt->execute([$national_id]);
        $row = $stmt->fetch();

        if (!$row) {
            $error = 'لا يوجد حساب بهذا الرقم. الرجاء إنشاء حساب.';
        } else {
            if (((string)$row['phone']) !== $phone) {
                $error = 'رقم الجوال غير صحيح';
            } elseif (empty($row['password_hash'])) {
                $error = 'هذا الحساب يحتاج تفعيل كلمة مرور. الرجاء إنشاء/تفعيل الحساب.';
            } elseif (!password_verify($password, (string)$row['password_hash'])) {
                $error = 'كلمة المرور غير صحيحة';
            } else {
                $_SESSION['customer_id'] = (int)$row['customer_id'];
                $_SESSION['customer_mode'] = in_array($mode, ['browse', 'buy'], true) ? $mode : 'browse';
                redirect('index.php');
            }
        }
    }
}

$title = 'تسجيل الدخول';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-6 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h4 mb-3">تسجيل الدخول</h1>

        <?php if ($error !== ''): ?>
          <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">رقم الهوية</label>
            <input class="form-control" name="national_id" required />
          </div>
          <div class="mb-3">
            <label class="form-label">رقم الجوال</label>
            <input class="form-control" name="phone" required />
          </div>
          <div class="mb-3">
            <label class="form-label">كلمة المرور</label>
            <input class="form-control" type="password" name="password" required />
          </div>
          <div class="mb-3">
            <label class="form-label">الاختيار</label>
            <select class="form-select" name="mode">
              <option value="browse">تصفح</option>
              <option value="buy">شراء</option>
            </select>
          </div>
          <div class="d-grid">
            <button class="btn btn-primary" type="submit">دخول</button>
          </div>
        </form>

        <hr class="my-4" />
        <div class="small text-muted">
          لا تملك حساب؟ <a href="register.php">إنشاء حساب</a>
          <span class="mx-2">|</span>
          دخول الأدمن: <a href="admin/login.php">لوحة التحكم</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
