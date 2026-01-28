<?php $is_admin = !empty($_SESSION['admin_id']); ?>

<?php if (!$is_admin): ?>
  </main>
<?php else: ?>
      </main>
    </div>
  </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
