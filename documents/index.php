<?php

require_once "../layout/header.php";

$sql = "
SELECT
  d.group_no,
  MIN(d.id) AS any_id,
  MAX(d.doc_date) AS doc_date,
  MAX(c.name) AS customer_name,
  MAX(d.total) AS total,
  GROUP_CONCAT(DISTINCT d.doc_type ORDER BY FIELD(d.doc_type,'QUO','BILL','TAX','REC')) AS types
FROM documents d
JOIN customers c ON c.id = d.customer_id
WHERE d.group_no IS NOT NULL AND d.group_no <> ''
GROUP BY d.group_no, d.customer_id
ORDER BY doc_date DESC, d.group_no DESC
";
$docs = $pdo->query($sql)->fetchAll();


function typeName($t) {
  return [
    'QUO'  => 'ใบเสนอราคา',
    'BILL' => 'ใบวางบิล',
    'REC'  => 'ใบเสร็จ',
    'TAX'  => 'ใบกำกับภาษี',
  ][$t] ?? $t;
}
?>



<?php
function hasType($typesStr, $t){
  return strpos(",".$typesStr.",", ",".$t.",") !== false;
}
?>

<table class="table table-bordered table-hover mt-3">
<thead>
  <tr>
    <th>ออเดอร์</th>
    <th>มีเอกสาร</th>
    <th>วันที่</th>
    <th>ลูกค้า</th>
    <th class="text-end">ยอดรวม</th>
    <th style="width:120px;">จัดการ</th>
  </tr>
</thead>


<tbody>
<?php foreach ($docs as $d): ?>
  <?php
    // ดึง id ของแต่ละประเภทใน group นี้ (ทำครั้งเดียวต่อแถว)
    $st = $pdo->prepare("
      SELECT doc_type, id
      FROM documents
      WHERE group_no=?
    ");
    $st->execute([$d['group_no']]);
    $ids = [];
    foreach ($st->fetchAll() as $r) {
      $ids[$r['doc_type']] = (int)$r['id'];
    }

    // เลือกใบหลักเปิด: ถ้ามี QUO ให้เปิด QUO ไม่งั้นเลือก any_id
    $openId = $ids['QUO'] ?? (int)$d['any_id'];
  ?>

  <tr>
    <td>
      <a class="text-decoration-none fw-bold"
         href="view.php?group_no=<?= htmlspecialchars($d['group_no']) ?>">
        <?= htmlspecialchars($d['group_no']) ?>
      </a>
    </td>

    <td>
      <!-- badge เอกสารชุด -->
      <a class="badge text-bg-success me-1 text-decoration-none"
         href="view.php?group_no=<?= htmlspecialchars($d['group_no']) ?>">
        เอกสารชุด
      </a>

      <?php if (!empty($ids['QUO'])): ?>
        <a class="badge text-bg-primary me-1 text-decoration-none"
           href="view.php?id=<?= (int)$ids['QUO'] ?>">
          ใบเสนอ (QUO)
        </a>
      <?php endif; ?>

      <?php if (!empty($ids['TAX'])): ?>
        <a class="badge text-bg-warning me-1 text-decoration-none"
           href="view.php?id=<?= (int)$ids['TAX'] ?>">
          ภาษี (TAX)
        </a>
      <?php endif; ?>

      <?php if (!empty($ids['REC'])): ?>
        <a class="badge text-bg-success me-1 text-decoration-none"
           href="view.php?id=<?= (int)$ids['REC'] ?>">
          รับเงิน (REC)
        </a>
      <?php endif; ?>


    </td>

    <td><?= htmlspecialchars($d['doc_date']) ?></td>
    <td><?= htmlspecialchars($d['customer_name']) ?></td>
    <td class="text-end"><?= number_format((float)$d['total'], 2) ?></td>
<td class="text-center">
  <a class="btn btn-sm btn-outline-secondary"
     href="create.php?id=<?= (int)$openId ?>">
    ✏️ แก้ไข
  </a>
</td>


  </tr>

<?php endforeach; ?>

<?php if (count($docs) === 0): ?>
  <tr><td colspan="6" class="text-center">ยังไม่มีเอกสาร</td></tr>
<?php endif; ?>
</tbody>

</table>


<?php
require_once "../layout/footer.php";
?>