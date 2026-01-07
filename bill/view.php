<style>
  :root{
    --line:#555;
    --light:#f2f2f2;
  }

  /* สีสำหรับแต่ละประเภทเอกสาร */
  .doc-quo {
    --doc-primary: #6366f1;
    --doc-light: #e0e7ff;
    --doc-text: #4338ca;
  }

  .doc-bill {
    --doc-primary: #f59e0b;
    --doc-light: #fef3c7;
    --doc-text: #b45309;
  }

  .doc-tax {
    --doc-primary: #10b981;
    --doc-light: #d1fae5;
    --doc-text: #047857;
  }

  .doc-rec {
    --doc-primary: #8b5cf6;
    --doc-light: #ede9fe;
    --doc-text: #6d28d9;
  }

  body {
    background: #f5f5f5;
    font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  /* กระดาษ */
.paper{
  width: 210mm;
  margin: 20px auto;
  background:#fff;
  color:#111;
  font-size: 14px;
  padding: 0;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}


  /* Header สีบน */
  .doc-header {
    background: var(--doc-primary);
    color: white;
    padding: 20px 30px;
    position: relative;
    min-height: 120px;
    border-radius: 12px 12px 0 0;
  }

  .doc-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
  }

  .doc-header .company-info {
    font-size: 13px;
    line-height: 1.5;
    margin-top: 5px;
  }

  .doc-number {
    position: absolute;
    top: 20px;
    right: 30px;
    text-align: right;
  }

  .doc-number .label {
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 5px;
  }

  .doc-number .number {
    font-size: 14px;
    font-weight: 600;
  }

  /* Content area */
  .doc-content {
    padding: 15px 15px;
  }

  /* กล่องข้อมูล */
  .info-box {
    border: 2px solid var(--doc-primary);
    background: white;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
  }

  .info-box.light-bg {
    background: var(--doc-light);
  }

  .info-box h6 {
    font-size: 13px;
    font-weight: 700;
    color: var(--doc-text);
    margin: 0 0 8px 0;
  }

  .info-row {
    display: flex;
    margin: 4px 0;
    font-size: 13px;
  }

  .info-row .label {
    min-width: 120px;
    color: #333;
  }

  .info-row .value {
    flex: 1;
    font-weight: 600;
    color: #000;
  }

  /* ตารางรายการ */
  table.items {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 13px;
  }

  table.items thead th {
    background: var(--doc-primary);
    color: white;
    padding: 12px 10px;
    /* text-align: center; */
    font-weight: 700;
    border: 2px solid var(--doc-primary);
  }

  table.items tbody td {
    padding: 10px;
    border: 1px solid #d1d5db;
    vertical-align: top;
  }

  table.items tbody tr:hover {
    background: #f9fafb;
  }

  .text-center { text-align: center; }
  .text-right { text-align: right; }

/* layout หลัก */
.bottom-section{
  display: grid;
  grid-template-columns: 1fr 360px;
  grid-template-rows: auto auto;
  gap: 14px;
  align-items: start;
}

/* ตำแหน่ง */
.total-box{ grid-column: 1 / 2; grid-row: 1; }
.remark-box{ grid-column: 1 / 2; grid-row: 2; }
.summary-box{ grid-column: 2 / 3; grid-row: 1 / span 2; }

