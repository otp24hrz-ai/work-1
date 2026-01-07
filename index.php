<?php
// index.php - simple router

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ถ้าโปรเจกต์อยู่ในโฟลเดอร์ย่อย เช่น http://localhost/doc-system/...
// ให้ตัด base path ออก (แก้ให้ตรงกับของคุณ)
$base = '/doc-system/';
if (strpos($path, $base) === 0) {
  $path = substr($path, strlen($base));
}
$path = rtrim($path, '/');
if ($path === '') $path = '/';

$routes = [
  '/' => __DIR__ . '/documents/create.php',

  '/documents/create' => __DIR__ . '/documents/create.php',
  '/documents/save'   => __DIR__ . '/documents/save.php',
  '/documents/view'   => __DIR__ . '/documents/view.php',
  '/documents/pdf'    => __DIR__ . '/documents/pdf.php',
];

if (isset($routes[$path])) {
  require $routes[$path];
  exit;
}

http_response_code(404);
echo "404 Not Found";
