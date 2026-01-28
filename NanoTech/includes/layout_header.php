<?php
require_once __DIR__ . '/init.php';

$title = $title ?? APP_NAME;
$active = $active ?? '';
$body_class = $body_class ?? '';
$delivery_phone = $_SESSION['delivery_phone'] ?? '';
$delivery_location = $_SESSION['delivery_location'] ?? '';

$body_classes = trim($body_class);
if (!empty($_SESSION['customer_id'])) {
    $body_classes = trim($body_classes . ' has-cta');
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
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="<?= e($body_classes) ?>">
<nav class="navbar navbar-expand-lg navbar-light app-navbar">
  <div class="container">
    <a class="navbar-brand" href="index.php"><?= e(APP_NAME) ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link <?= $active==='home'?'active':'' ?>" href="index.php">الرئيسية</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='washers'?'active':'' ?>" href="washers.php">المغاسل</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='parts'?'active':'' ?>" href="parts.php">قطع الغيار</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='additions'?'active':'' ?>" href="additions.php">الإضافات</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='total'?'active':'' ?>" href="total.php">الإجمالي</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='more'?'active':'' ?>" href="more.php">المزيد</a></li>
      </ul>
      <div class="d-flex gap-2">
        <?php if (!empty($_SESSION['customer_id'])): ?>
          <a class="btn btn-outline-secondary btn-sm" href="logout.php"><i class="bi bi-box-arrow-right"></i> تسجيل الخروج</a>
        <?php else: ?>
          <a class="btn btn-primary btn-sm" href="login.php"><i class="bi bi-person"></i> تسجيل الدخول</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<div class="app-delivery-bar">
  <div class="container py-2">
    <form class="row g-2 align-items-center" method="post" action="save_delivery.php">
      <div class="col-12 col-md-3">
        <input class="form-control form-control-sm" name="phone" placeholder="رقم الجوال للتوصيل" value="<?= e($delivery_phone) ?>" required />
      </div>
      <div class="col-12 col-md-7">
        <input class="form-control form-control-sm" name="location" placeholder="موقع التوصيل" value="<?= e($delivery_location) ?>" required />
      </div>
      <div class="col-12 col-md-2 d-grid">
        <button class="btn btn-sm btn-primary" type="submit">حفظ التوصيل</button>
      </div>
    </form>
  </div>
</div>

<main class="container py-4">
