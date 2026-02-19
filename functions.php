<?php

// ====== Setup du thème ======
function mota_theme_setup() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails'); // OK même si tu n'utilises plus l'image mise en avant
  add_theme_support('post-formats', ['standard', 'gallery', 'video', 'image']); // futur blog

  register_nav_menus([
    'main-menu' => 'Menu principal',
  ]);
}
add_action('after_setup_theme', 'mota_theme_setup');

// ====== Enqueue CSS / JS + variables Ajax ======
function mota_enqueue_assets() {

  wp_enqueue_style(
    'mota-style',
    get_template_directory_uri() . '/assets/css/style.css',
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

  // IMPORTANT : on fournit ajaxUrl + nonce au front
  wp_localize_script('mota-scripts', 'motaAjax', [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce'   => wp_create_nonce('mota_ajax'),
  ]);
}
add_action('wp_enqueue_scripts', 'mota_enqueue_assets');

// ====== (Optionnel) Support thumbnails pour CPT photo ======
add_action('registered_post_type', function($post_type, $args) {
  if ($post_type === 'photo') {
    add_post_type_support($post_type, ['thumbnail', 'title', 'editor']);
  }
}, 10, 2);

// ====== Ajax: récupérer termes pour remplir les selects ======
add_action('wp_ajax_mota_get_filters', 'mota_get_filters');
add_action('wp_ajax_nopriv_mota_get_filters', 'mota_get_filters');

function mota_get_filters() {
  check_ajax_referer('mota_ajax', 'nonce');

  $cats    = get_terms(['taxonomy' => 'categorie', 'hide_empty' => true]);
  $formats = get_terms(['taxonomy' => 'format', 'hide_empty' => true]);

  wp_send_json([
    'categories' => is_wp_error($cats) ? [] : array_map(function($t){
      return ['slug' => $t->slug, 'name' => $t->name];
    }, $cats),
    'formats' => is_wp_error($formats) ? [] : array_map(function($t){
      return ['slug' => $t->slug, 'name' => $t->name];
    }, $formats),
  ]);
}

// ====== Ajax: charger photos (filtres + tri + pagination) ======
add_action('wp_ajax_mota_load_photos', 'mota_load_photos');
add_action('wp_ajax_nopriv_mota_load_photos', 'mota_load_photos');

function mota_load_photos() {
  check_ajax_referer('mota_ajax', 'nonce');

  $paged  = isset($_POST['paged']) ? max(1, (int) $_POST['paged']) : 1;
  $cat    = isset($_POST['cat']) ? sanitize_text_field($_POST['cat']) : '';
  $format = isset($_POST['format']) ? sanitize_text_field($_POST['format']) : '';
  $sort   = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'date_desc';

  $tax_query = [];

  if ($cat) {
    $tax_query[] = [
      'taxonomy' => 'categorie',
      'field'    => 'slug',
      'terms'    => $cat,
    ];
  }

  if ($format) {
    $tax_query[] = [
      'taxonomy' => 'format',
      'field'    => 'slug',
      'terms'    => $format,
    ];
  }

  if (count($tax_query) > 1) {
    $tax_query['relation'] = 'AND';
  }

  $args = [
    'post_type'      => 'photo',
    'posts_per_page' => 8,
    'paged'          => $paged,
    'post_status'    => 'publish',
  ];

  if (!empty($tax_query)) {
    $args['tax_query'] = $tax_query;
  }

  if ($sort === 'date_asc') {
    $args['orderby'] = 'date';
    $args['order']   = 'ASC';
  } elseif ($sort === 'rand') {
    $args['orderby'] = 'rand';
  } else {
    $args['orderby'] = 'date';
    $args['order']   = 'DESC';
  }

  $q = new WP_Query($args);

  ob_start();
  if ($q->have_posts()) {
    while ($q->have_posts()) {
      $q->the_post();
      get_template_part('templates_parts/photo_block');
    }
  }
  wp_reset_postdata();
  $html = ob_get_clean();

  wp_send_json([
    'html'    => $html,
    'hasMore' => ($paged < (int) $q->max_num_pages),
  ]);
}