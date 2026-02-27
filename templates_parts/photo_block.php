<?php
$reference   = get_field('reference');
$photo_field = get_field('photo_image');
$photo_title = get_the_title();

$photo_url = '';
if (is_string($photo_field) && !empty($photo_field)) {
  $photo_url = $photo_field;
} elseif (is_int($photo_field) && $photo_field > 0) {
  $photo_url = wp_get_attachment_image_url($photo_field, 'large');
} elseif (is_array($photo_field)) {
  if (!empty($photo_field['url'])) {
    $photo_url = $photo_field['url'];
  } elseif (!empty($photo_field['ID'])) {
    $photo_url = wp_get_attachment_image_url((int)$photo_field['ID'], 'large');
  }
}

$cat_terms = get_the_terms(get_the_ID(), 'categorie');
$fmt_terms = get_the_terms(get_the_ID(), 'format');
$cat_name = (!is_wp_error($cat_terms) && !empty($cat_terms)) ? $cat_terms[0]->name : '';
$fmt_name = (!is_wp_error($fmt_terms) && !empty($fmt_terms)) ? $fmt_terms[0]->name : '';
?>

<article class="photo-block">
  <div class="photo-media">
    <?php if (!empty($photo_url)): ?>
      <!-- Clic photo => page dédiée -->
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
    <div class="photo-infos">
      <?php if ($cat_name): ?><span class="photo-tag"><?php echo esc_html($cat_name); ?></span><?php endif; ?>
      <?php if ($fmt_name): ?><span class="photo-tag"><?php echo esc_html($fmt_name); ?></span><?php endif; ?>
    </div>

    <div class="photo-actions">
      <button type="button" class="contact-button" data-ref="<?php echo esc_attr($reference); ?>">CONTACT</button>

      <!-- Icône plein écran => lightbox, on passe les infos en data-* -->
      <button
        type="button"
        class="fullscreen-button lightbox-open"
        aria-label="Plein écran"
        data-src="<?php echo esc_url($photo_url); ?>"
        data-title="<?php echo esc_attr($photo_title); ?>"
        data-ref="<?php echo esc_attr($reference); ?>"
      >
        ⤢
      </button>
    </div>
  </div>
</article>