<?php

declare(strict_types=1);

require_once __DIR__ . '/init.php';

function require_customer(): void
{
    if (empty($_SESSION['customer_id'])) {
        redirect('login.php');
    }
}

function require_admin(): void
{
    if (empty($_SESSION['admin_id'])) {
        redirect('login.php');
    }
}
