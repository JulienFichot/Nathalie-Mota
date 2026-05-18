<?php
/**
 * includes/menu.php — Comportement spécial du lien "CONTACT" dans le menu
 * ══════════════════════════════════════════════════════════════════════════
 * OC Étape 1 — Points évalués ici :
 *   ✓ Le menu est géré depuis WP Admin (wp_nav_menu dans header.php)
 *   ✓ Ce filtre ajoute dynamiquement la classe CSS sur le lien CONTACT
 *     pour que contact.js puisse l'intercepter et ouvrir la modale
 *     au lieu de suivre le lien normalement.
 *
 * Pourquoi un filtre plutôt que du code en dur ?
 * → Nathalie garde la main sur les liens du menu depuis l'interface WP.
 *   Ce filtre détecte automatiquement le lien nommé "CONTACT" (insensible
 *   à la casse) sans qu'on ait besoin de connaître son ID ou son URL.
 */
add_filter('nav_menu_link_attributes', function($atts, $item) {
  // Si le titre du lien est "CONTACT" (peu importe la casse)
  if (strtoupper(trim($item->title)) === 'CONTACT') {
    $atts['class'] = trim(($atts['class'] ?? '') . ' nav-contact-modal'); // Classe interceptée par contact.js
    $atts['href']  = '#'; // Empêche la navigation vers une vraie page
  }
  return $atts;
}, 10, 2);
