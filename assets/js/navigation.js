/**
 * navigation.js — Navigation AJAX entre photos (page single-photo)
 *
 * Ce script ne s'active que sur les pages de type single-photo
 * (détectées par la présence de main.single-photo dans le DOM).
 *
 * Fonctionnalités :
 *
 * 1. NAVIGATION AJAX (liens ← PRÉCÉDENTE / SUIVANTE →)
 *    Au clic sur un lien .nav-under, charge la page suivante/précédente
 *    en AJAX et remplace le contenu de <main.single-photo> sans rechargement.
 *    - Affiche une transition d'opacité pendant le chargement
 *    - Met à jour l'URL et le titre de la page via l'History API
 *    - Relie les boutons CONTACT sur le nouveau contenu
 *    - Fallback : redirige normalement si le fetch échoue
 *
 * 2. NAVIGATION TACTILE (swipe mobile)
 *    Sur écran tactile, un glissement horizontal d'au moins 60px
 *    déclenche la navigation vers la photo suivante ou précédente.
 *    - Swipe gauche → suivante
 *    - Swipe droite → précédente
 *
 * Requiert :
 *   window.motaBindContactButtons (exposé par contact.js)
 */
document.addEventListener('DOMContentLoaded', () => {

  // Ne s'exécute que sur la page single-photo
  if (!document.querySelector('main.single-photo')) return;

  // ── Navigation AJAX ───────────────────────────────────────────────────────

  /**
   * Délégation : capte tous les clics sur les liens .nav-under
   * (qui peuvent être rechargés en AJAX après navigation).
   */
  document.addEventListener('click', async (e) => {
    const link = e.target.closest('.nav-under');

    // Ignore les liens désactivés (pas de photo précédente/suivante)
    if (!link || link.classList.contains('disabled')) return;
    e.preventDefault();

    const url  = link.href;
    const main = document.querySelector('main.single-photo');
    if (!main) return;

    // Indication visuelle de chargement
    main.style.opacity    = '0.4';
    main.style.transition = 'opacity .2s';

    try {
      // Récupère le HTML de la page cible
      const html    = await fetch(url).then(r => r.text());
      const doc     = new DOMParser().parseFromString(html, 'text/html');
      const newMain = doc.querySelector('main.single-photo');

      if (newMain) {
        // Remplace le contenu sans rechargement complet
        document.querySelector('main.single-photo').replaceWith(newMain);

        // Met à jour l'URL et le titre dans le navigateur
        history.pushState({}, '', url);
        document.title = doc.title;

        // Relie les boutons CONTACT sur le nouveau contenu
        window.motaBindContactButtons?.(document);

        // Remonte en haut de page en douceur
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        // Fallback : redirection classique si le sélecteur est introuvable
        window.location.href = url;
      }
    } catch {
      // Fallback en cas d'erreur réseau
      window.location.href = url;
    }
  });

  // Rechargement de la page au retour arrière/avant du navigateur
  window.addEventListener('popstate', () => window.location.reload());

  // ── Navigation tactile (swipe) ────────────────────────────────────────────

  let touchStartX = 0;

  // Enregistre la position de départ du doigt
  document.addEventListener('touchstart', (e) => {
    touchStartX = e.touches[0].clientX;
  }, { passive: true });

  // Calcule la distance parcourue et déclenche la navigation si ≥ 60px
  document.addEventListener('touchend', (e) => {
    const dx = e.changedTouches[0].clientX - touchStartX;
    if (Math.abs(dx) < 60) return; // trop court pour être un swipe intentionnel

    // Swipe gauche → suivante, swipe droite → précédente
    const selector = dx < 0
      ? '.nav-link-group--next .nav-under:not(.disabled)'
      : '.nav-link-group--prev .nav-under:not(.disabled)';

    document.querySelector(selector)?.click();
  }, { passive: true });

});
