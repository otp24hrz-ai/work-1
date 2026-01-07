<?php
require_once "../layout/header.php";

?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">เพิ่มลูกค้า</h3>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="index.php">← รายการลูกค้า</a>
      <a class="btn btn-outline-secondary" href="../documents/index.php">รายการเอกสาร</a>
    </div>
  </div>

  <form method="post" action="store.php" class="card">
    <div class="card-body">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">ชื่อลูกค้า / บริษัท <span class="text-danger">*</span></label>
          <input name="name" class="form-control" required placeholder="เช่น บจก. เอส.อาร์.ซี.เอ็นจิเนียริ่ง">
        </div>

        <div class="col-md-6">
          <label class="form-label">เลขผู้เสียภาษี</label>
          <input name="tax_id" class="form-control" placeholder="เช่น 01055xxxxxxx">
        </div>

        <div class="col-12">
          <label class="form-label">ที่อยู่</label>
          <textarea name="address" class="form-control" rows="3"
            placeholder="ที่อยู่สำหรับออกเอกสาร"></textarea>
        </div>

        <div class="col-md-6">
          <label class="form-label">โทร</label>
          <input name="phone" class="form-control" placeholder="เช่น 02-xxx-xxxx">
        </div>

        <div class="col-md-6">
          <label class="form-label">อีเมล</label>
          <input type="email" name="email" class="form-control" placeholder="example@email.com">
        </div>
      </div>

      <hr class="my-3">

      <div class="d-flex gap-2">
        <button class="btn btn-primary">บันทึก</button>
        <a class="btn btn-outline-secondary" href="index.php">ยกเลิก</a>
      </div>

    </div>
  </form>

<?php
require_once "../layout/footer.php";
?>
