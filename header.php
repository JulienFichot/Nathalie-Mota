<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header class="site-header">
  <div class="header-container">

    <div class="site-logo">
      <a href="<?php echo home_url('/'); ?>">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-nathalie-mota.svg" alt="Nathalie Mota">
      </a>
    </div>

    <nav class="main-navigation">
      <?php
        wp_nav_menu([
          'theme_location' => 'main-menu',
          'container' => false
        ]);
      ?>
    </nav>

  </div>
</header>
