/**
 * lightbox.js — Visionneuse plein écran (lightbox)
 *
 * Ouvre une photo en grand format via un overlay plein écran.
 * Permet de naviguer entre les photos avec les boutons ← → ou
 * les touches clavier ArrowLeft / ArrowRight.
 * Se ferme au clic sur l'overlay (hors image) ou avec Échap.
 *
 * Éléments HTML attendus (dans footer.php) :
 *   #lightbox-overlay  — overlay plein écran
 *   #lightbox-img      — balise <img> de la photo affichée
 *   #lightbox-title    — titre de la photo
 *   #lightbox-ref      — référence de la photo
 *   .lightbox-close    — bouton ✕
 *   .lightbox-prev     — bouton ← précédente
 *   .lightbox-next     — bouton → suivante
 *
 * Déclencheur : tout bouton avec la classe .lightbox-open
 *   data-src   = URL de l'image
 *   data-title = titre
 *   data-ref   = référence
 */
document.addEventListener('DOMContentLoaded', () => {

  // Références aux éléments de l'overlay
  const overlay  = document.getElementById('lightbox-overlay');
  const lbImg    = document.getElementById('lightbox-img');
  const lbTitle  = document.getElementById('lightbox-title');
  const lbRef    = document.getElementById('lightbox-ref');
  const btnClose = overlay?.querySelector('.lightbox-close');
  const btnPrev  = overlay?.querySelector('.lightbox-prev');
  const btnNext  = overlay?.querySelector('.lightbox-next');

  if (!overlay) return; // la lightbox n'est pas dans la page

  // Liste des photos chargées, reconstruite à chaque ouverture
  let lightboxItems = [];
  let currentIndex  = 0;

  /**
   * Construit la liste des photos disponibles à partir de tous les
   * boutons .lightbox-open présents dans le DOM.
   * Dédoublonne par URL (Set) pour éviter les doublons.
   */
  function rebuildLightboxItems() {
    const seen = new Set();
    lightboxItems = Array.from(document.querySelectorAll('.lightbox-open'))
      .map(btn => ({
        src:   btn.getAttribute('data-src')   || '',
        title: btn.getAttribute('data-title') || '',
        ref:   btn.getAttribute('data-ref')   || '',
      }))
      .filter(x => {
        if (!x.src || seen.has(x.src)) return false;
        seen.add(x.src);
        return true;
      });
  }

  /**
   * Affiche la photo à l'index donné (avec boucle infinie).
   */
  function renderLightbox(index) {
    if (!lbImg || !lightboxItems.length) return;
    // Boucle infinie : si on dépasse le dernier, on revient au premier
    currentIndex = (index + lightboxItems.length) % lightboxItems.length;
    const item = lightboxItems[currentIndex];
    lbImg.src = item.src;
    lbImg.alt = item.title;
    if (lbTitle) lbTitle.textContent = item.title;
    if (lbRef)   lbRef.textContent   = item.ref ? `REF. ${item.ref}` : '';
  }

  /**
   * Ouvre la lightbox sur la photo à l'index donné.
   */
  function openLightboxAt(index) {
    rebuildLightboxItems();
    renderLightbox(index);
    overlay.classList.add('is-open');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden'; // empêche le scroll du fond
  }

  /**
   * Ferme la lightbox et réinitialise l'image.
   */
  function closeLightbox() {
    overlay.classList.remove('is-open');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (lbImg) lbImg.src = ''; // libère la mémoire
  }

  // --- Événements ---

  // Délégation : capture tous les clics sur .lightbox-open (y compris chargés en AJAX)
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.lightbox-open');
    if (!btn) return;
    e.preventDefault();
    rebuildLightboxItems();
    // Retrouve l'index de la photo cliquée dans la liste
    const index = lightboxItems.findIndex(item => item.src === (btn.getAttribute('data-src') || ''));
    openLightboxAt(index >= 0 ? index : 0);
  });

  // Fermeture au clic sur l'overlay (mais pas sur l'image ni les boutons)
  overlay.addEventListener('click', (e) => {
    if (e.target !== lbImg && !e.target.closest('button')) closeLightbox();
  });

  btnClose?.addEventListener('click', closeLightbox);
  btnPrev?.addEventListener('click', () => renderLightbox(currentIndex - 1));
  btnNext?.addEventListener('click', () => renderLightbox(currentIndex + 1));

  // Clavier : Échap = fermer, flèches = naviguer
  document.addEventListener('keydown', (e) => {
    if (!overlay.classList.contains('is-open')) return;
    if (e.key === 'Escape')     closeLightbox();
    if (e.key === 'ArrowLeft')  renderLightbox(currentIndex - 1);
    if (e.key === 'ArrowRight') renderLightbox(currentIndex + 1);
  });

  // Expose la fonction closeLightbox pour contact.js (gestion Échap partagée)
  window.motaCloseLightbox = closeLightbox;
  window.motaLightboxIsOpen = () => overlay.classList.contains('is-open');

});
