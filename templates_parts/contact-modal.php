
<?php
// Modale Contact : appelée depuis footer.php
?>
<div class="contact-modal" id="contact-modal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="contact-modal__panel" role="document">
    <button type="button" class="contact-modal__close" aria-label="Fermer">✕</button>

    <div class="contact-modal__content">
      <?php
      echo do_shortcode('[contact-form-7 id="a4924c5" title="Contact"]');
      ?>
    </div>
  </div>
</div>