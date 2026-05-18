<?php
/**
 * page.php — Template générique pour les pages statiques WordPress
 * ══════════════════════════════════════════════════════════════════
 * OC Étape 1 — Ce template sert aux pages comme :
 *   - À propos (a-propos)
 *   - Mentions légales (mentions-legales)
 *   - Vie privée (vie-privee)
 *
 * have_posts() / the_post() / the_title() / the_content() sont les
 * fonctions standard de la boucle WordPress.
 */
get_header();
?>

<main class="page-content container">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <h1 class="page-title"><?php the_title(); /* Titre de la page depuis WP Admin */ ?></h1>
    <div class="page-body">
      <?php the_content(); /* Contenu saisi dans l'éditeur WordPress */ ?>
    </div>
  <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
