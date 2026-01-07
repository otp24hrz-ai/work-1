<?php
require_once "../layout/header.php";

/**
 * Generate running document number: TYPE + YYMM + /NNN
 * Example: QUO2601/001
 */
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

/**
 * Clean items + compute totals
 */
function prepareItemsAndTotals(array $items): array {
  $subtotal = 0.0;
  $cleanItems = [];

  foreach ($items as $it) {
    $name = trim($it['name'] ?? '');
    $qty  = (float)($it['qty'] ?? 0);
    $unit = trim($it['unit'] ?? 'ชิ้น');
    $price = (float)($it['price'] ?? 0);

    if ($name === '' || $qty <= 0) continue;

    $line = round($qty * $price, 2);
    $subtotal += $line;

    $cleanItems[] = [
      'name' => $name,
      'qty' => $qty,
      'unit' => $unit === '' ? 'ชิ้น' : $unit,
      'price' => $price,
      'line' => $line
    ];
  }

  if (count($cleanItems) === 0) {
    die("รายการไม่ถูกต้อง");
  }

  $discount = 0.00; // ปรับเพิ่มทีหลังได้
  $vatRate = 7.00;
  $vatAmount = round(($subtotal - $discount) * ($vatRate / 100), 2);
  $total = round(($subtotal - $discount) + $vatAmount, 2);

  return [$cleanItems, $subtotal, $discount, $vatRate, $vatAmount, $total];
}


function genGroupNo(PDO $pdo, string $docDate): string {
  $y = (int)substr($docDate, 0, 4);
  $m = (int)substr($docDate, 5, 2);

  $yy = $y % 100; // ค.ศ. 2 หลัก
  $yymm = str_pad((string)$yy, 2, '0', STR_PAD_LEFT)
        . str_pad((string)$m, 2, '0', STR_PAD_LEFT);

  // group_no รูปแบบ: YYMM/NNN เช่น 2601/001
  $stmt = $pdo->prepare("
    SELECT group_no
    FROM documents
    WHERE group_no LIKE ?
    ORDER BY group_no DESC
    LIMIT 1
  ");
  $stmt->execute([$yymm . "/%"]);
  $last = $stmt->fetchColumn();

  $next = 1;
  if ($last) {
    $parts = explode('/', $last);
    if (count($parts) === 2 && ctype_digit($parts[1])) {
      $next = (int)$parts[1] + 1;
    }
  }

  return $yymm . "/" . str_pad((string)$next, 3, '0', STR_PAD_LEFT);
}


function createDocument(
  PDO $pdo,
  string $docType,
  string $docDate,
  ?string $dueDate,
  int $customerId,
  string $note,
  array $cleanItems,
  float $subtotal,
  float $discount,
  float $vatRate,
  float $vatAmount,
  float $total,
  string $groupNo,           // << เลขชุด เช่น 2601/001
  ?int $sourceDocId = null   // << ใบที่สร้างมาจากอะไร
): int {

  // doc_no เป็น TYPE + group_no เช่น QUO2601/001
  $docNo = $docType . $groupNo;

  $stmt = $pdo->prepare("
    INSERT INTO documents(
      doc_type, doc_role, group_no, source_doc_id,
      doc_no, doc_date, customer_id, due_date,
      subtotal, discount_amount, vat_rate, vat_amount, total, note
    )
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)
  ");
  $stmt->execute([
    $docType, $docType, $groupNo, $sourceDocId,
    $docNo, $docDate, $customerId, $dueDate,
    $subtotal, $discount, $vatRate, $vatAmount, $total, $note
  ]);

  $docId = (int)$pdo->lastInsertId();

  $stmtItem = $pdo->prepare("
    INSERT INTO document_items(document_id, item_name, qty, unit, unit_price, line_total)
    VALUES(?,?,?,?,?,?)
  ");
  foreach ($cleanItems as $it) {
    $stmtItem->execute([$docId, $it['name'], $it['qty'], $it['unit'], $it['price'], $it['line']]);
  }

  return $docId;
}


// ================== รับค่า POST ==================
$mode = $_POST['doc_mode'] ?? 'SINGLE'; // FULL หรือ SINGLE (ถ้าไม่ได้ส่งมา จะเป็น SINGLE)
$docType = $_POST['doc_type'] ?? 'QUO';

$docDate = $_POST['doc_date'] ?? '';
$dueDate = ($_POST['due_date'] ?? '') ?: null;
$customerId = (int)($_POST['customer_id'] ?? 0);
$note = trim($_POST['note'] ?? '');

$items = $_POST['items'] ?? [];

// ตรวจหัวเอกสาร
if ($docDate === '' || $customerId <= 0) {
  die("ข้อมูลหัวเอกสารไม่ครบ");
}
if (!is_array($items) || count($items) === 0) {
  die("กรุณาเพิ่มอย่างน้อย 1 รายการ");
}

// เตรียมรายการ + ยอดรวม (ใช้ร่วมกันทุกใบ)
[$cleanItems, $subtotal, $discount, $vatRate, $vatAmount, $total] = prepareItemsAndTotals($items);

// ================== บันทึก ==================
$pdo->beginTransaction();

try {
  if ($mode === 'FULL') {
    // ครบชุด: QUO -> TAX -> REC
    // ไม่ใช้ doc_no จากฟอร์ม เพื่อไม่ชนกัน และเพื่อให้เลขของแต่ละชนิดถูกต้อง
	$groupNo = genGroupNo($pdo, $docDate);

	$quoId = createDocument($pdo, 'QUO', $docDate, $dueDate, $customerId, $note, $cleanItems,
	  $subtotal, $discount, $vatRate, $vatAmount, $total,
	  $groupNo, null);

	$taxId = createDocument($pdo, 'TAX', $docDate, null, $customerId, $note, $cleanItems,
	  $subtotal, $discount, $vatRate, $vatAmount, $total,
	  $groupNo, $quoId);

	$recId = createDocument($pdo, 'REC', $docDate, null, $customerId, $note, $cleanItems,
	  $subtotal, $discount, $vatRate, $vatAmount, $total,
	  $groupNo, $taxId);

    $pdo->commit();

    // ส่งไปดูใบเสนอเป็นหลัก
    header("Location: view.php?id=" . $quoId);
    exit;

  } else {
    // SINGLE: ออกใบเดียวตาม doc_type
    if (!in_array($docType, ['QUO','BILL','TAX','REC'], true)) {
      die("ประเภทเอกสารไม่ถูกต้อง");
    }

	// ใช้เลข doc_no จากฟอร์มได้ (ถ้ามี) ไม่งั้น gen ใหม่
	$forcedDocNo = trim($_POST['doc_no'] ?? '');

	$docId = createDocument($pdo, $docType, $docDate, $dueDate, $customerId, $note, $cleanItems, $subtotal, $discount, $vatRate, $vatAmount, $total, $forcedDocNo);


    $pdo->commit();

    header("Location: view.php?id=" . $docId);
    exit;
  }

} catch (Exception $e) {
  $pdo->rollBack();
  die("บันทึกไม่สำเร็จ: " . $e->getMessage());
}
