<?php
require_once "../layout/header.php";

$groupNo = $_GET['group_no'] ?? '';
if ($groupNo === '') die("Invalid group");

$st = $pdo->prepare("
  SELECT id, doc_type
  FROM documents
  WHERE group_no=?
");
$st->execute([$groupNo]);
$docs = $st->fetchAll(PDO::FETCH_KEY_PAIR); // doc_type => id
?>

<h4>แก้ไขเอกสารชุด <?= htmlspecialchars($groupNo) ?></h4>

<div class="d-flex flex-column gap-2 mt-3">
  <?php if (!empty($docs['QUO'])): ?>
    <a class="btn btn-outline-primary"
       href="create.php?id=<?= (int)$docs['QUO'] ?>">
      ✏️ แก้ไขใบเสนอราคา (QUO)
    </a>
  <?php endif; ?>

  <?php if (!empty($docs['TAX'])): ?>
    <a class="btn btn-outline-warning"
       href="create.php?id=<?= (int)$docs['TAX'] ?>">
      ✏️ แก้ไขใบกำกับภาษี (TAX)
    </a>
  <?php endif; ?>

  <?php if (!empty($docs['REC'])): ?>
    <a class="btn btn-outline-success"
       href="create.php?id=<?= (int)$docs['REC'] ?>">
      ✏️ แก้ไขใบเสร็จรับเงิน (REC)
    </a>
  <?php endif; ?>

  <?php if (!empty($docs['BILL'])): ?>
    <a class="btn btn-outline-secondary"
       href="create.php?id=<?= (int)$docs['BILL'] ?>">
      ✏️ แก้ไขใบวางบิล (BILL)
    </a>
  <?php endif; ?>
</div>
