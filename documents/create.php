<?php

require_once "../layout/header.php";

$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll();

$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;

$doc = null;
$items = [];

if ($isEdit) {
  $st = $pdo->prepare("SELECT * FROM documents WHERE id=?");
  $st->execute([$id]);
  $doc = $st->fetch();
  if (!$doc) die("ไม่พบเอกสาร");

  $st = $pdo->prepare("SELECT * FROM document_items WHERE document_id=? ORDER BY id ASC");
  $st->execute([$id]);
  $items = $st->fetchAll();
}


?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>ออกเอกสาร</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">
  <div class="d-flex justify-content-between align-items-center">
    <h3>ออกเอกสาร</h3>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="../customers/index.php">ลูกค้า</a>
      <a class="btn btn-outline-secondary" href="index.php">รายการเอกสาร</a>
    </div>
  </div>

  <form method="post" action="<?= $isEdit ? 'update.php' : 'save.php' ?>" class="mt-3">
<?php if ($isEdit): ?>
  <input type="hidden" name="id" value="<?= (int)$doc['id'] ?>">
<?php endif; ?>

    <div class="row g-2">
	<div class="col-md-3">
	  <label class="form-label">โหมดการสร้าง</label>
    <select id="doc_mode" name="doc_mode" class="form-select" required>
      <option value="FULL" <?= ($doc['doc_mode'] ?? '')==='FULL'?'selected':'' ?>>ครบชุด</option>
      <option value="SINGLE" <?= ($doc['doc_mode'] ?? '')==='SINGLE'?'selected':'' ?>>สร้างใบเดียว</option>
    </select>

	</div>

	<div class="col-md-3">
	  <label class="form-label">ประเภทเอกสาร</label>
	  <select id="doc_type" name="doc_type" class="form-select" required <?= $isEdit ? 'disabled' : '' ?>>
		<option value="BILL">ใบวางบิล (BILL)</option>
		<option value="QUO">ใบเสนอราคา (QUO)</option>
		<option value="REC">ใบเสร็จรับเงิน (REC)</option>
		<option value="TAX">ใบกำกับภาษี (TAX)</option>
	  </select>
	</div>


      <div class="col-md-3">
        <label class="form-label">เลขที่เอกสาร (อัตโนมัติ)</label>
        <input id="doc_no" name="doc_no" class="form-control"
       value="<?= htmlspecialchars($doc['doc_no'] ?? '') ?>" readonly>

        <div class="form-text">ระบบจะสร้างเลขให้ตามประเภท + เดือน</div>
      </div>

      <div class="col-md-3">
        <label class="form-label">วันที่เอกสาร</label>
      <input type="date" name="doc_date" class="form-control" required
            value="<?= htmlspecialchars($doc['doc_date'] ?? date('Y-m-d')) ?>">

      </div>

      <div class="col-md-3">
        <label class="form-label">ครบกำหนด (ถ้ามี)</label>
        <input type="date" name="due_date" class="form-control">
      </div>

      <div class="col-md-6">
        <label class="form-label">ลูกค้า</label>
        <select name="customer_id" class="form-select" required <?= $isEdit ? 'disabled' : '' ?>>
          <?php foreach ($customers as $c): ?>
            <option value="<?= (int)$c['id'] ?>"
              <?= (($doc['customer_id'] ?? 0)==$c['id'])?'selected':'' ?>>
              <?= htmlspecialchars($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

      </div>

      <div class="col-md-6">
        <label class="form-label">หมายเหตุ</label>
        <textarea name="note" class="form-control" rows="2"><?= htmlspecialchars($doc['note'] ?? '') ?></textarea>

      </div>
    </div>

    <hr class="my-3">

    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0">รายการ (พิมพ์เอง)</h5>
      <button type="button" class="btn btn-secondary" onclick="addRow()">+ เพิ่มรายการ</button>
    </div>

    <div id="items" class="mt-3"></div>

    <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary">
      <?= $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกเอกสาร' ?>
    </button>

      <a class="btn btn-outline-secondary" href="index.php">ยกเลิก</a>
    </div>
  </form>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const mode = document.getElementById('doc_mode');
  const type = document.getElementById('doc_type');

  function syncUI(){
    if (mode.value === 'FULL') {
      // ครบชุดเริ่มจาก QUO เสมอ
      type.value = 'QUO';
      type.disabled = true;
    } else {
      type.disabled = false;
    }
  }

  mode.addEventListener('change', syncUI);
  syncUI();
});
</script>