/* ===== กล่องข้อความตัวอักษร (บนซ้าย) ===== */
.total-box{
  background: var(--doc-light);
  border-radius: 6px;
  padding: 8px 12px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.total-box h6{
  margin: 0;
  font-size: 14px;
  font-weight: 700;
  color: var(--doc-primary);
}

/* ===== กล่อง remark + summary ===== */
.remark-box,
.summary-box{
  background: var(--doc-light);
  border: 2px solid var(--doc-primary);
  border-radius: 10px;
  padding: 14px 16px;
}

.remark-box{
    min-height: 160px;
}
.remark-box h6{
  margin: 0 0 8px 0;
  font-size: 13px;
  font-weight: 700;
  color: var(--doc-text);
}

.summary-box h6{
  margin: 0 0 8px 0;
  font-size: 13px;
  font-weight: 700;
  color: var(--doc-text);
}


/* แถวข้อมูล */
.summary-row{
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 12px;
  padding: 6px 0;
  font-size: 13px;
  font-weight: 700;
  color: #111;
}

.summary-row .label{
  opacity: .95;
}

.summary-row .value{
  min-width: 120px;
  text-align: right;
}

/* เส้นคั่น + ยอดรวม */
.summary-total{
  margin-top: 10px;
  padding-top: 10px;
  border-top: 2px solid var(--doc-primary);
  color: var(--doc-text);
  font-size: 15px;
  font-weight: 800;
}

.summary-total .value{
  font-size: 16px;
  font-weight: 900;
}

  /* กล่องลายเซ็น */
  .signature-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 20px;
  }

  .signature-box {
    border: 2px solid var(--doc-primary);
    background: var(--doc-light);
    padding: 15px;
    border-radius: 8px;
  }

  .signature-box h6 {
    font-size: 13px;
    font-weight: 700;
    margin: 0 0 8px 0;
    color: var(--doc-text);
  }

  .sig-line {
    border-bottom: 2px solid #333;
    display: inline-block;
    min-width: 200px;
    height: 30px;
    vertical-align: bottom;
  }

  .muted { color: #6b7280; }

/* ให้ A4 ตรงจริง */
@page{
  size: A4;
  margin: 0;
}

@media print{
  html, body{
    margin: 0 !important;
    padding: 0 !important;
    background: #fff !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .no-print{ display:none !important; }

  .paper{
    width: 210mm !important;
    height: 297mm !important;      /* ใช้ height (ไม่ใช้ min-height) กันแตกหน้า */
    margin: 0 !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    box-sizing: border-box;
    padding: 10mm;                 /* ระยะขอบ “ในกระดาษ” คุมได้แน่นกว่า @page margin */
    page-break-after: always;
    break-after: page;
    overflow: hidden;              /* กันล้นแล้วโดนแตกหน้าแบบคาดไม่ถึง */
  }

  /* ไม่ต้องมีหน้าว่างท้ายสุด */
  .paper:last-child{
    page-break-after: auto;
    break-after: auto;
  }
}



  
  
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<?php
require_once "../layout/header.php";

$id = (int)($_GET['id'] ?? 0);
$groupNo = trim($_GET['group_no'] ?? '');

$minRows = 6;



function typeName($t){
  return [
    'QUO'  => 'ใบเสนอราคา',
    'BILL' => 'ใบวางบิล',
    'REC'  => 'ใบเสร็จรับเงิน',
    'TAX'  => 'ใบกำกับภาษี',
  ][$t] ?? $t;
}

function getDocClass($type){
  return [
    'QUO'  => 'doc-quo',
    'BILL' => 'doc-bill',
    'REC'  => 'doc-rec',
    'TAX'  => 'doc-tax',
  ][$type] ?? 'doc-quo';
}

function fetchDocWithCustomer(PDO $pdo, int $id){
  $stmt = $pdo->prepare("
    SELECT d.*, c.name AS customer_name, c.tax_id, c.address, c.phone, c.email
    FROM documents d
    JOIN customers c ON c.id = d.customer_id
    WHERE d.id = ?
  ");
  $stmt->execute([$id]);
  return $stmt->fetch();
}

  function numtothaistring($num)
{
$return_str = "";
$txtnum1 = array('','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า');
$txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน');
$num_arr = str_split($num);
$count = count($num_arr);
foreach($num_arr as $key=>$val)
{
	if($count > 1 && $val == 1 && $key ==($count-1))
	$return_str .= "เอ็ด";
	else
	$return_str .= $txtnum1[$val].$txtnum2[$count-$key-1];
}
return $return_str ;
}


  function numtothai($num)
{
    $return = "";
    $num = str_replace(",", "", $num);
    $number = explode(".", $num);

    if (sizeof($number) > 2) {
        return 'รูปแบบข้อมูลไม่ถูกต้อง';
        exit;
    }

    if (isset($number[0])) {
        $return .= numtothaistring($number[0]) . "บาท";
    } else {
        return 'รูปแบบข้อมูลไม่ถูกต้อง';
        exit;
    }

    if (isset($number[1])) {
        $stang = intval($number[1]);
        if ($stang > 0) {
            $return .= numtothaistring($stang) . "สตางค์";
        } else {
            $return .= "ถ้วน";
        }
    }else{
	$return .= "ถ้วน";	
	}

    return $return;
}

function fetchItems(PDO $pdo, int $docId){
  $stmt = $pdo->prepare("SELECT * FROM document_items WHERE document_id=? ORDER BY id ASC");
  $stmt->execute([$docId]);
  return $stmt->fetchAll();
}

$docs = [];

if ($groupNo !== '') {
  $stmt = $pdo->prepare("
    SELECT d.*, c.name AS customer_name, c.tax_id, c.address, c.phone, c.email
    FROM documents d
    JOIN customers c ON c.id = d.customer_id
    WHERE d.group_no = ?
    ORDER BY FIELD(d.doc_type,'QUO','BILL','TAX','REC'), d.id ASC
  ");
  $stmt->execute([$groupNo]);
  $docs = $stmt->fetchAll();
  if (!$docs || count($docs) === 0) die("Not found (group_no)");
} else {
  if ($id <= 0) die("Invalid id");
  $doc = fetchDocWithCustomer($pdo, $id);
  if (!$doc) die("Not found");
  $docs = [$doc];
  $groupNo = trim($doc['group_no'] ?? '');
}

$itemsByDocId = [];
foreach ($docs as $d) {
  $itemsByDocId[(int)$d['id']] = fetchItems($pdo, (int)$d['id']);
}

$title = (count($docs) > 1)
  ? ("ชุดเอกสาร " . $groupNo)
  : (typeName($docs[0]['doc_type']) . " " . $docs[0]['doc_no']);
?>

<div class="no-print d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php">← รายการเอกสาร</a>
    <a class="btn btn-outline-secondary" href="create.php">+ ออกเอกสารใหม่</a>
    <?php if ($groupNo !== '' && count($docs) === 1): ?>
      <a class="btn btn-outline-primary" href="view.php?group_no=<?= htmlspecialchars($groupNo) ?>">ดูชุด</a>
    <?php endif; ?>
    <button class="btn btn-success no-print" onclick="downloadPDF()">บันทึก PDF</button>
  </div>

<!-- <button class="btn btn-outline-success no-print" onclick="downloadPNG()">บันทึกเป็นรูป</button> -->

</div>

<?php if (count($docs) > 1): ?>
  <div class="no-print alert alert-info">
    <b>ชุดเอกสาร:</b> <?= htmlspecialchars($groupNo) ?>
    <span class="ms-2 text-muted">(QUO/BILL/TAX/REC)</span>
  </div>
<?php endif; ?>

<?php foreach ($docs as $docIndex => $doc): ?>
  <?php
    $docTypeName = typeName($doc['doc_type']);
    $docClass = getDocClass($doc['doc_type']);
    $items = $itemsByDocId[(int)$doc['id']] ?? [];
    $copyTypes = ['ต้นฉบับ', 'สำเนา'];
  ?>

  <?php if (($doc['doc_type'] ?? '') === 'BILL'): ?>
    <?php
      $billRows = [];
      $billSum = 0.0;
      $st = $pdo->prepare("SELECT group_no FROM bill_groups WHERE bill_id=? ORDER BY id ASC");
      $st->execute([$doc['id']]);
      $groups = $st->fetchAll(PDO::FETCH_COLUMN);

      foreach ($groups as $g) {
        $st2 = $pdo->prepare("
          SELECT doc_no, doc_date, total
          FROM documents
          WHERE group_no=? AND doc_type IN ('TAX','QUO')
          ORDER BY FIELD(doc_type,'TAX','QUO'), id ASC
          LIMIT 1
        ");
        $st2->execute([$g]);
        $ref = $st2->fetch();

        $refNo   = $ref['doc_no']   ?? '-';
        $refDate = $ref['doc_date'] ?? '-';
        $amt     = (float)($ref['total'] ?? 0);

        $billRows[] = [
          'ref_no'   => $refNo,
          'ref_date' => $refDate,
          'amount'   => $amt,
        ];
        $billSum += $amt;
      }
    ?>
     <?php foreach ($copyTypes as $copyType): ?>
    <div class="paper <?= $docClass ?>">
      <div class="doc-header">
        <div class="doc-number">
          <div class="label"><?= htmlspecialchars($docTypeName) ?> </div>
          <div class="number mb-1">เลขที่วางบิล <?= htmlspecialchars($doc['doc_no']) ?></div>
          <span style = 'font-weight:700;font-size:14px;'>( <?= htmlspecialchars($copyType) ?>  )</span>
        </div>
        <h3>หจก. อวยพร เอ็นจิเนี่ยริ่ง</h3>
        <div class="company-info">
          เลขประจำตัวผู้เสียภาษี: 0103554048312 สาขา: สำนักงานใหญ่ <br>
          299/495 ถนน สายไหม แขวงสายไหม เขตสายไหม กรุงเทพฯ 10220<br>
          โทร: 02-974-9226 Email: otp24hr@gmail.com
        </div>
      </div>

      <div class="doc-content">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:20px;">
          <div class="info-box">
            <h6>ถึง (To)</h6>
            <div style="font-weight:700;font-size:14px;margin-bottom:5px;">
              <?= htmlspecialchars($doc['customer_name']) ?>
            </div>
            <?php if (!empty($doc['tax_id'])): ?>
              <div style="font-size:13px;">เลขประจำตัวผู้เสียภาษี: <?= htmlspecialchars($doc['tax_id']) ?></div>
            <?php endif; ?>
            <?php if (!empty($doc['address'])): ?>
              <div style="font-size:13px;margin-top:5px;"><?= nl2br(htmlspecialchars($doc['address'])) ?></div>
            <?php endif; ?>
            <?php if (!empty($doc['phone']) || !empty($doc['email'])): ?>
              <div style="font-size:12px;color:#6b7280;margin-top:5px;">
                <?php if (!empty($doc['phone'])): ?>โทร: <?= htmlspecialchars($doc['phone']) ?><?php endif; ?>
                <?php if (!empty($doc['email'])): ?> Email: <?= htmlspecialchars($doc['email']) ?><?php endif; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="info-box light-bg">
            <h6>ข้อมูลเอกสาร</h6>
            <div class="info-row">
              <span class="label">เลขที่วางบิล </span>
              <span class="value"><?= htmlspecialchars($doc['doc_no']) ?></span>
            </div>
            <div class="info-row">
              <span class="label">วันที่เอกสาร</span>
              <span class="value"><?= htmlspecialchars($doc['doc_date']) ?></span>
            </div>
            <?php if (!empty($doc['due_date'])): ?>
            <div class="info-row">
              <span class="label">ถึงกำหนด</span>
              <span class="value"><?= htmlspecialchars($doc['due_date']) ?></span>
            </div>
            <div class="info-row">
              <span class="label">เครดิต</span>
              <span class="value">-</span>
            </div>
            <?php endif; ?>
            <div class="info-row">
              <span class="label">เงื่อนไขชำระ</span>
              <span class="value"><?= htmlspecialchars($doc['payment_term'] ?? 'เงินสด') ?></span>
            </div>
          </div>
        </div>

        <table class="items">
          <thead>
            <tr>
              <th style="width:60px;">ลำดับ</th>
              <th class="text-center">เลขที่ใบกำกับ/เอกสารอ้างอิง</th>
              <th class="text-center" style="width:120px;">วันที่</th>
              <th class="text-right"  style="width:140px;">จำนวนเงิน</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($billRows as $i => $r): ?>
              <tr>
                <td class="text-center"><?= $i+1 ?></td>
                <td><?= htmlspecialchars($r['ref_no']) ?></td>
                <td class="text-center"><?= htmlspecialchars($r['ref_date']) ?></td>
                <td class="text-right"><?= number_format((float)$r['amount'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php
          $rowCount = count($billRows);
          $emptyRows = max(0, $minRows - $rowCount);
          ?>
            <!-- แถวเปล่าเติมความสูง -->
            <?php for ($i=0; $i<$emptyRows; $i++): ?>
              <tr class="empty-row">
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>

        <div class="bottom-section">
          <div class="total-box">
            <h6>( <?= numtothai(number_format((float)($doc['total'] ?? 0), 2)) ?> )</h6>
          </div>
          <div class="remark-box">
            <h6 style="margin-top:5px;">หมายเหตุ (Remark)</h6>
            <div style="font-size:13px;margin-top:8px;">
              <?= nl2br(htmlspecialchars($doc['note'] ?? '* รับประกัน 30 วัน *')) ?>
            </div>
          </div>

          <div class="summary-box">
            <h6 style="margin-top:5px;">รวมทั้งหมด</h6>
            <div class="summary-row">
              <span class="label">รวมเป็นเงิน</span>
              <span class="value"><?= number_format((float)$billSum, 2) ?></span>
            </div>
            <div class="summary-row">
              <span class="label">หัก ส่วนลด</span>
              <span class="value"><?= number_format((float)$doc['discount_amount'], 2) ?></span>
            </div>
            <div class="summary-row">
              <span class="label">จำนวนเงินหลังหักส่วนลด</span>
              <span class="value"><?= number_format((float)(($doc['subtotal'] ?? 0) - ($doc['discount_amount'] ?? 0)), 2) ?></span>
            </div>
            <!-- <div class="summary-row">
              <span class="label">ภาษีมูลค่าเพิ่ม <?= number_format((float)$doc['vat_rate'], 2) ?>%</span>
              <span class="value"><?= number_format((float)$doc['vat_amount'], 2) ?></span>
            </div> -->
            <div class="summary-row summary-total">
              <span class="label">จำนวนเงินรวมทั้งสิ้น</span>
              <span class="value"><?= number_format((float)$billSum, 2) ?></span>
            </div>
          </div>
        </div>

        <div class="signature-section" style="margin-top:30px;">
          <div class="signature-box text-center">
            <h6>ข้อมลูวางบิล</h6>
            <div style="margin-top:15px;">
              <div><b>ผู้รับวางบิล</b></div>
              <div class="sig-line" style="margin-top:10px;"></div>
            </div>
            <div style="margin-top:15px;">
              <b>วันที่รับ</b> <span class="sig-line" style="min-width:150px;"></span>
            </div>
            <div style="margin-top:10px;">
              <b>วันที่นัดรับเช็ค</b> <span class="sig-line" style="min-width:150px;"><span class="value"><?= htmlspecialchars($doc['due_date']) ?></span></span>
            </div>
          </div>

          <div class="signature-box text-center">
            <h6>ขอแสดงความนับถือ</h6>
            <div style="margin-top:15px;">
              <div><b>ผู้วางบิล</b></div>
              <div class="sig-line" style="margin-top:10px;"></div>
              <div class="muted" style="margin-top:5px;">(ผู้วางบิล)</div>
            <div style="margin-top:15px;">
              <b>วันที่รับ</b> <span class="sig-line" style="min-width:150px;"></span>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; // end copyTypes ?>
  <?php else: ?>
    <?php foreach ($copyTypes as $copyType): ?>
    <div class="paper <?= $docClass ?>">
      <div class="doc-header">
        <div class="doc-number">
          <div class="label"><?= htmlspecialchars($docTypeName) ?> </div>
          <div class="number mb-1">เลขที่เอกสาร : <?= htmlspecialchars($doc['doc_no']) ?></div>
          <span style = 'font-weight:700;font-size:14px;'>( <?= htmlspecialchars($copyType) ?> )</span>
        </div>
        <h3>หจก. อวยพร เอ็นจิเนี่ยริ่ง</h3>
        <div class="company-info">
          เลขประจำตัวผู้เสียภาษี: 0103554048312 สาขา: สำนักงานใหญ่ <br>
          299/495 ถนน สายไหม แขวงสายไหม เขตสายไหม กรุงเทพฯ 10220<br>
          โทร: 02-974-9226 Email: otp24hr@gmail.com
        </div>
      </div>

      <div class="doc-content">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:20px;">
          <div class="info-box">
            <h6>ถึง (To)</h6>
            <div style="font-weight:700;font-size:14px;margin-bottom:5px;">
              <?= htmlspecialchars($doc['customer_name']) ?>
            </div>
            <?php if (!empty($doc['tax_id'])): ?>
              <div style="font-size:13px;">เลขประจำตัวผู้เสียภาษี: <?= htmlspecialchars($doc['tax_id']) ?></div>
            <?php endif; ?>
            <?php if (!empty($doc['address'])): ?>
              <div style="font-size:13px;margin-top:5px;"><?= nl2br(htmlspecialchars($doc['address'])) ?></div>
            <?php endif; ?>
            <?php if (!empty($doc['phone']) || !empty($doc['email'])): ?>
              <div style="font-size:12px;color:#6b7280;margin-top:5px;">
                <?php if (!empty($doc['phone'])): ?>โทร: <?= htmlspecialchars($doc['phone']) ?><?php endif; ?>
                <?php if (!empty($doc['email'])): ?> Email: <?= htmlspecialchars($doc['email']) ?><?php endif; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="info-box light-bg">
            <h6>ข้อมูลเอกสาร</h6>
            <div class="info-row">
              <span class="label">เลขที่วางบิล </span>
              <span class="value"><?= htmlspecialchars($doc['doc_no']) ?></span>
            </div>
            <div class="info-row">
              <span class="label">วันที่เอกสาร</span>
              <span class="value"><?= htmlspecialchars($doc['doc_date']) ?></span>
            </div>
            <?php if (!empty($doc['due_date'])): ?>
            <div class="info-row">
              <span class="label">ถึงกำหนด</span>
              <span class="value"><?= htmlspecialchars($doc['due_date']) ?></span>
            </div>
            <div class="info-row">
              <span class="label">เครดิต</span>
              <span class="value">-</span>
            </div>
            <?php endif; ?>
            <div class="info-row">
              <span class="label">เงื่อนไขชำระ</span>
              <span class="value"><?= htmlspecialchars($doc['payment_term'] ?? 'เงินสด') ?></span>
            </div>
          </div>
        </div>

        <table class="items">
          <thead>
            <tr>
              <th class="text-center" style="width:60px;">ลำดับ</th>
              <th class="text-center">รหัสสินค้า/รายละเอียด</th>
              <th class="text-center" style="width:90px;">จำนวน</th>
              <th class="text-center" style="width:90px;">หน่วย</th>
              <th class="text-right" style="width:120px;">หน่วยละ</th>
              <th class="text-right" style="width:130px;">จำนวนเงิน</th>
            </tr>
          </thead>
          <tbody >
            <?php foreach ($items as $i => $it): ?>
              <tr>
                <td class="text-center"><?= $i+1 ?></td>
                <td>
                  <div style="font-weight:700;"><?= htmlspecialchars($it['item_name']) ?></div>
                  <?php if (!empty($it['item_desc'])): ?>
                    <div class="muted" style="font-size:12px;margin-top:3px;">
                      <?= nl2br(htmlspecialchars($it['item_desc'])) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td class="text-center"><?= number_format((float)$it['qty'], 0) ?></td>
                <td class="text-center"><?= htmlspecialchars($it['unit']) ?></td>
                <td class="text-right"><?= number_format((float)$it['unit_price'], 2) ?></td>
                <td class="text-right"><?= number_format((float)$it['line_total'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
              <?php

            $rowCount = count($items);
            $emptyRows = max(0, $minRows - $rowCount);
            ?>
              <!-- แถวเปล่าเติมความสูง -->
              <?php for ($i=0; $i<$emptyRows; $i++): ?>
                <tr class="empty-row">
                  <td>&nbsp;</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
              <?php endfor; ?>
          </tbody>
        </table>

        <div class="bottom-section">
          <div class="total-box">
            <h6>( <?= numtothai(number_format((float)($doc['total'] ?? 0), 2)) ?> )</h6>
          </div>
          <div class="remark-box">
            <h6 style="margin-top:5px;">หมายเหตุ (Remark)</h6>
            <div style="font-size:13px;margin-top:8px;">
              <?= nl2br(htmlspecialchars($doc['note'] ?? '* รับประกัน 30 วัน *')) ?>
            </div>
          </div>

          <div class="summary-box">
            <div class="summary-row">
              <span class="label">รวมเป็นเงิน</span>
              <span class="value"><?= number_format((float)$doc['subtotal'], 2) ?></span>
            </div>
            <div class="summary-row">
              <span class="label">หัก ส่วนลด</span>
              <span class="value"><?= number_format((float)$doc['discount_amount'], 2) ?></span>
            </div>
            <div class="summary-row">
              <span class="label">จำนวนเงินหลังหักส่วนลด</span>
              <span class="value"><?= number_format((float)(($doc['subtotal'] ?? 0) - ($doc['discount_amount'] ?? 0)), 2) ?></span>
            </div>
            <div class="summary-row">
              <span class="label">ภาษีมูลค่าเพิ่ม <?= number_format((float)$doc['vat_rate'], 2) ?>%</span>
              <span class="value"><?= number_format((float)$doc['vat_amount'], 2) ?></span>
            </div>
            <div class="summary-row summary-total">
              <span class="label">จำนวนเงินรวมทั้งสิ้น</span>
              <span class="value"><?= number_format((float)$doc['total'], 2) ?></span>
            </div>
          </div>
        </div>

        <div class="signature-section">
          <?php if (($doc['doc_type'] ?? '') === 'QUO'):   $owner = 'ผู้เสนอราคา';  ?>
            <div class="signature-box text-center">
              <h6>กรุณาเซ็นยืนยันการสั่งซื้อด้านล่างนี้</h6>
              <!-- <div class="muted" style="font-size:12px;">Please sign below for purchasing confirmation.</div> -->
              <div style="margin-top:20px;">
                <div><b>ในนาม</b></div>
                <div class="sig-line" style="margin-top:10px;"></div>
                <div class="muted" style="margin-top:5px;">( ผู้สั่งซื้อสินค้า )</div>
              </div>
              <div style="margin-top:15px;">
                <div>วันที่ / Date: <span class="sig-line" style="min-width:150px;"></span></div>
              </div>
            </div>
          <?php else:  $owner = 'ผู้ตรวจเช็คสินค้า'; ?>
            <div class="signature-box text-center">
              <h6>ได้รับสินค้าตามรายการถูกต้องแล้ว</h6>
              <div style="margin-top:20px;">
                <div><b>ผู้รับสินค้า</b></div>
                <div class="sig-line" style="margin-top:10px;"></div>
                <div class="muted" style="margin-top:5px;">( <?= htmlspecialchars($owner) ?> )</div>
              </div>
              <div style="margin-top:15px;">
                <div>วันที่ / Date: <span class="sig-line" style="min-width:150px;"></span></div>
              </div>
            </div>
          <?php endif; ?>
          <div class="signature-box text-center">
            <h6>ขอแสดงความนับถือ</h6>
            <div style="margin-top:20px;">
              <div><b>ในนาม</b></div>
              <div class="sig-line" style="margin-top:10px;"></div>
              <div class="muted" style="margin-top:5px;">( <?= htmlspecialchars($owner) ?> )</div>
              <div style="margin-top:15px;">
                <div>วันที่ / Date: <span class="sig-line" style="min-width:150px;"></span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php endif; ?>

<?php endforeach; ?>


<script>
async function downloadPDF() {
  const { jsPDF } = window.jspdf;

  const papers = document.querySelectorAll('.paper'); // เอาทุกใบ
  if (!papers.length) return;

  const pdf = new jsPDF('p', 'mm', 'a4'); // A4 แนวตั้ง
  const pageW = 210, pageH = 297;

  for (let i = 0; i < papers.length; i++) {
    const el = papers[i];

    // ทำให้พื้นหลังขาว (กันบางทีโปร่ง)
    const canvas = await html2canvas(el, {
      scale: 2,
      useCORS: true,
      backgroundColor: '#ffffff'
    });

    const imgData = canvas.toDataURL('image/jpeg', 0.95);

    // คำนวณให้พอดี A4 (ใส่เต็มหน้า)
    if (i > 0) pdf.addPage('a4', 'p');
    pdf.addImage(imgData, 'JPEG', 0, 0, pageW, pageH);
  }

  pdf.save('document.pdf');
}

async function downloadPNG() {
  const el = document.querySelector('.paper'); // เอาใบแรก
  if (!el) return;

  const canvas = await html2canvas(el, {
    scale: 2,
    useCORS: true,
    backgroundColor: '#ffffff'
  });

  const link = document.createElement('a');
  link.download = 'document.png';
  link.href = canvas.toDataURL('image/png');
  link.click();
}
</script>


<?php
require_once "../layout/footer.php";
?>