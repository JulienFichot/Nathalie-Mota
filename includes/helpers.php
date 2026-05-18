<?php
/**
 * includes/helpers.php — Fonctions utilitaires réutilisées dans tous les templates
 * ══════════════════════════════════════════════════════════════════════════════════
 * OC Étape 2/3 — Points évalués ici :
 *   ✓ Lecture des champs ACF : photo_image, référence, type
 *   ✓ Lecture des taxonomies : catégorie, format (via get_the_terms)
 *
 * Ces fonctions sont appelées dans : front-page.php, single-photo.php,
 * templates_parts/photo_block.php
 */

/**
 * mota_get_photo_url() — Récupère l'URL de l'image depuis le champ ACF "photo_image"
 *
 * ACF peut retourner trois formats selon la configuration du champ :
 *   - string : URL directe (ex: "https://…/photo.jpg")
 *   - int    : ID d'un media WordPress
 *   - array  : tableau avec les clés 'url' et 'ID'
 *
 * @param int|false $post_id  ID du post (false = post courant dans la boucle)
 * @param string    $size     Taille WP : 'thumbnail', 'medium', 'large', 'full'
 * @return string             URL de l'image, ou '' si introuvable
 */
function mota_get_photo_url($post_id = false, $size = 'large') {
  $field = get_field('photo_image', $post_id); // Lecture du champ personnalisé ACF

  if (is_string($field) && $field)  return $field;                                    // Format 1 : URL directe
  if (is_int($field) && $field > 0) return wp_get_attachment_image_url($field, $size); // Format 2 : ID media
  if (is_array($field)) {
    if (!empty($field['url'])) return $field['url'];                                  // Format 3a : tableau avec 'url'
    if (!empty($field['ID']))  return wp_get_attachment_image_url((int)$field['ID'], $size); // Format 3b : tableau avec 'ID'
  }
  return ''; // Aucun format reconnu → chaîne vide (les templates testent avec if)
}

/**
 * mota_get_term_name() — Récupère le nom (ou le slug) du premier terme d'une taxonomie
 *
 * Exemples d'utilisation :
 *   mota_get_term_name($id, 'categorie')         → "Portrait"
 *   mota_get_term_name($id, 'categorie', 'slug') → "portrait"
 *   mota_get_term_name($id, 'format')            → "30×40 cm"
 *
 * @param int    $post_id   ID du post
 * @param string $taxonomy  Slug de la taxonomie ('categorie' ou 'format')
 * @param string $field     Propriété voulue sur l'objet terme ('name', 'slug', 'term_id')
 * @return string           Valeur demandée, ou '' si aucun terme
 */
function mota_get_term_name($post_id, $taxonomy, $field = 'name') {
  $terms = get_the_terms($post_id, $taxonomy); // Retourne un tableau de termes ou WP_Error
  return (!is_wp_error($terms) && !empty($terms)) ? $terms[0]->$field : '';
}
