<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_customer();

$pdo = db();
$promos = $pdo->query('SELECT promo_id, title, content, image_url FROM promotions WHERE is_active = 1 ORDER BY promo_id DESC')->fetchAll();

$title = 'المزيد';
$active = 'more';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="row g-3">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">
        <h1 class="h4">المزيد</h1>
        <div class="text-muted">أحدث العروض والخدمات والإعلانات</div>
      </div>
    </div>
  </div>

  <?php foreach ($promos as $p): ?>
    <div class="col-12 col-lg-6">
      <div class="card shadow-sm h-100">
        <?php if (!empty($p['image_url'])): ?>
          <img src="<?= e((string)$p['image_url']) ?>" class="card-img-top" alt="image" style="object-fit:cover;max-height:220px;" />
        <?php endif; ?>
        <div class="card-body">
          <div class="fw-semibold mb-1"><?= e((string)$p['title']) ?></div>
          <div class="text-muted"><?= e((string)$p['content']) ?></div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php';
