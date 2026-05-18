<?php
/**
 * header.php — En-tête du site (logo + navigation)
 * ══════════════════════════════════════════════════
 * OC Étape 1 — Points évalués ici :
 *   ✓ wp_head()        → charge les CSS/JS enqueués depuis setup.php (obligatoire)
 *   ✓ wp_nav_menu()    → menu géré depuis WP Admin > Apparence > Menus (pas en dur)
 *   ✓ Logo extrait de la maquette Figma et intégré en tant qu'image
 *   ✓ Bouton burger pour la navigation mobile (géré par burger.js)
 *   ✓ Attributs ARIA (aria-expanded, aria-controls) pour l'accessibilité clavier
 *
 * Ce fichier est appelé via get_header() au début de chaque template.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); /* Injecte le CSS, les JS et les balises meta enregistrés via wp_enqueue */ ?>
</head>

<body <?php body_class(); ?>>

<header class="site-header">
  <div class="container header-inner">

    <!-- Logo — lien vers la page d'accueil -->
    <a class="site-logo" href="<?php echo esc_url(home_url('/')); ?>">
      <img
        src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo-nathalie-mota.png'); ?>"
        alt="Nathalie Mota"
      >
    </a>

    <!-- Bouton burger — visible uniquement sur mobile (CSS) — géré par burger.js -->
    <button class="burger" type="button" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
      <span class="burger-bar"></span><span class="burger-bar"></span><span class="burger-bar"></span>
      <span class="burger-close" aria-hidden="true">✕</span>
    </button>

    <!-- Menu principal — wp_nav_menu() lit les liens depuis WP Admin (OC : pas en dur) -->
    <nav id="main-nav" class="main-navigation" aria-label="Navigation principale">
      <?php
        wp_nav_menu([
          'theme_location' => 'main-menu', // Correspond au menu enregistré dans setup.php
          'container'      => false,       // Pas de <div> wrapper autour du <ul>
          'menu_class'     => 'menu',      // Classe CSS appliquée au <ul>
        ]);
      ?>
    </nav>

  </div>
</header>
