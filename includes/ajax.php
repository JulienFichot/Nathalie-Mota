<?php
/**
 * includes/ajax.php — Gestionnaires AJAX de la galerie
 * ══════════════════════════════════════════════════════
 * OC Étape 4 — Points évalués ici :
 *   ✓ mota_get_filters  : les selects sont remplis dynamiquement depuis les taxonomies
 *                         (OC : "les données qui alimentent les selects doivent être
 *                          chargées dynamiquement depuis les taxonomies")
 *   ✓ mota_load_photos  : pagination infinie + filtres/tri via l'API AJAX de WordPress
 *   ✓ check_ajax_referer: sécurité (vérification du nonce sur chaque requête)
 *   ✓ sanitize_text_field: toutes les données POST sont nettoyées avant usage
 *
 * Ces actions répondent aux requêtes fetch() de gallery.js.
 * Elles sont accessibles aux visiteurs non connectés (wp_ajax_nopriv_).
 */

// ══════════════════════════════════════════════════════
// ACTION 1 — mota_get_filters
// Retourne la liste des catégories et formats pour peupler les <select>
// ══════════════════════════════════════════════════════

// Enregistre l'action AJAX pour utilisateurs connectés ET non connectés
add_action('wp_ajax_mota_get_filters',        'mota_get_filters');
add_action('wp_ajax_nopriv_mota_get_filters', 'mota_get_filters');

function mota_get_filters() {
  // Vérifie le nonce pour sécuriser la requête (protection CSRF)
  check_ajax_referer('mota_ajax', 'nonce');

  $result = [];
  // Récupère les termes des deux taxonomies et les formate pour le JSON
  foreach (['categories' => 'categorie', 'formats' => 'format'] as $key => $taxonomy) {
    $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
    $result[$key] = is_wp_error($terms)
      ? []
      : array_map(fn($t) => ['slug' => $t->slug, 'name' => $t->name], $terms);
  }

  wp_send_json($result); // Envoie la réponse JSON et arrête l'exécution
}

// ══════════════════════════════════════════════════════
// ACTION 2 — mota_load_photos
// Retourne un lot de 8 photos en HTML selon les filtres et la pagination
// ══════════════════════════════════════════════════════

add_action('wp_ajax_mota_load_photos',        'mota_load_photos');
add_action('wp_ajax_nopriv_mota_load_photos', 'mota_load_photos');

function mota_load_photos() {
  check_ajax_referer('mota_ajax', 'nonce'); // Sécurité : vérifie le nonce

  // Récupère et nettoie les paramètres envoyés par gallery.js
  $paged  = isset($_POST['paged'])  ? max(1, (int) $_POST['paged'])        : 1;
  $cat    = isset($_POST['cat'])    ? sanitize_text_field($_POST['cat'])    : '';
  $format = isset($_POST['format']) ? sanitize_text_field($_POST['format']) : '';
  $sort   = isset($_POST['sort'])   ? sanitize_text_field($_POST['sort'])   : 'date_desc';

  // Construit le filtre de taxonomie (tax_query) selon les selects choisis
  $tax_query = [];
  foreach (['categorie' => $cat, 'format' => $format] as $taxonomy => $value) {
    if ($value) $tax_query[] = ['taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $value];
  }
  if (count($tax_query) > 1) $tax_query['relation'] = 'AND'; // Les deux filtres doivent être vrais

  // Correspondance entre la valeur du select "Trier" et les arguments WP_Query
  $sort_map = [
    'date_asc'  => ['orderby' => 'date', 'order' => 'ASC'],  // Plus ancien en premier
    'rand'      => ['orderby' => 'rand'],                     // Ordre aléatoire
    'date_desc' => ['orderby' => 'date', 'order' => 'DESC'],  // Plus récent en premier (défaut)
  ];

  // Arguments de la requête WP_Query (OC Étape 4 : afficher les articles du CPT "photo")
  $args = array_merge([
    'post_type'      => 'photo', // CPT créé avec CPT UI (OC Étape 2)
    'posts_per_page' => 8,       // Pagination : 8 photos par lot (Green Code)
    'paged'          => $paged,
    'post_status'    => 'publish',
  ], $sort_map[$sort] ?? $sort_map['date_desc']);

  if ($tax_query) $args['tax_query'] = $tax_query; // Ajoute les filtres si définis

  $q = new WP_Query($args);

  // Génère le HTML des cartes photo via le template réutilisable (OC Étape 3)
  ob_start(); // Démarre la capture du output
  while ($q->have_posts()) { $q->the_post(); get_template_part('templates_parts/photo_block'); }
  wp_reset_postdata(); // Réinitialise $post après une WP_Query personnalisée

  // Retourne le HTML généré + un booléen indiquant s'il reste des photos à charger
  wp_send_json([
    'html'    => ob_get_clean(),                          // HTML des cartes photo
    'hasMore' => ($paged < (int) $q->max_num_pages),     // false = masque le bouton "Charger plus"
  ]);
}
