<?php
// db.php

$host = "127.0.0.1";
$dbname = "work";
$user = "root";      // ปกติ XAMPP ใช้ root
$pass = "";          // XAMPP ไม่มีรหัสผ่าน

try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $user,
    $pass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
  );
} catch (PDOException $e) {
  die("❌ DB connection failed: " . $e->getMessage());
}
