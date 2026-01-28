<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

if (!is_post()) {
    redirect('index.php');
}

$phone = trim((string)($_POST['phone'] ?? ''));
$location = trim((string)($_POST['location'] ?? ''));

if ($phone === '' || $location === '') {
    redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
}

$_SESSION['delivery_phone'] = $phone;
$_SESSION['delivery_location'] = $location;

$pdo = db();
$customer_id = !empty($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;


$stmt = $pdo->prepare('INSERT INTO deliveries (customer_id, phone, location) VALUES (?, ?, ?)');
$stmt->execute([$customer_id, $phone, $location]);

$_SESSION['delivery_id'] = (int)$pdo->lastInsertId();

redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
