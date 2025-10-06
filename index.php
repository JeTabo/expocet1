<?php
require_once __DIR__ . '/config.php';

// Consulta de sitios
try {
  $pdo = db_connect();
  // Estructura con mejoras: + titulo (VARCHAR), + orden (INT)
  $stmt = $pdo->query('SELECT idsitio, titulo, creador, curso, enlace, orden FROM ' . SITES_TABLE . ' ORDER BY (orden IS NULL), orden ASC, idsitio ASC');
  $sites = $stmt->fetchAll();
} catch (Throwable $e) {
  http_response_code(500);
  echo '<h1>Error de servidor</h1><p>No se pudo conectar a la base de datos.</p>';
  exit;
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>EXPOCET1 – El fruto de nuestro esfuerzo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header class="hero" role="banner">
    <div class="brand">
      <img src="image.php?id=0&amp;field=logo" alt="Logo CET 1" class="logo-image" onerror="this.src='imagenes/LogoCet1.png'" />
      <span class="brand-name">EXPOCET1</span>
    </div>

    <div class="hero-content">
      <h1 class="hero-title">El fruto de nuestro esfuerzo</h1>
      <p class="hero-tagline">Explora nuestro trabajo</p>
    </div>
    </header>
    <div class="divider" aria-hidden="true"></div>

    <main id="paginas" class="section">
      <div class="section-heading">
        <h2>Páginas realizadas por los estudiantes</h2>
      </div>

      <section class="gallery" aria-label="Galería de trabajos">
        <?php foreach ($sites as $i => $site): ?>
          <?php
            $id = (int)$site['idsitio'];
            $titulo = trim((string)($site['titulo'] ?? ''));
            if ($titulo === '') { $titulo = 'Sitio #'.$id; }
            $nameImg = 'image.php?id='.$id.'&field=captura1'; // portada/nombre si la guardas en captura1 o logo
            $slide1 = 'image.php?id='.$id.'&field=captura1';
            $slide2 = 'image.php?id='.$id.'&field=captura2';
            $slide3 = 'image.php?id='.$id.'&field=captura3';
            $enlace = htmlspecialchars($site['enlace'] ?? '#', ENT_QUOTES, 'UTF-8');
            $creador = htmlspecialchars($site['creador'] ?? '', ENT_QUOTES, 'UTF-8');
            $curso = htmlspecialchars((string)($site['curso'] ?? ''), ENT_QUOTES, 'UTF-8');
          ?>
          <figure class="gallery-item main-item" data-group="<?php echo $i+1; ?>">
            <img
              src="<?php echo $nameImg; ?>"
              alt="<?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>"
              data-caption="<?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>"
              data-proyecto="<?php echo $creador; ?>"
              data-curso="<?php echo $curso; ?>"
              data-name-src="<?php echo $nameImg; ?>"
              data-slide1="<?php echo $slide1; ?>"
              data-slide2="<?php echo $slide2; ?>"
              data-slide3="<?php echo $slide3; ?>"
            />
            <figcaption>
              <span><?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></span>
              <a href="<?php echo $enlace; ?>" class="item-link" target="_blank" rel="noopener">Ver más</a>
            </figcaption>
          </figure>
        <?php endforeach; ?>
      </section>
    </main>

    <div id="lightbox" class="lightbox" aria-hidden="true" aria-label="Visor de imágenes" role="dialog">
      <button class="lightbox-close" aria-label="Cerrar (Esc)">×</button>
      <img class="lightbox-name" alt="Nombre del proyecto" />
      <img class="lightbox-image" alt="Imagen ampliada" />
      <div class="lightbox-caption" aria-live="polite"></div>
    </div>

    <footer class="site-footer" role="contentinfo">
      <div class="footer-inner">
        <p class="footer-brand">EXPOCET1 – El fruto de nuestro esfuerzo</p>
        <div class="footer-left">
          <p class="f1">Proyecto de: Jeremías Taboada.</p>
          <p class="f1">Curso: 4º1 C.S 2025</p>
          <p class="f1">Profesor/a: Silvana Vargas</p>
        </div>
        <div class="footer-right" aria-label="Contacto y redes sociales">
          <h4 class="footer-title">Contacto</h4>
          <ul class="contact-list">
            <li><a href="mailto:contacto@cet1.edu" class="contact-link">contacto@cet1.edu</a></li>
            <li><a href="tel:+5400000000" class="contact-link">+54 00 0000-0000</a></li>
            <li><a href="#" class="contact-link">Dirección de la escuela</a></li>
          </ul>
          <h4 class="footer-title">Redes sociales</h4>
          <div class="social-links">
            <a href="#" aria-label="Facebook" class="social-link">Facebook</a>
            <a href="#" aria-label="Instagram" class="social-link">Instagram</a>
          </div>
        </div>
        <button class="back-to-top" aria-label="Volver al inicio" title="Volver al inicio">↑</button>
        <p class="footer-meta">© 2025 CET 1 • Todos los derechos reservados</p>
      </div>
    </footer>
    <script src="app.js" defer></script>
  </body>
</html>


