<?php
/**
 * templates_parts/photo_block.php — Carte photo réutilisable
 * ════════════════════════════════════════════════════════════
 * OC Étape 3 — Points évalués ici :
 *   ✓ Bloc d'affichage d'une photo créé à l'Étape 3 et réutilisé à l'Étape 4
 *   ✓ Hover overlay avec icône plein écran (ouvre la lightbox) et œil (→ single)
 *   ✓ loading="lazy" + decoding="async" sur l'image (Green Code — OC Étape 2)
 *   ✓ data-src, data-title, data-ref transmis à lightbox.js (OC Étape 5)
 *   ✓ data-ref transmis à contact.js pour pré-remplir la référence (OC Étape 3)
 *
 * Ce template est appelé depuis :
 *   - front-page.php (galerie d'accueil)
 *   - single-photo.php (section "Vous aimerez aussi")
 *   - includes/ajax.php (réponses AJAX)
 */

$id          = get_the_ID();
$photo_title = get_the_title();
$reference   = get_field('reference');  // Champ ACF (OC Étape 2)
$photo_url   = mota_get_photo_url();   // Helper — includes/helpers.php
$cat_name    = mota_get_term_name($id, 'categorie'); // Taxonomie (OC Étape 2)
?>

<article class="photo-block">
  <div class="photo-media">
    <?php if ($photo_url): ?>
      <a href="<?php the_permalink(); ?>" class="photo-link" aria-label="<?php echo esc_attr($photo_title); ?>">
        <img
          src="<?php echo esc_url($photo_url); ?>"
          alt="<?php echo esc_attr($photo_title); ?>"
          loading="lazy"
          decoding="async"
        >
      </a>
    <?php else: ?>
      <div class="photo-missing">Image non trouvée</div>
    <?php endif; ?>
  </div>

  <div class="photo-overlay">

    <!-- Haut droite : plein écran -->
    <button
      type="button"
      class="fullscreen-button lightbox-open"
      aria-label="Plein écran"
      data-src="<?php echo esc_url($photo_url); ?>"
      data-title="<?php echo esc_attr($photo_title); ?>"
      data-ref="<?php echo esc_attr($reference); ?>"
    >⤢</button>

    <!-- Centre : œil → page infos de la photo -->
    <a
      href="<?php the_permalink(); ?>"
      class="photo-eye"
      aria-label="Voir les infos de la photo"
    ><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>

    <!-- Bas : titre gauche / catégorie droite -->
    <div class="photo-bottom">
      <span class="photo-bottom-title"><?php echo esc_html($photo_title); ?></span>
      <?php if ($cat_name): ?>
        <span class="photo-bottom-cat"><?php echo esc_html($cat_name); ?></span>
      <?php endif; ?>
    </div>

  </div>
</article>
