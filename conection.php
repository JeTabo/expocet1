<?php
// Archivo de compatibilidad para establecer la conexión a la base de datos
// Uso: require_once __DIR__ . '/conection.php';  // obtendrás $pdo listo para usar

require_once __DIR__ . '/config.php';

try {
  $pdo = db_connect();
} catch (Throwable $e) {
  // Evita mostrar credenciales en pantalla
  http_response_code(500);
  die('Error de conexión a la base de datos.');
}

?>


