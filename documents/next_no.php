<?php
require_once "../db.php";
header('Content-Type: application/json; charset=utf-8');

$docType = $_GET['doc_type'] ?? 'QUO';
$docDate = $_GET['doc_date'] ?? date('Y-m-d');

if (!preg_match('/^(QUO|BILL|REC|TAX)$/', $docType)) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid doc_type']);
  exit;
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $docDate)) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid doc_date']);
  exit;
}

$y = (int)substr($docDate, 0, 4);
$m = (int)substr($docDate, 5, 2);

// ใช้ ค.ศ. 2 หลัก เช่น 2026 → 26
$yy = $y % 100;
$yymm = str_pad((string)$yy, 2, '0', STR_PAD_LEFT)
      . str_pad((string)$m, 2, '0', STR_PAD_LEFT);


$prefix = $docType . $yymm; // เช่น QUO6604

// หาเลขล่าสุดของ prefix นี้
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

$docNo = $prefix . "/" . str_pad((string)$next, 3, '0', STR_PAD_LEFT);

echo json_encode([
  'doc_no' => $docNo,
  'prefix' => $prefix,
  'next_running' => $next
], JSON_UNESCAPED_UNICODE);
