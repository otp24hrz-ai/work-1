<?php
require_once "../layout/header.php";

$groups = $_POST['group_no'] ?? [];
if (!is_array($groups) || count($groups) === 0) {
  die("กรุณาเลือกอย่างน้อย 1 ชุด");
}

// ------------------- helper: genDocNo -------------------
function genDocNo(PDO $pdo, string $docType, string $docDate): string {
  $y = (int)substr($docDate, 0, 4);
  $m = (int)substr($docDate, 5, 2);

  $yy = $y % 100; // ค.ศ. 2 หลัก
  $yymm = str_pad((string)$yy, 2, '0', STR_PAD_LEFT)
        . str_pad((string)$m, 2, '0', STR_PAD_LEFT);

  $prefix = $docType . $yymm;

  $stmt = $pdo->prepare("
    SELECT doc_no
    FROM documents
    WHERE doc_no LIKE ?
    ORDER BY doc_no DESC
    LIMIT 1
  ");
  $stmt->execute([$prefix . "/%"]);
  $last = $stmt->fetchColumn();

  $next = 1;
  if ($last) {
    $parts = explode('/', $last);
    if (count($parts) === 2 && ctype_digit($parts[1])) {
      $next = (int)$parts[1] + 1;
    }
  }
  return $prefix . "/" . str_pad((string)$next, 3, '0', STR_PAD_LEFT);
}

// ------------------- helper: ดึงเอกสารอ้างอิงของ group (TAX ก่อน) -------------------
function pickRefDoc(PDO $pdo, string $groupNo): ?array {
  // TAX เป็นหลัก ถ้าไม่มี TAX ใช้ QUO
  $st = $pdo->prepare("
    SELECT id, doc_type, doc_no, doc_date, customer_id, total
    FROM documents
    WHERE group_no = ?
      AND doc_type IN ('TAX','QUO')
    ORDER BY FIELD(doc_type,'TAX','QUO'), id ASC
    LIMIT 1
  ");
  $st->execute([$groupNo]);
  $row = $st->fetch();
  return $row ?: null;
}

// ------------------- sanitize groups -------------------
$groups = array_values(array_filter(array_map('trim', $groups), fn($x) => $x !== ''));
$groups = array_values(array_unique($groups));

if (count($groups) === 0) {
  die("กรุณาเลือกอย่างน้อย 1 ชุด");
}

// ------------------- ตรวจว่ากลุ่มไหนวางบิลแล้ว -------------------
$already = [];
{
  $in = implode(',', array_fill(0, count($groups), '?'));
  $st = $pdo->prepare("SELECT group_no FROM bill_groups WHERE group_no IN ($in)");
  $st->execute($groups);
  $already = array_flip($st->fetchAll(PDO::FETCH_COLUMN));
}

// ------------------- รวมยอด + ตรวจลูกค้าเดียวกัน -------------------
$selected = [];         // group_no ที่จะเอาไปทำบิลจริง
$refDocs  = [];         // เก็บเอกสารอ้างอิงของแต่ละ group
$total    = 0.0;

$customerId = null;
$billDate = date('Y-m-d'); // ใช้วันนี้เป็นวันที่ BILL (ถ้าจะใช้ max doc_date ก็ทำได้)

$skipped = 0;

foreach ($groups as $g) {
  // ถ้าวางบิลแล้ว ข้าม
  if (isset($already[$g])) { $skipped++; continue; }

  $ref = pickRefDoc($pdo, $g);
  if (!$ref) { $skipped++; continue; }

  $refCustomer = (int)$ref['customer_id'];
  if ($customerId === null) $customerId = $refCustomer;

  // บังคับให้เป็นลูกค้าคนเดียวกัน
  if ($refCustomer !== $customerId) {
    // ถ้าต้องการรองรับหลายลูกค้า ให้แยกสร้างหลายบิล (คนละใบ) — แต่ตอนนี้ตาม requirement ทำใบเดียว
    die("เลือกหลายลูกค้าไม่ได้: กรุณาเลือกเฉพาะชุดของลูกค้าคนเดียวกัน");
  }

  $amt = (float)$ref['total'];
  if ($amt <= 0) { $skipped++; continue; }

  $selected[] = $g;
  $refDocs[$g] = $ref;
  $total += $amt;
}

if (count($selected) === 0) {
  die("ไม่มีชุดที่สร้างได้ (อาจถูกวางบิลแล้วทั้งหมด หรือไม่มี TAX/QUO ในชุด)");
}

if ($customerId === null) {
  die("ไม่พบลูกค้า");
}

// ------------------- สร้าง BILL 1 ใบ -------------------
$pdo->beginTransaction();

try {
  $billNo = genDocNo($pdo, 'BILL', $billDate);
  $note = "วางบิลรวมชุด: " . implode(', ', $selected);

  // IMPORTANT: ใบวางบิลไม่คิด VAT ซ้ำ (เพราะยอดมาจาก TAX แล้ว)
  // เลยตั้ง subtotal=total, vat=0 (หรือจะให้ subtotal=0 แล้ว total=total ก็ได้ตามชอบ)
  $subtotal = $total;
  $discount = 0.0;
  $vatRate = 0.0;
  $vatAmount = 0.0;

  $stmt = $pdo->prepare("
    INSERT INTO documents (
      doc_type, doc_no, doc_date, customer_id, due_date,
      subtotal, discount_amount, vat_rate, vat_amount, total, note
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?)
  ");
  $stmt->execute([
    'BILL', $billNo, $billDate, $customerId, null,
    $subtotal, $discount, $vatRate, $vatAmount, $total, $note
  ]);

  $billId = (int)$pdo->lastInsertId();

  // ------------------- เก็บ group_no ที่อยู่ใน BILL -------------------
  $ins = $pdo->prepare("INSERT INTO bill_groups (bill_id, group_no) VALUES (?,?)");
  foreach ($selected as $g) {
    $ins->execute([$billId, $g]); // ถ้ามี UNIQUE(group_no) อยู่แล้ว จะกันวางซ้ำโดยธรรมชาติ
  }

  $pdo->commit();

  // จะ redirect ไปดูบิลเลยก็ได้
  header("Location: view.php?id=" . $billId);
  exit;

} catch (Exception $e) {
  $pdo->rollBack();
  die("สร้าง BILL ไม่สำเร็จ: " . $e->getMessage());
}
