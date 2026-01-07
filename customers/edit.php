<?php
require_once "../layout/header.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid id");

$st = $pdo->prepare("SELECT * FROM customers WHERE id=?");
$st->execute([$id]);
$c = $st->fetch();
if (!$c) die("ไม่พบลูกค้า");
?>


  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">แก้ไขลูกค้า</h3>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="index.php">← รายการลูกค้า</a>
      <a class="btn btn-outline-secondary" href="../documents/index.php">รายการเอกสาร</a>
    </div>
  </div>

  <form method="post" action="update.php" class="card">
    <div class="card-body">
      <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">ชื่อลูกค้า</label>
          <input name="name" class="form-control" required
                 value="<?= htmlspecialchars($c['name'] ?? '') ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">เลขผู้เสียภาษี</label>
          <input name="tax_id" class="form-control"
                 value="<?= htmlspecialchars($c['tax_id'] ?? '') ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">โทร</label>
          <input name="phone" class="form-control"
                 value="<?= htmlspecialchars($c['phone'] ?? '') ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">อีเมล</label>
          <input type="email" name="email" class="form-control"
                 value="<?= htmlspecialchars($c['email'] ?? '') ?>">
        </div>

        <div class="col-12">
          <label class="form-label">ที่อยู่</label>
          <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($c['address'] ?? '') ?></textarea>
        </div>
      </div>

      <hr class="my-3">

      <div class="d-flex gap-2">
        <button class="btn btn-primary">บันทึกการแก้ไข</button>
        <a class="btn btn-outline-secondary" href="index.php">ยกเลิก</a>
      </div>
    </div>
  </form>

<?php
require_once "../layout/footer.php";
?>