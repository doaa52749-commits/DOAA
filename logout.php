<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

unset($_SESSION['customer_id'], $_SESSION['customer_mode']);

redirect('login.php');
