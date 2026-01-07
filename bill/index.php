<?php
require_once "../layout/header.php";

// =====================
// 1) กลุ่มที่ยังไม่วางบิล (Unbilled groups)
// =====================
// เอายอดจาก TAX เป็นหลัก ถ้าไม่มี TAX ใช้ QUO
$unbilled = $pdo->query("
  SELECT
    d.group_no,
    MAX(c.name) AS customer_name,
    MAX(d.doc_date) AS doc_date,

    -- ยอดจาก TAX ก่อน ถ้าไม่มี TAX ใช้ QUO
    COALESCE(
      MAX(CASE WHEN d.doc_type='TAX' THEN d.total END),
      MAX(CASE WHEN d.doc_type='QUO' THEN d.total END),
      0
    ) AS total,

    GROUP_CONCAT(DISTINCT d.doc_type ORDER BY FIELD(d.doc_type,'QUO','TAX','REC','BILL')) AS types,

    -- ids แยกประเภทไว้ทำลิงก์ badge
    MAX(CASE WHEN d.doc_type='QUO' THEN d.id END) AS quo_id,
    MAX(CASE WHEN d.doc_type='TAX' THEN d.id END) AS tax_id,
    MAX(CASE WHEN d.doc_type='REC' THEN d.id END) AS rec_id
  FROM documents d
  JOIN customers c ON c.id = d.customer_id
  LEFT JOIN bill_groups bg ON bg.group_no = d.group_no
  WHERE d.group_no IS NOT NULL AND d.group_no <> ''
    AND bg.group_no IS NULL
    AND d.doc_type IN ('QUO','TAX','REC')   -- กลุ่ม “งาน” ที่เกี่ยวข้อง
  GROUP BY d.group_no
  ORDER BY doc_date DESC, d.group_no DESC
")->fetchAll();

function hasType($typesStr, $t){
  return strpos(",".$typesStr.",", ",".$t.",") !== false;
}

// =====================
// 2) ใบวางบิลที่สร้างแล้ว (Bills)
// =====================
$bills = $pdo->query("
  SELECT d.id, d.doc_no, d.doc_date, d.total, d.customer_id, c.name AS customer_name
  FROM documents d
  JOIN customers c ON c.id = d.customer_id
  WHERE d.doc_type='BILL'
  ORDER BY d.id DESC
")->fetchAll();
?>


<h4 class="mb-3">ใบวางบิล (ยังไม่วางบิล)</h4>

<form method="post" action="bill_actions.php" onsubmit="return confirm('ย้ายกลุ่มเข้า BILL ที่เลือกใช่ไหม?');">
  <input type="hidden" name="action" value="assign">




  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th style="width:40px;" class="text-center align-middle">
          <input type="checkbox" id="checkAll" class="form-check-input" title="เลือกทั้งหมด">
        </th>
        <th>ออเดอร์</th>
        <th>มีเอกสาร</th>
        <th>วันที่</th>
        <th>ลูกค้า</th>
        <th class="text-end">ยอดเงิน</th>
        <th style="width:320px;">คำสั่ง</th>

      </tr>
    </thead>
    <tbody>
      <?php foreach ($unbilled as $d): ?>
        <?php $types = $d['types'] ?? ''; ?>
        <tr>
          <td class="text-center">
            <input class="form-check-input group-check" type="checkbox"
                   name="group_no[]"
                   value="<?= htmlspecialchars($d['group_no']) ?>">
          </td>

          <td>
            <a class="text-decoration-none fw-bold"
               href="view.php?group_no=<?= htmlspecialchars($d['group_no']) ?>">
              TAX<?= htmlspecialchars($d['group_no']) ?>
            </a>
          </td>

          <td>

            <?php if (!empty($d['quo_id'])): ?>
              <a class="badge text-bg-primary me-1 text-decoration-none"
                 href="view.php?id=<?= (int)$d['quo_id'] ?>">ใบเสนอ (QUO)</a>
            <?php endif; ?>

            <?php if (!empty($d['tax_id'])): ?>
              <a class="badge text-bg-warning me-1 text-decoration-none"
                 href="view.php?id=<?= (int)$d['tax_id'] ?>">ภาษี (TAX)</a>
            <?php endif; ?>

            <?php if (!empty($d['rec_id'])): ?>
              <a class="badge text-bg-success me-1 text-decoration-none"
                 href="view.php?id=<?= (int)$d['rec_id'] ?>">รับเงิน (REC)</a>
            <?php endif; ?>
          </td>

          <td><?= htmlspecialchars($d['doc_date']) ?></td>
          <td><?= htmlspecialchars($d['customer_name']) ?></td>
          <td class="text-end"><?= number_format((float)$d['total'], 2) ?></td>
        <td>
          <form method="post" action="bill_actions.php">
            <input type="hidden" name="action" value="assign">
            <input type="hidden" name="group_no[]" value="<?= htmlspecialchars($d['group_no']) ?>">

            <select name="bill_id" class="form-select form-select-sm"
                    onchange="this.form.submit()" required>
              <option value="">เลือกใบวางบิล</option>
              <?php foreach ($bills as $b): ?>
                <option value="<?= (int)$b['id'] ?>">
                  <?= htmlspecialchars($b['doc_no']) ?> (<?= htmlspecialchars($b['customer_name']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </form>
        </td>

        </tr>
      <?php endforeach; ?>

      <?php if (count($unbilled) === 0): ?>
        <tr><td colspan="7" class="text-center text-muted">ไม่มีชุดที่ยังไม่วางบิล</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
    <div class="d-flex justify-content-between align-items-center mb-2">
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-sm btn-primary" type="submit">สร้างใบวางบิล</button>
    </div>
  </div>

</form>

<hr class="my-4">

<h4 class="mb-3">ประวัติใบวางบิล</h4>
<form method="post" action="bill_actions.php" onsubmit="return confirm('ย้ายกลุ่มที่เลือกออกจาก BILL ใช่ไหม?');">
  <input type="hidden" name="action" value="remove">

<table class="table table-bordered table-hover">
  <thead>
    <tr>

      <th>ใบวางบิล</th>
      <th>เลขเอกสาร</th>
      <th>รายละเอียด</th>
      <th>วันที่</th>
      <th>ลูกค้า</th>
      <th class="text-end">ยอดรวม</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($bills as $b): ?>
    <!-- แถวหลัก: BILL -->
    <tr class="table-light">
      <td>
<span class="badge text-bg-secondary ms-2">ใบวางบิล</span>
      </td>
      <td class="text-muted">        
        <a class="fw-bold text-decoration-none" href="view.php?id=<?= (int)$b['id'] ?>">
          <?= htmlspecialchars($b['doc_no']) ?>
        </a>
      </td>
      <td><a class="badge text-bg-danger me-1 text-decoration-none" href="view.php?id=<?= (int)$b['id'] ?>">ใบวางบิล (Bill)</a></td>
      <td><?= htmlspecialchars($b['doc_date']) ?></td>
      <td><?= htmlspecialchars($b['customer_name']) ?></td>
      <td class="text-end"><b><?= number_format((float)$b['total'], 2) ?></b></td>
    </tr>

    <?php
      // กลุ่มใน BILL นี้
      $stg = $pdo->prepare("SELECT group_no FROM bill_groups WHERE bill_id=? ORDER BY id ASC");
      $stg->execute([(int)$b['id']]);
      $groups = $stg->fetchAll(PDO::FETCH_COLUMN);
    ?>

    <?php foreach ($groups as $gno): ?>
      <?php
        // หาเอกสารในกลุ่มเพื่อทำ badge link + ยอดจาก TAX/QUO
        $st = $pdo->prepare("
          SELECT doc_type, id, doc_date, total
          FROM documents
          WHERE group_no=?
        ");
        $st->execute([$gno]);
        $rows = $st->fetchAll();

        $ids = [];
        foreach ($rows as $r) $ids[$r['doc_type']] = (int)$r['id'];

        $ref = null;
        foreach ($rows as $r) { if ($r['doc_type']==='TAX') { $ref = $r; break; } }
        if (!$ref) { foreach ($rows as $r) { if ($r['doc_type']==='QUO') { $ref = $r; break; } } }

        $gDate  = $ref['doc_date'] ?? '';
        $gTotal = (float)($ref['total'] ?? 0);
      ?>

      <!-- แถวรอง: group_no -->
      <tr>
<td class="text-center">
  <input class="form-check-input" type="checkbox"
         name="group_no[]"
         value="<?= htmlspecialchars($gno) ?>">
</td>

        <td>
          <span class="text-muted">↳</span>
          <a class="fw-semibold small text-decoration-none" href="view.php?group_no=<?= htmlspecialchars($gno) ?>">
            TAX<?= htmlspecialchars($gno) ?>
          </a>
        </td>

        <td>
          <?php if (!empty($ids['QUO'])): ?>
            <a class="badge text-bg-primary me-1 text-decoration-none"
               href="view.php?id=<?= (int)$ids['QUO'] ?>">ใบเสนอ (QUO)</a>
          <?php endif; ?>
          <?php if (!empty($ids['TAX'])): ?>
            <a class="badge text-bg-warning me-1 text-decoration-none"
               href="view.php?id=<?= (int)$ids['TAX'] ?>">ภาษี (TAX)</a>
          <?php endif; ?>
          <?php if (!empty($ids['REC'])): ?>
            <a class="badge text-bg-success me-1 text-decoration-none"
               href="view.php?id=<?= (int)$ids['REC'] ?>">รับเงิน (REC)</a>
          <?php endif; ?>
        </td>

        <td><?= htmlspecialchars($gDate) ?></td>
        <td><?= htmlspecialchars($b['customer_name']) ?></td>
        <td class="text-end"><?= number_format($gTotal, 2) ?></td>
      </tr>
    <?php endforeach; ?>

  <?php endforeach; ?>

  <?php if (count($bills) === 0): ?>
    <tr><td colspan="5" class="text-center text-muted">ยังไม่มีใบวางบิล</td></tr>
  <?php endif; ?>
  </tbody>
</table>
  <div class="d-flex justify-content-start mb-2">
    <button class="btn btn-sm btn-danger" type="submit">
      ย้ายออก
    </button>
  </div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const checkAll = document.getElementById('checkAll');
  if (!checkAll) return;

  checkAll.addEventListener('change', () => {
    document.querySelectorAll('.group-check').forEach(cb => cb.checked = checkAll.checked);
  });
});
</script>

</body>
</html>
<?php
require_once "../layout/footer.php";
?>