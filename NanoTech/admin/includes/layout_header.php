<?php
require_once __DIR__ . '/../../includes/init.php';

$title = $title ?? (APP_NAME . ' - Admin');
$active = $active ?? '';
$is_admin = !empty($_SESSION['admin_id']);

$current_page = basename((string)($_SERVER['SCRIPT_NAME'] ?? ''));
if (!$is_admin && $current_page !== 'login.php') {
    redirect('login.php');
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= e($title) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/admin.css" />
</head>
<body>

<?php if (!$is_admin): ?>
  <main class="container py-5">
<?php else: ?>
  <div class="admin-app">
    <aside class="admin-sidebar">
      <div class="px-3 py-3 border-bottom border-secondary-subtle">
        <a class="admin-brand-link text-decoration-none d-flex align-items-center gap-2" href="index.php">
          <span class="admin-logo">NT</span>
          <span class="fw-semibold">Nano Tech</span>
        </a>
      </div>

      <div class="p-3">
        <div class="text-uppercase small text-secondary mb-2">الإدارة</div>
        <nav class="nav flex-column gap-1">
          <a class="nav-link admin-nav-link <?= $active==='dashboard'?'active':'' ?>" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
          <a class="nav-link admin-nav-link <?= $active==='customers'?'active':'' ?>" href="customers.php"><i class="bi bi-people"></i> العملاء</a>
          <a class="nav-link admin-nav-link <?= $active==='washers'?'active':'' ?>" href="washers.php"><i class="bi bi-droplet"></i> المغاسل</a>
          <a class="nav-link admin-nav-link <?= $active==='parts'?'active':'' ?>" href="parts.php"><i class="bi bi-gear"></i> قطع الغيار</a>
          <a class="nav-link admin-nav-link <?= $active==='additions'?'active':'' ?>" href="additions.php"><i class="bi bi-plus-circle"></i> الإضافات</a>
          <a class="nav-link admin-nav-link <?= $active==='orders'?'active':'' ?>" href="orders.php"><i class="bi bi-bag-check"></i> الطلبات</a>
          <a class="nav-link admin-nav-link <?= $active==='deliveries'?'active':'' ?>" href="deliveries.php"><i class="bi bi-truck"></i> التوصيل</a>
          <a class="nav-link admin-nav-link <?= $active==='promotions'?'active':'' ?>" href="promotions.php"><i class="bi bi-megaphone"></i> العروض والإعلانات</a>
        </nav>
      </div>

      <div class="mt-auto p-3 border-top border-secondary-subtle">
        <div class="d-grid gap-2">
          <a class="btn btn-sm btn-outline-light" href="../index.php"><i class="bi bi-globe"></i> الموقع</a>
          <a class="btn btn-sm btn-outline-light" href="logout.php"><i class="bi bi-box-arrow-right"></i> خروج</a>
        </div>
      </div>
    </aside>

    <div class="admin-main">
      <nav class="navbar bg-white border-bottom">
        <div class="container-fluid">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminOffcanvas">
              <i class="bi bi-list"></i>
            </button>
            <div class="fw-semibold"><?= e($title) ?></div>
          </div>
        </div>
      </nav>

      <div class="offcanvas offcanvas-start" tabindex="-1" id="adminOffcanvas">
        <div class="offcanvas-header">
          <div class="fw-semibold">لوحة التحكم</div>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
          <div class="p-3">
            <nav class="nav flex-column gap-1">
              <a class="nav-link admin-nav-link <?= $active==='dashboard'?'active':'' ?>" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
              <a class="nav-link admin-nav-link <?= $active==='customers'?'active':'' ?>" href="customers.php"><i class="bi bi-people"></i> العملاء</a>
              <a class="nav-link admin-nav-link <?= $active==='washers'?'active':'' ?>" href="washers.php"><i class="bi bi-droplet"></i> المغاسل</a>
              <a class="nav-link admin-nav-link <?= $active==='parts'?'active':'' ?>" href="parts.php"><i class="bi bi-gear"></i> قطع الغيار</a>
              <a class="nav-link admin-nav-link <?= $active==='additions'?'active':'' ?>" href="additions.php"><i class="bi bi-plus-circle"></i> الإضافات</a>
              <a class="nav-link admin-nav-link <?= $active==='orders'?'active':'' ?>" href="orders.php"><i class="bi bi-bag-check"></i> الطلبات</a>
              <a class="nav-link admin-nav-link <?= $active==='deliveries'?'active':'' ?>" href="deliveries.php"><i class="bi bi-truck"></i> التوصيل</a>
              <a class="nav-link admin-nav-link <?= $active==='promotions'?'active':'' ?>" href="promotions.php"><i class="bi bi-megaphone"></i> العروض والإعلانات</a>
            </nav>
          </div>
        </div>
      </div>

      <main class="container-fluid py-4">
<?php endif; ?>
