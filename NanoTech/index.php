<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_customer();

$title = 'الرئيسية';
$active = 'home';
$body_class = 'home-3d';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row g-3">
  <div class="col-12">
    <div class="p-4 kpi">
      <div class="d-flex justify-content-between flex-wrap gap-2">
        <div>
          <div class="h4 mb-1">مرحبًا بك في نانو تيك</div>
          <div class="text-muted">اختر القسم الذي تريد تصفحه</div>
        </div>
        <div class="text-muted">وضع الاستخدام: <?= e($_SESSION['customer_mode'] ?? 'browse') ?></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-lg-3">
    <a class="text-decoration-none" href="washers.php">
      <div class="card card-hover shadow-sm">
        <div class="card-body">
          <div class="h5 mb-1">المغاسل</div>
          <div class="text-muted small">عرض جميع المغاسل</div>
        </div>
      </div>
    </a>
  </div>

  <div class="col-12 col-md-6 col-lg-3">
    <a class="text-decoration-none" href="parts.php">
      <div class="card card-hover shadow-sm">
        <div class="card-body">
          <div class="h5 mb-1">قطع الغيار</div>
          <div class="text-muted small">عرض القطع المتوفرة</div>
        </div>
      </div>
    </a>
  </div>

  <div class="col-12 col-md-6 col-lg-3">
    <a class="text-decoration-none" href="additions.php">
      <div class="card card-hover shadow-sm">
        <div class="card-body">
          <div class="h5 mb-1">الإضافات</div>
          <div class="text-muted small">طلبات خاصة وملحقات</div>
        </div>
      </div>
    </a>
  </div>

  <div class="col-12 col-md-6 col-lg-3">
    <a class="text-decoration-none" href="total.php">
      <div class="card card-hover shadow-sm">
        <div class="card-body">
          <div class="h5 mb-1">الإجمالي</div>
          <div class="text-muted small">مراجعة الطلب وتأكيده</div>
        </div>
      </div>
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
