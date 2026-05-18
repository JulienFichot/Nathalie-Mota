/**
 * contact.js — Modale de contact
 *
 * - Ouvre la modale depuis .contact-button (data-ref = réf. photo)
 *   et depuis le lien CONTACT du menu (.nav-contact-modal)
 * - Pré-remplit le champ "ref" si une référence est passée
 * - Gère le focus trap (accessibilité clavier)
 * - Ferme avec ✕, Échap ou clic sur l'overlay
 *
 * Le formulaire est géré par Contact Form 7.
 *
 * Expose window.motaBindContactButtons(scope) pour gallery.js.
 */
document.addEventListener('DOMContentLoaded', () => {

  const modal = document.getElementById('contact-modal');

  const focusableSelectors = 'a, button, textarea, input, select, [tabindex]:not([tabindex="-1"])';
  let focusableEls, firstFocusableEl, lastFocusableEl;
  let lastTriggerBtn = null;

  function openModal(e) {
    if (!modal) return;
    lastTriggerBtn = e.currentTarget;

    // Pré-remplir le champ "ref" si disponible (champ CF7 nommé "ref")
    const photoRef = lastTriggerBtn.getAttribute('data-ref');
    const refField = modal.querySelector('input[name="ref"]');
    if (photoRef && refField) refField.value = photoRef;

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    focusableEls     = modal.querySelectorAll(focusableSelectors);
    firstFocusableEl = focusableEls[0];
    lastFocusableEl  = focusableEls[focusableEls.length - 1];
    if (firstFocusableEl) firstFocusableEl.focus();
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (lastTriggerBtn) lastTriggerBtn.focus();
  }

  modal?.querySelector('.contact-modal__close')?.addEventListener('click', closeModal);

  modal?.addEventListener('click', (e) => {
    if (!e.target.closest('.contact-modal__panel')) closeModal();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (modal?.classList.contains('is-open'))  closeModal();
      if (window.motaLightboxIsOpen?.())         window.motaCloseLightbox?.();
    }
    if (e.key === 'Tab' && modal?.classList.contains('is-open')) {
      if (!firstFocusableEl || !lastFocusableEl) return;
      if (e.shiftKey && document.activeElement === firstFocusableEl) {
        e.preventDefault();
        lastFocusableEl.focus();
      } else if (!e.shiftKey && document.activeElement === lastFocusableEl) {
        e.preventDefault();
        firstFocusableEl.focus();
      }
    }
  });

  function bindContactButtons(scope) {
    scope.querySelectorAll('.contact-button').forEach(btn => {
      if (btn.dataset.boundContact) return;
      btn.dataset.boundContact = '1';
      btn.addEventListener('click', openModal);
    });
  }

  bindContactButtons(document);

  document.querySelector('.nav-contact-modal')
    ?.addEventListener('click', (e) => { e.preventDefault(); openModal(e); });

  window.motaBindContactButtons = bindContactButtons;

});
