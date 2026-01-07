<?php

require_once "../db.php";
// ===== Config from page =====
// ตั้งค่าจากหน้าเรียกใช้ได้ เช่น:
// $pageTitle = "แก้ไขเอกสาร";
// $activeMenu = "documents"; // documents|customers|items
// $breadcrumbs = [
//   ["label" => "เอกสาร", "href" => "/work/doc-system/documents/index.php"],
//   ["label" => "แก้ไข", "href" => ""],
// ];
// $showBack = true;
// $backUrl = ""; // ถ้าไม่กำหนด จะใช้ HTTP_REFERER อัตโนมัติ
// $username = "Admin"; // หรือดึงจาก session
$pageTitle   = $pageTitle   ?? "Doc System";
$activeMenu  = $activeMenu  ?? "";          // documents/customers/items
$breadcrumbs = $breadcrumbs ?? [];          // array of ["label"=>..., "href"=>...]
$showBack    = $showBack    ?? false;
$backUrl     = $backUrl     ?? "";
$username    = $username    ?? ($_SESSION['username'] ?? "Guest");

// base path ของโปรเจกต์ (แก้ตรงนี้ถ้า path เปลี่ยน)
$BASE = "/work/doc-system";

// Back URL อัตโนมัติ
$autoBack = $_SERVER['HTTP_REFERER'] ?? "";
$finalBackUrl = $backUrl !== "" ? $backUrl : $autoBack;

// helper
function isActive($key, $activeMenu) {
  return $key === $activeMenu ? "active" : "";
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700;800&display=swap" rel="stylesheet">

<style>
html, body {
  font-family: 'Sarabun', sans-serif !important;
  font-size: 14pt;
}
</style>


 
 <style>
    .nav-btn.active{
      border-color: #0d6efd !important;
      background: rgba(13,110,253,.08);
      color: #0d6efd !important;
      font-weight: 600;
    }
  </style>
</head>

<body>
<nav class="navbar navbar-expand-lg bg-light border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $BASE ?>/documents/index.php">📄 Doc System</a>

    <div class="d-flex align-items-center gap-2 flex-wrap">
      <a class="btn btn-sm btn-outline-secondary nav-btn <?= isActive('customers',$activeMenu) ?>"
         href="<?= $BASE ?>/customers/index.php">ลูกค้า</a>

      <a class="btn btn-sm btn-outline-secondary nav-btn <?= isActive('documents',$activeMenu) ?>"
         href="<?= $BASE ?>/documents/index.php">เอกสาร</a>

      <a class="btn btn-sm btn-outline-secondary nav-btn <?= isActive('items',$activeMenu) ?>"
         href="<?= $BASE ?>/bill/index.php">ใบวางบิล</a>

      <a class="btn btn-sm btn-primary"
         href="<?= $BASE ?>/documents/create.php">+ ออกเอกสาร</a>

      <span class="ms-2 text-muted small">
        👤 <?= htmlspecialchars($username) ?>
      </span>
    </div>
  </div>
</nav>

<main class="container py-3">

  <?php if ($showBack && $finalBackUrl): ?>
    <div class="mb-2">
      <a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars($finalBackUrl) ?>">← ย้อนกลับ</a>
    </div>
  <?php endif; ?>

  <?php if (!empty($breadcrumbs)): ?>
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb mb-0">
        <?php foreach ($breadcrumbs as $i => $bc): ?>
          <?php
            $label = $bc['label'] ?? '';
            $href  = $bc['href'] ?? '';
            $isLast = ($i === count($breadcrumbs)-1);
          ?>
          <?php if ($isLast || $href === ''): ?>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($label) ?></li>
          <?php else: ?>
            <li class="breadcrumb-item">
              <a class="text-decoration-none" href="<?= htmlspecialchars($href) ?>"><?= htmlspecialchars($label) ?></a>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ol>
    </nav>
  <?php endif; ?>
