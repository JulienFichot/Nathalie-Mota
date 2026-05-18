/**
 * burger.js — Gestion du menu burger (navigation mobile)
 *
 * Ce script gère l'ouverture et la fermeture du menu de navigation
 * sur mobile via le bouton burger (☰ → ✕).
 *
 * Comportement :
 * - Clic sur le burger → bascule la classe .nav-open sur <body>
 * - La classe .nav-open est utilisée en CSS pour faire glisser le menu
 * - L'attribut aria-expanded est mis à jour pour l'accessibilité
 * - Les barres (☰) et la croix (✕) sont affichées/masquées via JS
 *   (plus fiable que CSS seul dans ce contexte)
 */
document.addEventListener('DOMContentLoaded', () => {

  const burger     = document.querySelector('.burger');
  const burgerBars = burger?.querySelectorAll('.burger-bar'); // les trois traits
  const burgerX    = burger?.querySelector('.burger-close');  // la croix ✕

  if (!burger) return;

  burger.addEventListener('click', () => {
    // Bascule .nav-open sur le body (utilisé en CSS pour afficher le menu)
    const isOpen = document.body.classList.toggle('nav-open');

    // Accessibilité : indique si le menu est ouvert
    burger.setAttribute('aria-expanded', String(isOpen));

    // Affiche soit les barres (menu fermé) soit la croix (menu ouvert)
    burgerBars?.forEach(b => { b.style.display = isOpen ? 'none' : ''; });
    if (burgerX) burgerX.style.display = isOpen ? 'block' : 'none';
  });

});
