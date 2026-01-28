<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

$error = '';
$pdo = db();

$admins_count = (int)$pdo->query('SELECT COUNT(*) AS c FROM admin_users')->fetch()['c'];
$default_hash = hash('sha256', 'admin123');
$stmt = $pdo->prepare('SELECT admin_id FROM admin_users WHERE username = ? AND password_sha256 = ?');
$stmt->execute(['admin', $default_hash]);
$default_admin = $stmt->fetch();
$default_admin_id = $default_admin ? (int)$default_admin['admin_id'] : 0;

$setup_mode = ($admins_count === 0) || ($admins_count === 1 && $default_admin_id > 0);

if (is_post()) {
    $action = (string)($_POST['action'] ?? 'login');

    if ($action === 'create_admin') {
        $new_username = trim((string)($_POST['new_username'] ?? ''));
        $new_password = (string)($_POST['new_password'] ?? '');
        $new_password2 = (string)($_POST['new_password2'] ?? '');

        if (!$setup_mode) {
            $error = 'غير مسموح بإنشاء مشرف من هذه الصفحة الآن';
        } elseif ($new_username === '' || $new_password === '' || $new_password2 === '') {
            $error = 'الرجاء إدخال اسم المستخدم وكلمة المرور';
        } elseif ($new_password !== $new_password2) {
            $error = 'كلمتا المرور غير متطابقتين';
        } elseif (mb_strlen($new_password) < 6) {
            $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
        } else {
            $check = $pdo->prepare('SELECT admin_id FROM admin_users WHERE username = ?');
            $check->execute([$new_username]);
            if ($check->fetch()) {
                $error = 'اسم المستخدم مستخدم بالفعل';
            } else {
                $ins = $pdo->prepare('INSERT INTO admin_users (username, password_sha256) VALUES (?, ?)');
                $ins->execute([$new_username, hash('sha256', $new_password)]);
                $new_admin_id = (int)$pdo->lastInsertId();

                if ($default_admin_id > 0) {
                    $del = $pdo->prepare('DELETE FROM admin_users WHERE admin_id = ?');
                    $del->execute([$default_admin_id]);
                }

                $_SESSION['admin_id'] = $new_admin_id;
                redirect('index.php');
            }
        }
    } else {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'الرجاء إدخال اسم المستخدم وكلمة المرور';
        } else {
            $stmt = $pdo->prepare('SELECT admin_id, password_sha256 FROM admin_users WHERE username = ?');
            $stmt->execute([$username]);
            $row = $stmt->fetch();

            if ($row && hash('sha256', $password) === $row['password_sha256']) {
                $_SESSION['admin_id'] = (int)$row['admin_id'];
                redirect('index.php');
            }

            $error = 'بيانات الدخول غير صحيحة';
        }
    }
}

$title = 'دخول المشرف';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-6 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h4 mb-3">دخول المشرف</h1>

        <?php if ($error !== ''): ?>
          <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($setup_mode): ?>
          <form method="post">
            <input type="hidden" name="action" value="create_admin" />
            <div class="mb-3">
              <label class="form-label">اسم المستخدم</label>
              <input class="form-control" name="new_username" required />
            </div>
            <div class="mb-3">
              <label class="form-label">كلمة المرور</label>
              <input class="form-control" type="password" name="new_password" required />
            </div>
            <div class="mb-3">
              <label class="form-label">تأكيد كلمة المرور</label>
              <input class="form-control" type="password" name="new_password2" required />
            </div>
            <div class="d-grid">
              <button class="btn btn-primary" type="submit">إنشاء المشرف</button>
            </div>
          </form>
        <?php else: ?>

        <form method="post">
          <input type="hidden" name="action" value="login" />
          <div class="mb-3">
            <label class="form-label">اسم المستخدم</label>
            <input class="form-control" name="username" required />
          </div>
          <div class="mb-3">
            <label class="form-label">كلمة المرور</label>
            <input class="form-control" type="password" name="password" required />
          </div>
          <div class="d-grid">
            <button class="btn btn-primary" type="submit">دخول</button>
          </div>
        </form>

        <?php endif; ?>

        <hr class="my-4" />
        <div class="small text-muted">لوحة التحكم - Nano Tech</div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
