<?php
require_once "../layout/header.php";

// ===== helper: ยอดของ group (TAX ก่อน ถ้าไม่มี TAX ใช้ QUO) =====
function groupAmount(PDO $pdo, string $groupNo): float {
  $st = $pdo->prepare("
    SELECT total
    FROM documents
    WHERE group_no=? AND doc_type IN ('TAX','QUO')
    ORDER BY FIELD(doc_type,'TAX','QUO'), id ASC
    LIMIT 1
  ");
  $st->execute([$groupNo]);
  return (float)($st->fetchColumn() ?: 0);
}

// ===== helper: รวมยอดใหม่ของ BILL ตามกลุ่มใน bill_groups =====
function recalcBill(PDO $pdo, int $billId): void {
  // รวมยอดของทุก group_no ที่อยู่ใน bill นี้
  $st = $pdo->prepare("SELECT group_no FROM bill_groups WHERE bill_id=?");
  $st->execute([$billId]);
  $groups = $st->fetchAll(PDO::FETCH_COLUMN);

  $sum = 0.0;
  foreach ($groups as $g) {
    $sum += groupAmount($pdo, $g);
  }

  // ใบวางบิลไม่คิด VAT ซ้ำ
  $stmt = $pdo->prepare("
    UPDATE documents
    SET subtotal=?, discount_amount=0, vat_rate=0, vat_amount=0, total=?
    WHERE id=? AND doc_type='BILL'
  ");
  $stmt->execute([$sum, $sum, $billId]);
}

// ===== input =====
$action = $_POST['action'] ?? '';
$billId = (int)($_POST['bill_id'] ?? 0);
$groups = $_POST['group_no'] ?? [];

if (!is_array($groups)) $groups = [];
$groups = array_values(array_unique(array_filter(array_map('trim', $groups), fn($x)=>$x!=='')));

if ($action === 'assign') {
  if ($billId <= 0) die("กรุณาเลือก BILL");
  if (count($groups) === 0) die("กรุณาเลือกอย่างน้อย 1 กลุ่ม");

  $pdo->beginTransaction();
  try {
    // ตรวจว่า billId เป็น BILL จริง
    $st = $pdo->prepare("SELECT id, customer_id FROM documents WHERE id=? AND doc_type='BILL' LIMIT 1");
    $st->execute([$billId]);
    $bill = $st->fetch();
    if (!$bill) die("BILL ไม่ถูกต้อง");

    // บังคับลูกค้าคนเดียวกัน: group ต้องเป็น customer_id เดียวกับ bill
    $billCustomer = (int)$bill['customer_id'];

    // เตรียม statement
    $ins = $pdo->prepare("INSERT INTO bill_groups(bill_id, group_no) VALUES(?,?)");
    $upd = $pdo->prepare("UPDATE bill_groups SET bill_id=? WHERE group_no=?");
    $sel = $pdo->prepare("SELECT bill_id FROM bill_groups WHERE group_no=? LIMIT 1");
    $docCustomer = $pdo->prepare("
      SELECT customer_id
      FROM documents
      WHERE group_no=? AND doc_type IN ('TAX','QUO')
      ORDER BY FIELD(doc_type,'TAX','QUO'), id ASC
      LIMIT 1
    ");

    $touchedBills = [$billId => true];

    foreach ($groups as $g) {
      // ตรวจ customer ของ group
      $docCustomer->execute([$g]);
      $gCustomer = (int)($docCustomer->fetchColumn() ?: 0);
      if ($gCustomer !== $billCustomer) {
        throw new Exception("กลุ่ม {$g} เป็นคนละลูกค้ากับ BILL นี้");
      }

      // ถ้า group เคยอยู่บิลอื่น -> update และเก็บ bill เก่าไว้ recalculation
      $sel->execute([$g]);
      $oldBillId = (int)($sel->fetchColumn() ?: 0);

      if ($oldBillId > 0) {
        if ($oldBillId !== $billId) {
          $upd->execute([$billId, $g]);
          $touchedBills[$oldBillId] = true;
        }
      } else {
        $ins->execute([$billId, $g]);
      }
    }

    // recalculation: bill ใหม่ + bill เก่าที่โดนย้ายออก
    foreach (array_keys($touchedBills) as $bid) {
      recalcBill($pdo, (int)$bid);
    }

    $pdo->commit();
    header("Location: index.php?msg=" . urlencode("ย้ายเข้า BILL สำเร็จ"));
    exit;

  } catch (Exception $e) {
    $pdo->rollBack();
    die("ย้ายเข้า BILL ไม่สำเร็จ: " . $e->getMessage());
  }
}

if ($action === 'remove') {
  if (count($groups) === 0) die("กรุณาเลือกอย่างน้อย 1 กลุ่ม");

  $pdo->beginTransaction();
  try {
    // หา bill_id ที่กลุ่มพวกนี้อยู่ (เพื่อ recalculation)
    $sel = $pdo->prepare("SELECT bill_id FROM bill_groups WHERE group_no=? LIMIT 1");
    $del = $pdo->prepare("DELETE FROM bill_groups WHERE group_no=?");

    $touchedBills = [];

    foreach ($groups as $g) {
      $sel->execute([$g]);
      $oldBillId = (int)($sel->fetchColumn() ?: 0);
      if ($oldBillId > 0) {
        $touchedBills[$oldBillId] = true;
        $del->execute([$g]);
      }
    }

    foreach (array_keys($touchedBills) as $bid) {
      recalcBill($pdo, (int)$bid);
    }

    $pdo->commit();
    header("Location: index.php?msg=" . urlencode("ย้ายออกจาก BILL สำเร็จ"));
    exit;

  } catch (Exception $e) {
    $pdo->rollBack();
    die("ย้ายออกจาก BILL ไม่สำเร็จ: " . $e->getMessage());
  }
}

die("Invalid action");
