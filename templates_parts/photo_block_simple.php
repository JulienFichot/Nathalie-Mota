<?php
$photo_title = get_the_title();
$photo_url   = mota_get_photo_url();
?>
<article class="photo-block">
  <?php if ($photo_url): ?>
    <a href="<?php the_permalink(); ?>" class="photo-link" aria-label="<?php echo esc_attr($photo_title); ?>">
      <div class="photo-media">
        <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($photo_title); ?>" loading="lazy" decoding="async">
      </div>
    </a>
  <?php endif; ?>
</article>
