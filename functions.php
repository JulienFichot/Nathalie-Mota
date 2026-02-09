<?php

function mota_theme_setup() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');

  register_nav_menus([
    'main-menu' => 'Menu principal'
  ]);
}
add_action('after_setup_theme', 'mota_theme_setup');

function mota_enqueue_assets() {
  wp_enqueue_style(
    'mota-style',
    get_stylesheet_uri(),
    [],
    '1.0'
  );

  wp_enqueue_script(
    'mota-scripts',
    get_template_directory_uri() . '/assets/js/scripts.js',
    [],
    '1.0',
    true
  );
}
add_action('wp_enqueue_scripts', 'mota_enqueue_assets');
