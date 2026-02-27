<?php
get_header();

if (have_posts()) : while (have_posts()) : the_post();

  // ACF
  $reference   = get_field('reference');
  $type        = get_field('type');
  $photo_field = get_field('photo_image');
  $title       = get_the_title();

  // URL image (robuste)
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

  // Taxonomies
  $cat_terms = get_the_terms(get_the_ID(), 'categorie');
  $fmt_terms = get_the_terms(get_the_ID(), 'format');
  $cat_name  = (!is_wp_error($cat_terms) && !empty($cat_terms)) ? $cat_terms[0]->name : '';
  $fmt_name  = (!is_wp_error($fmt_terms) && !empty($fmt_terms)) ? $fmt_terms[0]->name : '';
  $cat_slug  = (!is_wp_error($cat_terms) && !empty($cat_terms)) ? $cat_terms[0]->slug : '';

  $year = get_the_date('Y');

  // Prev/Next (toutes photos)
  $prev = get_previous_post(false, '', 'categorie');
  $next = get_next_post(false, '', 'categorie');

  // Aperçu image suivante (si null, on prend next global)
  if (!$next) {
    $next = get_next_post();
  }
  $next_img = '';
  if ($next) {
    $next_field = get_field('photo_image', $next->ID);
    if (is_string($next_field) && !empty($next_field)) {
      $next_img = $next_field;
    } elseif (is_int($next_field) && $next_field > 0) {
      $next_img = wp_get_attachment_image_url($next_field, 'medium');
    } elseif (is_array($next_field)) {
      if (!empty($next_field['url'])) $next_img = $next_field['url'];
      elseif (!empty($next_field['ID'])) $next_img = wp_get_attachment_image_url((int)$next_field['ID'], 'medium');
    }
  }

  // Related: 2 photos même catégorie (hors current)
  $related = null;
  if ($cat_slug) {
    $related = new WP_Query([
      'post_type'      => 'photo',
      'posts_per_page' => 2,
      'post__not_in'   => [get_the_ID()],
      'tax_query'      => [[
        'taxonomy' => 'categorie',
        'field'    => 'slug',
        'terms'    => $cat_slug,
      ]],
      'post_status'    => 'publish',
    ]);
  }
?>

<main class="single-photo container">
  <section class="single-top">
    <div class="single-info">
      <h1 class="single-title"><?php echo esc_html($title); ?></h1>

      <ul class="single-meta">
        <?php if ($reference): ?><li><strong>RÉFÉRENCE</strong> <?php echo esc_html($reference); ?></li><?php endif; ?>
        <?php if ($type): ?><li><strong>TYPE</strong> <?php echo esc_html($type); ?></li><?php endif; ?>
        <?php if ($cat_name): ?><li><strong>CATÉGORIE</strong> <?php echo esc_html($cat_name); ?></li><?php endif; ?>
        <?php if ($fmt_name): ?><li><strong>FORMAT</strong> <?php echo esc_html($fmt_name); ?></li><?php endif; ?>
        <li><strong>ANNÉE</strong> <?php echo esc_html($year); ?></li>
      </ul>

      <div class="single-contact">
        <div class="single-sep"></div>
        <p class="single-cta">Cette photo vous intéresse ?</p>

        <div class="single-form">
          <?php
            // Formulaire dédié (CF7)
            echo do_shortcode('[contact-form-7 id="123" title="Contact Photo"]');
          ?>
        </div>
      </div>
    </div>

    <div class="single-image">
      <?php if ($photo_url): ?>
        <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($title); ?>">
      <?php endif; ?>
    </div>
  </section>

 <section class="single-nav">
  <div class="single-sep"></div>

  <div class="nav-row">
    <div class="nav-next-preview">
      <?php if ($next && $next_img): ?>
        <a href="<?php echo esc_url(get_permalink($next->ID)); ?>" class="nav-preview-link" aria-label="Voir la photo suivante">
          <img src="<?php echo esc_url($next_img); ?>" alt="">
        </a>
      <?php else: ?>
        <div class="nav-preview-empty"></div>
      <?php endif; ?>

      <div class="nav-arrows-under">
        <?php if ($prev) : ?>
          <a class="nav-under left" href="<?php echo esc_url(get_permalink($prev->ID)); ?>" aria-label="Photo précédente">← Précédente</a>
        <?php else: ?>
          <span class="nav-under disabled">← Précédente</span>
        <?php endif; ?>

        <?php if ($next) : ?>
          <a class="nav-under right" href="<?php echo esc_url(get_permalink($next->ID)); ?>" aria-label="Photo suivante">Suivante →</a>
        <?php else: ?>
          <span class="nav-under disabled">Suivante →</span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="single-sep"></div>
</section>

    <div class="single-sep"></div>
  </section>

  <section class="single-related">
    <h2 class="related-title">Vous aimerez aussi</h2>

    <div class="related-grid">
      <?php
      if ($related && $related->have_posts()) :
        while ($related->have_posts()) : $related->the_post();
          get_template_part('templates_parts/photo_block');
        endwhile;
        wp_reset_postdata();
      endif;
      ?>
    </div>
  </section>
</main>

<?php
endwhile; endif;

get_footer();