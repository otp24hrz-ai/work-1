<?php
require_once "../layout/header.php";

$name = trim($_POST['name'] ?? '');
$tax_id = trim($_POST['tax_id'] ?? '');
$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($name === '') {
  die("กรุณากรอกชื่อลูกค้า");
}

$stmt = $pdo->prepare("INSERT INTO customers(name, tax_id, address, phone, email) VALUES(?,?,?,?,?)");
$stmt->execute([$name, $tax_id, $address, $phone, $email]);

header("Location: index.php");
exit;
