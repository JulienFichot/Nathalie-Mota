<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header class="site-header">
  <div class="container header-inner">
    <a class="site-logo" href="<?php echo esc_url(home_url('/')); ?>">
      <img
        src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo-nathalie-mota.png'); ?>"
        alt="Nathalie Mota"
      >
    </a>

    <button class="burger" type="button" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
      <span></span><span></span><span></span>
    </button>

    <nav id="main-nav" class="main-navigation" aria-label="Navigation principale">
      <?php
        wp_nav_menu([
          'theme_location' => 'main-menu',
          'container'      => false,
          'menu_class'     => 'menu',
        ]);
      ?>
    </nav>
  </div>
</header>
