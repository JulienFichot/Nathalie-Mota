<?php
/**
 * page-contact.php — Page contact dédiée (accessible via /contact)
 * ══════════════════════════════════════════════════════════════════
 * OC Étape 1 — Template personnalisé pour la page contact :
 *   ✓ "Template Name" permet d'assigner ce template depuis WP Admin
 *   ✓ Titre typographique répété (CONTACT CONTACT…) selon la maquette
 *   ✓ Formulaire Contact Form 7 intégré via do_shortcode()
 *
 * Note : Ce template est distinct de la modale contact (templates_parts/contact-modal.php).
 * La page /contact est accessible directement depuis le menu, la modale s'ouvre en overlay.
 */
get_header();
?>

<main class="contact-page container">

  <div class="contact-hero" aria-hidden="true">
    <p class="contact-hero-text">CONTACT&ensp;CONTACT&ensp;CONTACT&ensp;CONTACT&ensp;CONTACT&ensp;CONTACT&ensp;CONTACT</p>
  </div>

  <div class="contact-form-wrap">
    <?php echo do_shortcode('[contact-form-7 title="Contact Nathalie Mota"]'); ?>
  </div>

</main>

<?php get_footer(); ?>
