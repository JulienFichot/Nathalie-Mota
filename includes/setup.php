<?php
/**
 * includes/setup.php — Configuration du thème et chargement des assets
 * ═══════════════════════════════════════════════════════════════════════
 * OC Étape 1 — Points évalués dans ce fichier :
 *   ✓ register_nav_menus()   → le menu est gérable depuis WP Admin (pas codé en dur)
 *   ✓ wp_enqueue_style()     → le CSS est chargé via WordPress (pas en dur dans le HTML)
 *   ✓ wp_enqueue_script()    → les JS sont chargés via WordPress (pas en dur dans le HTML)
 *   ✓ wp_localize_script()   → passe l'URL AJAX et le nonce de sécurité au JavaScript
 */

// ── Fonctionnalités déclarées au thème ──────────────────────────────────────
function mota_theme_setup() {
  add_theme_support('title-tag');       // WordPress gère la balise <title>
  add_theme_support('post-thumbnails'); // Active les images à la une sur les posts

  // Enregistre le menu : apparaît dans WP Admin > Apparence > Menus
  // Le client Nathalie peut modifier les liens sans toucher au code.
  register_nav_menus(['main-menu' => 'Menu principal']);
}
add_action('after_setup_theme', 'mota_theme_setup');

// ── Chargement CSS et JavaScript (OC : obligatoire via wp_enqueue) ──────────
function mota_enqueue_assets() {
  $uri = get_template_directory_uri(); // URL de base du thème

  // CSS unique du thème (toutes les sections dans un seul fichier)
  wp_enqueue_style('mota-style', $uri . '/assets/css/style.css', [], '1.0');

  // JS chargés en pied de page (true) pour ne pas bloquer l'affichage (Green Code)
  wp_enqueue_script('mota-burger',     $uri . '/assets/js/burger.js',     [], '1.0', true); // Menu mobile
  wp_enqueue_script('mota-lightbox',   $uri . '/assets/js/lightbox.js',   [], '1.0', true); // OC Étape 5 : lightbox
  wp_enqueue_script('mota-contact',    $uri . '/assets/js/contact.js',    [], '1.0', true); // OC Étape 1 : modale contact
  wp_enqueue_script('mota-gallery',    $uri . '/assets/js/gallery.js',    [], '1.0', true); // OC Étape 4 : galerie AJAX
  wp_enqueue_script('mota-navigation', $uri . '/assets/js/navigation.js', [], '1.0', true); // OC Étape 3 : navigation single

  // Passe les variables PHP → JavaScript sans les écrire en dur dans le JS
  // ajaxUrl : adresse du point d'entrée AJAX de WordPress
  // nonce   : jeton de sécurité qui protège les requêtes AJAX contre les attaques CSRF
  wp_localize_script('mota-gallery', 'motaAjax', [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce'   => wp_create_nonce('mota_ajax'),
  ]);
}
add_action('wp_enqueue_scripts', 'mota_enqueue_assets');

// Supprime les styles Gutenberg inutilisés (Green Code : réduit le poids de la page)
add_action('wp_enqueue_scripts', function() {
  wp_dequeue_style('wp-block-library');
  wp_dequeue_style('wp-block-library-theme');
  wp_dequeue_style('global-styles');
}, 100);

// Ajoute thumbnail/titre/éditeur au CPT "photo" créé par CPT UI (OC Étape 2)
add_action('registered_post_type', function($post_type) {
  if ($post_type === 'photo') add_post_type_support($post_type, ['thumbnail', 'title', 'editor']);
}, 10, 2);
