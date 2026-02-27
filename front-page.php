<?php
get_header();
?>

<section class="hero" aria-label="Hero">
  <?php
  // Hero: prend la première photo publiée (ACF photo_image) sinon fallback
  $hero_query = new WP_Query([
    'post_type'      => 'photo',
    'posts_per_page' => 1,
    'post_status'    => 'publish'
  ]);

  $hero_url = '';
  if ($hero_query->have_posts()) {
    $hero_query->the_post();
    $hero_url = (string) get_field('photo_image');
  }
  wp_reset_postdata();

  if (!$hero_url) {
    $hero_url = get_template_directory_uri() . '/assets/images/hero-fallback.jpg';
  }
  ?>
  <div class="hero-bg" style="background-image:url('<?php echo esc_url($hero_url); ?>')"></div>
</section>

<section class="gallery-section">
  <div class="container">

    <div class="filters">
      <select id="filter-category" aria-label="Filtrer par catégorie">
        <option value="">CATÉGORIES</option>
      </select>

      <select id="filter-format" aria-label="Filtrer par format">
        <option value="">FORMATS</option>
      </select>

      <select id="sort-by" aria-label="Trier">
        <option value="date_desc">TRIER PAR</option>
        <option value="date_desc">PLUS RÉCENT</option>
        <option value="date_asc">PLUS ANCIEN</option>
        <option value="rand">ALÉATOIRE</option>
      </select>
    </div>

    <div class="photo-gallery" id="photo-gallery">
      <?php
      // 8 photos au chargement
      $photos = new WP_Query([
        'post_type'      => 'photo',
        'posts_per_page' => 8,
        'paged'          => 1,
        'post_status'    => 'publish'
      ]);

      if ($photos->have_posts()) :
        while ($photos->have_posts()) : $photos->the_post();
          get_template_part('templates_parts/photo_block');
        endwhile;
      else :
        echo '<p>Aucune photo disponible pour le moment.</p>';
      endif;

      wp_reset_postdata();
      ?>
    </div>

    <div id="ajax-pagination">
      <button id="load-more" type="button">CHARGER PLUS</button>
    </div>

  </div>
</section>

<div class="lightbox-overlay" id="lightbox-overlay" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="lightbox">
    <button class="lightbox-close" type="button" aria-label="Fermer">✕</button>

    <button class="lightbox-prev" type="button" aria-label="Précédente">←</button>
    <button class="lightbox-next" type="button" aria-label="Suivante">→</button>

    <div class="lightbox-media">
      <img id="lightbox-img" src="" alt="">
    </div>

    <div class="lightbox-info">
      <span id="lightbox-title"></span>
      <span id="lightbox-ref"></span>
    </div>
  </div>
</div>

<?php
get_footer();
