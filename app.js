// Los grupos se construirán dinámicamente desde el DOM; no se usan GIFs
const photoGroups = {};

// Elementos del lightbox
const lightbox = document.getElementById('lightbox');
const lightboxImg = lightbox.querySelector('.lightbox-image');
const lightboxName = lightbox.querySelector('.lightbox-name');
const lightboxCaption = lightbox.querySelector('.lightbox-caption');
const btnClose = lightbox.querySelector('.lightbox-close');
// Se eliminan botones de navegación

let currentGroup = null;
let currentIndex = 0;
let slideIndex = 0;
let slideshowTimer = null;

function openLightbox(groupNumber) {
  currentGroup = groupNumber;
  currentIndex = 0;
  slideIndex = 0;
  
  showCurrentItem();
  // iniciar autoplay del carrusel
  if (slideshowTimer){ clearInterval(slideshowTimer); }
  slideshowTimer = setInterval(() => {
    slideIndex = (slideIndex + 1) % 3;
    showCurrentItem();
  }, 2000);
  
  lightbox.classList.add('open');
  lightbox.setAttribute('aria-hidden', 'false');
  
  // Evitar scroll del body
  document.documentElement.style.overflow = 'hidden';
  btnClose.focus();
  
  // Sin reproducción automática
}

function showCurrentItem() {
  const group = photoGroups[currentGroup];
  const item = group[currentIndex];
  
  // Mostrar portada (captura nombre) fija y preparar slide actual
  if (lightboxName && (item.nameSrc || item.imageSrc || item.src)){
    const nameSrc = item.nameSrc || item.imageSrc || item.src;
    lightboxName.src = nameSrc;
    lightboxName.alt = (item.alt || '') + ' (nombre)';
  }
  // determinar arreglo de slides (3 capturas)
  const slides = item.slides || [item.imageSrc, item.src].filter(Boolean);
  const currentSlide = slides.length ? slides[slideIndex % slides.length] : (item.imageSrc || item.src || '');
  // transición suave
  lightboxImg.style.transition = 'opacity .5s ease';
  lightboxImg.style.opacity = '0';
  setTimeout(() => {
    lightboxImg.src = currentSlide;
    lightboxImg.alt = item.alt;
    lightboxImg.style.opacity = '1';
  }, 80);
  lightboxImg.alt = item.alt;
  const projectBy = item.proyectoDe || '';
  const course = item.curso || '';
  lightboxCaption.innerHTML = `Proyecto de: ${projectBy}<br>Curso: ${course}`;
}

function closeLightbox() {
  lightbox.classList.remove('open');
  lightbox.setAttribute('aria-hidden', 'true');
  document.documentElement.style.overflow = '';
  
  // Limpieza
  lightboxImg.removeAttribute('src');
  if (lightboxName){ lightboxName.removeAttribute('src'); }
  currentGroup = null;
  currentIndex = 0;
  slideIndex = 0;
  if (slideshowTimer){ clearInterval(slideshowTimer); slideshowTimer = null; }
}

// Sin navegación entre elementos; cada grupo tiene un único elemento

// Autoplay eliminado

// Eventos para las fotos principales
document.addEventListener('DOMContentLoaded', function() {
  const mainItems = document.querySelectorAll('.main-item');
  const backToTop = document.querySelector('.back-to-top');
  // (revertido) sin carrusel en encabezado
  
  mainItems.forEach((item) => {
    const groupNumber = parseInt(item.dataset.group);
    const img = item.querySelector('img');
    
    // Preferir fuentes provistas por data-* (desde index.php); si no existen, derivar por carpeta
    try{
      const src = img.getAttribute('src') || '';
      const dataName = img.getAttribute('data-name-src');
      const dataS1 = img.getAttribute('data-slide1');
      const dataS2 = img.getAttribute('data-slide2');
      const dataS3 = img.getAttribute('data-slide3');
      const group = photoGroups[groupNumber] || (photoGroups[groupNumber] = []);
      let nameSrc = dataName;
      let slides = [dataS1, dataS2, dataS3].filter(Boolean);

      if (!nameSrc || slides.length === 0) {
        // ejemplo: imagenes/pagina/Lo%20veo%20y%20lo%20quiero/captura%20nombre.png
        const folder = src.substring(0, src.lastIndexOf('/'));
        const base = folder + '/captura ';
        nameSrc = nameSrc || (folder + '/captura nombre.png');
        if (slides.length === 0) slides = [base + '1.png', base + '2.png', base + '3.png'];
      }
      // Leer metadatos personalizados desde atributos data-
      const proyectoAttr = img.getAttribute('data-proyecto') || item.getAttribute('data-proyecto') || '';
      const cursoAttr = img.getAttribute('data-curso') || item.getAttribute('data-curso') || '';
      const candidate = {
        nameSrc: nameSrc,
        slides: slides,
        alt: img.getAttribute('alt') || '',
        caption: img.getAttribute('data-caption') || img.getAttribute('alt') || '',
        proyectoDe: proyectoAttr,
        curso: cursoAttr
      };
      // Si el grupo no tiene item aún o viene con datos viejos, reemplazar por el derivado
      if (!group.length || !group[0].slides){
        photoGroups[groupNumber] = [candidate];
      }
    }catch(_e){}

    img.addEventListener('click', () => openLightbox(groupNumber));
    img.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openLightbox(groupNumber);
      }
    });
    img.tabIndex = 0; // accesible con teclado
  });

  // Mostrar botón al llegar cerca del final del documento
  function toggleBackToTopVisibility(){
    if (!backToTop) return;
    const scrolledToBottom = (window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 10);
    backToTop.classList.toggle('visible', scrolledToBottom);
  }
  toggleBackToTopVisibility();
  window.addEventListener('scroll', toggleBackToTopVisibility);

  // Scroll suave al inicio
  if (backToTop){
    backToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // fin revertido
});

btnClose.addEventListener('click', closeLightbox);

lightbox.addEventListener('click', (e) => {
  // cerrar al hacer click en el fondo oscuro
  if (e.target === lightbox) closeLightbox();
});

// Navegación por teclado
window.addEventListener('keydown', (e) => {
  if (!lightbox.classList.contains('open')) return;

  if (e.key === 'Escape') closeLightbox();
});