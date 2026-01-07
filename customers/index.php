<?php
require_once "../layout/header.php";

$customers = $pdo->query("SELECT * FROM customers ORDER BY id DESC")->fetchAll();
?>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">ลูกค้า</h3>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="../documents/index.php">รายการเอกสาร</a>
      <a class="btn btn-primary" href="create.php">+ เพิ่มลูกค้า</a>
    </div>
  </div>

  <table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
      <tr>
        <th style="width:70px;">#</th>
        <th>ชื่อ</th>
        <th style="width:160px;">เลขผู้เสียภาษี</th>
        <th style="width:140px;">โทร</th>
        <th>อีเมล</th>
        <th style="width:120px;" class="text-center">จัดการ</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($customers as $c): ?>
        <tr>
          <td><?= (int)$c['id'] ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($c['name']) ?></td>
          <td><?= htmlspecialchars($c['tax_id'] ?? '') ?></td>
          <td><?= htmlspecialchars($c['phone'] ?? '') ?></td>
          <td><?= htmlspecialchars($c['email'] ?? '') ?></td>
          <td class="text-center">
            <a class="btn btn-sm btn-outline-secondary"
               href="edit.php?id=<?= (int)$c['id'] ?>">
              ✏️ แก้ไข
            </a>
          </td>
        </tr>
      <?php endforeach; ?>

      <?php if (count($customers) === 0): ?>
        <tr>
          <td colspan="6" class="text-center text-muted">ยังไม่มีข้อมูลลูกค้า</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <a href="../documents/create.php" class="btn btn-outline-primary">+ ออกเอกสาร</a>

<?php
require_once "../layout/footer.php";
?>