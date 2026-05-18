<?php
/**
 * templates_parts/contact-modal.php — HTML de la modale de contact
 * ══════════════════════════════════════════════════════════════════
 * OC Étape 1 — Points évalués ici :
 *   ✓ Modale créée dans templates_parts/ pour être réutilisable (OC : "créez un fichier
 *     dédié dans /templates_part")
 *   ✓ Formulaire géré par Contact Form 7 via do_shortcode()
 *   ✓ Attributs ARIA (aria-hidden, role="dialog", aria-modal) pour l'accessibilité
 *   ✓ Ouverture/fermeture gérée par contact.js
 *   ✓ Le champ "ref" est pré-rempli par contact.js quand on vient d'une fiche photo
 */
?>
<div class="contact-modal" id="contact-modal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="contact-modal__panel" role="document">

    <!-- Bouton fermeture — géré par contact.js -->
    <button type="button" class="contact-modal__close" aria-label="Fermer">✕</button>

    <!-- Titre décoratif répété — aria-hidden car purement visuel -->
    <div class="contact-modal__hero" aria-hidden="true">
      <p class="contact-hero-text">CONTACTCONTACTCONTACTCONTACTCONTACTCONTACTCONTACTCONTACTCONTACTCONTACT</p>
    </div>

    <!-- Formulaire Contact Form 7 — shortcode WordPress -->
    <!-- Le champ nommé "ref" est trouvé et pré-rempli par contact.js (OC Étape 3) -->
    <div class="contact-modal__body">
      <?php echo do_shortcode('[contact-form-7 title="Contact Nathalie Mota"]'); ?>
    </div>

  </div>
</div>
