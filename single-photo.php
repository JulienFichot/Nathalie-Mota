<?php
/**
 * single-photo.php — Fiche détail d'une photo (template CPT "photo")
 * ════════════════════════════════════════════════════════════════════
 * OC Étape 3 — Points évalués ici :
 *   ✓ Template dédié au CPT "photo" (nommé single-{post_type}.php)
 *   ✓ Données dynamiques ACF : titre, référence, type, catégorie, format, année
 *   ✓ Modale contact pré-remplie avec la référence photo (data-ref sur le bouton)
 *   ✓ Navigation prev/next entre les photos avec boucle infinie
 *   ✓ Photos apparentées via WP_Query sur la même catégorie (tax_query)
 *   ✓ Réutilisation de photo_block.php pour les photos apparentées
 *   ✓ object-fit: cover et vh/vw utilisés en CSS pour adapter l'image à l'écran
 *
 * La navigation AJAX sans rechargement est gérée par navigation.js.
 */

get_header(); // Charge header.php

if (have_posts()) : while (have_posts()) : the_post();

  // ── Données de la photo (champs ACF + taxonomies) ──────────────────────────
  $id        = get_the_ID();
  $title     = get_the_title();
  $reference = get_field('reference');  // Champ ACF "référence" (OC Étape 2)
  $type      = get_field('type');       // Champ ACF "type" (OC Étape 2)
  $photo_url = mota_get_photo_url();    // Fonction helper (includes/helpers.php)

  // Lecture des taxonomies (OC Étape 2 : catégorie et format)
  $cat_terms = get_the_terms($id, 'categorie');
  $fmt_terms = get_the_terms($id, 'format');
  $cat_name  = (!is_wp_error($cat_terms) && $cat_terms) ? $cat_terms[0]->name : '';
  $cat_slug  = (!is_wp_error($cat_terms) && $cat_terms) ? $cat_terms[0]->slug : '';
  $fmt_name  = (!is_wp_error($fmt_terms) && $fmt_terms) ? $fmt_terms[0]->name : '';

  $year = get_the_date('Y'); // Utilise la date native du post WordPress (OC Étape 2)

  // ── Navigation prev/next avec boucle infinie (OC Étape 3) ──────────────────
  // get_previous_post / get_next_post sont des fonctions natives WordPress
  $prev = get_previous_post(false, '', 'categorie');
  $next = get_next_post(false, '', 'categorie') ?: get_next_post();

  // Si on est sur la dernière photo, on revient à la première (boucle infinie)
  if (!$next) {
    $q = new WP_Query(['post_type' => 'photo', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'ASC', 'post__not_in' => [$id], 'post_status' => 'publish']);
    if ($q->have_posts()) $next = $q->posts[0];
  }
  // Si on est sur la première photo, on revient à la dernière (boucle infinie)
  if (!$prev) {
    $q = new WP_Query(['post_type' => 'photo', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC', 'post__not_in' => [$id], 'post_status' => 'publish']);
    if ($q->have_posts()) $prev = $q->posts[0];
  }

  $prev_img = $prev ? mota_get_photo_url($prev->ID, 'medium') : ''; // Miniature précédente
  $next_img = $next ? mota_get_photo_url($next->ID, 'medium') : ''; // Miniature suivante

  // ── Photos apparentées — même catégorie (OC Étape 3 : wp_query même catégorie) ──
  $related = $cat_slug ? new WP_Query([
    'post_type'      => 'photo',
    'posts_per_page' => 2,           // 2 photos apparentées maximum
    'post__not_in'   => [$id],       // Exclut la photo affichée
    'post_status'    => 'publish',
    'tax_query'      => [[            // Filtre par catégorie
      'taxonomy' => 'categorie',
      'field'    => 'slug',
      'terms'    => $cat_slug,
    ]],
  ]) : null;
?>

<main class="single-photo">

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
    </div>

    <div class="single-image">
      <?php if ($photo_url): ?>
        <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($title); ?>">
      <?php endif; ?>
    </div>

  </section>

  <div class="single-bottom-bar">

    <div class="single-contact">
      <p class="single-cta">Cette photo vous intéresse ?</p>
      <!-- OC Étape 3 : data-ref transmet la référence à contact.js qui pré-remplit le formulaire -->
      <button type="button" class="contact-button" data-ref="<?php echo esc_attr($reference); ?>">CONTACT</button>
    </div>

    <div class="single-nav-links">

      <div class="nav-link-group nav-link-group--prev">
        <?php if ($prev_img): ?>
          <div class="nav-thumb-hover">
            <img src="<?php echo esc_url($prev_img); ?>" alt="">
          </div>
        <?php endif; ?>
        <?php if ($prev): ?>
          <a class="nav-under" href="<?php echo esc_url(get_permalink($prev->ID)); ?>">← PRÉCÉDENTE</a>
        <?php else: ?>
          <span class="nav-under disabled">← PRÉCÉDENTE</span>
        <?php endif; ?>
      </div>

      <div class="nav-link-group nav-link-group--next">
        <?php if ($next_img): ?>
          <div class="nav-thumb-hover">
            <img src="<?php echo esc_url($next_img); ?>" alt="">
          </div>
        <?php endif; ?>
        <?php if ($next): ?>
          <a class="nav-under" href="<?php echo esc_url(get_permalink($next->ID)); ?>">SUIVANTE →</a>
        <?php else: ?>
          <span class="nav-under disabled">SUIVANTE →</span>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <?php if ($related && $related->have_posts()): ?>
  <section class="single-related">
    <h2 class="related-title">Vous aimerez aussi</h2>
    <div class="related-grid">
      <?php
      while ($related->have_posts()) : $related->the_post();
        get_template_part('templates_parts/photo_block');
      endwhile;
      wp_reset_postdata();
      ?>
    </div>
  </section>
  <?php endif; ?>

</main>

<?php
endwhile; endif;

get_footer();