<script>
function escapeHtml(str){
  return String(str ?? '')
    .replaceAll('&','&amp;')
    .replaceAll('<','&lt;')
    .replaceAll('>','&gt;')
    .replaceAll('"','&quot;')
    .replaceAll("'","&#039;");
}

// ===== เลขเอกสารออโต้ =====
async function refreshDocNo() {
  const typeEl = document.querySelector('[name="doc_type"]');
  const dateEl = document.querySelector('[name="doc_date"]');
  const noEl   = document.getElementById('doc_no');

  if (!typeEl || !dateEl || !noEl) return;

  try {
    const url = new URL('next_no.php', window.location.href);
    url.searchParams.set('doc_type', typeEl.value);
    url.searchParams.set('doc_date', dateEl.value);

    const res = await fetch(url.toString());
    const text = await res.text();

    let data;
    try { data = JSON.parse(text); }
    catch(e) { console.error('next_no.php ไม่ใช่ JSON:', text); return; }

    if (data.doc_no) noEl.value = data.doc_no;
  } catch (err) {
    console.error('เรียก next_no.php ไม่สำเร็จ:', err);
  }
}

// ===== รายการ =====
let idx = 0;

function addRow(name='', qty=1, unit='ชิ้น', price=0){
  const wrap = document.getElementById('items');
  if (!wrap) return;

  const row = document.createElement('div');
  row.className = 'row g-2 align-items-end mb-2 border rounded p-2 mt-3';

  row.innerHTML = `
    <div class="col-md-6">
      <label class="form-label">รายละเอียด</label>
      <input name="items[${idx}][name]" class="form-control" required value="${escapeHtml(name)}">
    </div>

    <div class="col-md-2">
      <label class="form-label">จำนวน</label>
      <input type="number" step="0.01" name="items[${idx}][qty]" class="form-control" value="${qty}" required>
    </div>

    <div class="col-md-2">
      <label class="form-label">หน่วย</label>
      <input name="items[${idx}][unit]" class="form-control" value="${escapeHtml(unit)}">
    </div>

    <div class="col-md-2">
      <label class="form-label">ราคา/หน่วย</label>
      <input type="number" step="0.01" name="items[${idx}][price]" class="form-control" value="${price}" required>
    </div>

    <div class="col-12">
      <button type="button" class="btn btn-outline-danger btn-sm"
        onclick="this.closest('.row').remove()">ลบรายการนี้</button>
    </div>
  `;

  wrap.appendChild(row);
  idx++;
}


document.addEventListener('DOMContentLoaded', () => {
  // 1) เลขเอกสารออโต้
  refreshDocNo();
  document.querySelector('[name="doc_type"]')?.addEventListener('change', refreshDocNo);
  document.querySelector('[name="doc_date"]')?.addEventListener('change', refreshDocNo);

  // 2) โหลดรายการ
  <?php if (!empty($isEdit) && $isEdit): ?>
    <?php foreach ($items as $it): ?>
      addRow(
        <?= json_encode($it['item_name'] ?? '') ?>,
        <?= (float)($it['qty'] ?? 1) ?>,
        <?= json_encode($it['unit'] ?? 'ชิ้น') ?>,
        <?= (float)($it['unit_price'] ?? 0) ?>
      );
    <?php endforeach; ?>

    // ถ้าเอกสารเดิมไม่มีรายการเลย กันหน้าโล่ง
    <?php if (count($items) === 0): ?>
      addRow();
    <?php endif; ?>
  <?php else: ?>
    addRow(); // โหมดสร้างใหม่
  <?php endif; ?>
});
</script>

</body>
</html>
