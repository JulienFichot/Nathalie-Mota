<?php
$reference   = get_field('reference');
$photo_field = get_field('photo_image'); // peut être URL, ID ou Array
$photo_title = get_the_title();

$photo_url = '';

// Si ACF retourne une URL
if (is_string($photo_field) && !empty($photo_field)) {
    $photo_url = $photo_field;
}

// Si ACF retourne un ID
elseif (is_int($photo_field)) {
    $photo_url = wp_get_attachment_image_url($photo_field, 'large');
}

// Si ACF retourne un Array
elseif (is_array($photo_field) && !empty($photo_field['url'])) {
    $photo_url = $photo_field['url'];
}
?>

<article class="photo-block">
  <div class="photo-media">
    <?php if (!empty($photo_url)): ?>
      <img
        src="<?php echo esc_url($photo_url); ?>"
        alt="<?php echo esc_attr($photo_title); ?>"
        class="lightbox-trigger"
        loading="lazy"
      >
    <?php else: ?>
      <div style="padding:20px;font-size:12px;color:#777;">
        Image non trouvée
      </div>
    <?php endif; ?>
  </div>

  <div class="photo-overlay">
    <button type="button"
            class="contact-button"
            data-ref="<?php echo esc_attr($reference); ?>">
      CONTACT
    </button>

    <button type="button"
            class="fullscreen-button"
            aria-label="Plein écran">
      ⤢
    </button>
  </div>
</article>
