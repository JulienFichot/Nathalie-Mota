<?php
/**
 * footer.php — Pied de page + modale contact + lightbox
 * ═══════════════════════════════════════════════════════
 * OC Étape 1 : Footer intégré selon la maquette
 * OC Étape 1 : Modale contact incluse ici via get_template_part()
 *              (disponible sur toutes les pages car footer.php est universel)
 * OC Étape 5 : HTML de la lightbox placé ici (OC : "intégrez son code PHP dans footer.php")
 *
 * wp_footer() en bas est obligatoire : il charge les scripts JS enqueués.
 */
?>

<footer class="site-footer">
  <div class="footer-line"></div>

  <nav class="footer-nav" aria-label="Liens footer">
    <?php
      // Récupère l'URL de la page Mentions légales par son slug
      $mentions = get_page_by_path('mentions-legales');
      $mentions_url = $mentions ? get_permalink($mentions->ID) : '#';
    ?>
    <a href="<?php echo esc_url($mentions_url); ?>">MENTIONS LÉGALES</a>
    <a href="<?php echo esc_url(get_privacy_policy_url()); ?>">VIE PRIVÉE</a>
    <span>TOUS DROITS RÉSERVÉS</span>
  </nav>
</footer>

<?php
// OC Étape 1 — Modale contact : incluse depuis templates_parts/ pour être réutilisable
// Elle est disponible sur toutes les pages car footer.php est appelé partout.
get_template_part('templates_parts/contact-modal');
?>

<!-- OC Étape 5 — Lightbox : HTML placé dans footer.php selon les recommandations OC -->
<!-- Le comportement est géré par assets/js/lightbox.js (chargé via wp_enqueue_script) -->
<div class="lightbox-overlay" id="lightbox-overlay" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="lightbox">
    <button class="lightbox-close" type="button" aria-label="Fermer">✕</button>
    <button class="lightbox-prev" type="button" aria-label="Précédente">←</button>
    <button class="lightbox-next" type="button" aria-label="Suivante">→</button>
    <div class="lightbox-media">
      <img id="lightbox-img" src="" alt=""> <!-- src rempli dynamiquement par lightbox.js -->
    </div>
    <div class="lightbox-info">
      <span id="lightbox-title"></span> <!-- Titre de la photo, injecté par lightbox.js -->
      <span id="lightbox-ref"></span>   <!-- Référence de la photo, injectée par lightbox.js -->
    </div>
  </div>
</div>

<?php wp_footer(); /* Obligatoire : injecte les scripts JS enqueués en footer */ ?>
</body>
</html>
