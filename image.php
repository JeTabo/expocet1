<?php
require_once __DIR__ . '/config.php';

// Endpoint: image.php?id=123&field=logo|captura1|captura2|captura3

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$field = isset($_GET['field']) ? $_GET['field'] : '';

$allowed = ['logo','captura1','captura2','captura3'];
if ($id <= 0 || !in_array($field, $allowed, true)) {
  http_response_code(400);
  echo 'Bad request';
  exit;
}

try {
  $pdo = db_connect();
  $sql = "SELECT {$field} AS blobdata FROM " . SITES_TABLE . " WHERE idsitio = :id LIMIT 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':id' => $id]);
  $row = $stmt->fetch();
  if (!$row || $row['blobdata'] === null) {
    http_response_code(404);
    echo 'Not found';
    exit;
  }

  $data = $row['blobdata'];
  // Detección simple de tipo de imagen (png/jpeg/gif/webp) por cabecera
  $mime = 'application/octet-stream';
  if (strncmp($data, "\x89PNG\r\n\x1a\n", 8) === 0) { $mime = 'image/png'; }
  elseif (strncmp($data, "\xff\xd8\xff", 3) === 0) { $mime = 'image/jpeg'; }
  elseif (substr($data, 0, 6) === 'GIF87a' || substr($data, 0, 6) === 'GIF89a') { $mime = 'image/gif'; }
  elseif (substr($data, 0, 4) === 'RIFF' && substr($data, 8, 4) === 'WEBP') { $mime = 'image/webp'; }

  header('Content-Type: ' . $mime);
  header('Cache-Control: public, max-age=31536000, immutable');
  echo $data;
} catch (Throwable $e) {
  http_response_code(500);
  echo 'Server error';
}

?>


