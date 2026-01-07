<?php
require_once "../layout/header.php";

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) die("Invalid id");

$name = trim($_POST['name'] ?? '');
$taxId = trim($_POST['tax_id'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');

if ($name === '') die("กรุณากรอกชื่อลูกค้า");

$st = $pdo->prepare("
  UPDATE customers
  SET name=?, tax_id=?, phone=?, email=?, address=?
  WHERE id=?
");
$st->execute([$name, $taxId, $phone, $email, $address, $id]);

header("Location: index.php");
exit;
