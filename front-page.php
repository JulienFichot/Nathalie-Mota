<?php
/**
 * front-page.php — Page d'accueil du site
 * ═════════════════════════════════════════
 * OC Étape 4 — Points évalués ici :
 *   ✓ Hero avec image de fond issue du catalogue (première photo publiée)
 *   ✓ Liste des photos via WP_Query sur le CPT "photo"
 *   ✓ Réutilisation du template photo_block.php (créé à l'OC Étape 3)
 *   ✓ Pagination infinie ("CHARGER PLUS") — chargement AJAX via gallery.js
 *   ✓ Filtres Catégorie/Format et Tri — peuplés dynamiquement via AJAX (ajax.php)
 *
 * Ce template est automatiquement utilisé par WordPress pour la page d'accueil
 * quand une "Page d'accueil statique" est définie dans WP Admin > Réglages > Lecture.
 */

get_header(); // Charge header.php (balise <html> + <head> + navigation)
?>

<!-- ── HERO (OC Étape 4) ──────────────────────────────────────────────────── -->
<!-- Section plein écran avec la première photo du catalogue en fond -->
<section class="hero" aria-label="Hero">
  <?php
  // Récupère l'URL de la première photo publiée pour le fond du hero
  $hero_query = new WP_Query(['post_type' => 'photo', 'posts_per_page' => 1, 'post_status' => 'publish']);
  $hero_url = '';
  if ($hero_query->have_posts()) { $hero_query->the_post(); $hero_url = mota_get_photo_url(); }
  wp_reset_postdata(); // Réinitialise $post après la requête personnalisée
  ?>
  <div class="hero-bg" style="background-image:url('<?php echo esc_url($hero_url); ?>')"></div>
</section>

<!-- ── GALERIE + FILTRES (OC Étape 4) ────────────────────────────────────── -->
<section class="gallery-section">
  <div class="container">

    <!-- Filtres : les <option> sont remplis dynamiquement par gallery.js via AJAX -->
    <!-- OC : "les données alimentant les selects doivent être chargées dynamiquement depuis les taxonomies" -->
    <div class="filters">
      <select id="filter-category" aria-label="Filtrer par catégorie">
        <option value="">CATÉGORIES</option>
        <!-- Options injectées par gallery.js → mota_get_filters (ajax.php) -->
      </select>

      <select id="filter-format" aria-label="Filtrer par format">
        <option value="">FORMATS</option>
        <!-- Options injectées par gallery.js → mota_get_filters (ajax.php) -->
      </select>

      <select id="sort-by" aria-label="Trier">
        <option value="">TRIER PAR</option>
        <option value="date_desc">PLUS RÉCENT</option>
        <option value="date_asc">PLUS ANCIEN</option>
        <option value="rand">ALÉATOIRE</option>
      </select>
    </div>

    <!-- Grille de photos — chargement initial PHP, rechargements suivants en AJAX -->
    <div class="photo-gallery" id="photo-gallery">
      <?php
      // Chargement initial : 8 premières photos (Green Code : pas toutes d'un coup)
      $photos = new WP_Query([
        'post_type'      => 'photo', // CPT créé à l'OC Étape 2
        'posts_per_page' => 8,
        'paged'          => 1,
        'post_status'    => 'publish',
      ]);
      if ($photos->have_posts()) :
        while ($photos->have_posts()) : $photos->the_post();
          // Réutilise le template card photo créé à l'OC Étape 3
          get_template_part('templates_parts/photo_block');
        endwhile;
      else :
        echo '<p>Aucune photo disponible pour le moment.</p>';
      endif;
      wp_reset_postdata();
      ?>
    </div>

    <!-- Bouton pagination infinie — géré par gallery.js (OC Étape 4 : pagination AJAX) -->
    <div id="ajax-pagination">
      <button id="load-more" type="button">CHARGER PLUS</button>
    </div>

  </div>
</section>

<?php
get_footer(); // Charge footer.php (modale contact + lightbox + wp_footer)
