</main>

<?php if (!empty($_SESSION['customer_id'])): ?>
  <div class="app-cta">
    <div class="app-cta-inner">
      <a class="btn btn-primary" href="total.php"><i class="bi bi-receipt"></i> الإجمالي</a>
    </div>
  </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
