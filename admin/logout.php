<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

unset($_SESSION['admin_id']);

redirect('login.php');
