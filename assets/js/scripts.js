document.addEventListener('DOMContentLoaded', () => {

  const modal = document.getElementById('contact-modal');
  const openBtn = document.querySelector('.contact-button');
  const closeBtn = modal.querySelector('.contact-modal__close');
  const focusableSelectors = 'a, button, textarea, input, select, [tabindex]:not([tabindex="-1"])';
  let focusableEls;
  let firstFocusableEl;
  let lastFocusableEl;

  function openModal() {
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    // Focus trap
    focusableEls = modal.querySelectorAll(focusableSelectors);
    firstFocusableEl = focusableEls[0];
    lastFocusableEl = focusableEls[focusableEls.length -1];
    firstFocusableEl.focus();
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    openBtn.focus();
  }

  openBtn.addEventListener('click', openModal);
  closeBtn.addEventListener('click', closeModal);

  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('is-open')) {
      closeModal();
    }

    // Focus trap
    if (e.key === 'Tab' && modal.classList.contains('is-open')) {
      if (e.shiftKey) { // shift + tab
        if (document.activeElement === firstFocusableEl) {
          e.preventDefault();
          lastFocusableEl.focus();
        }
      } else { // tab
        if (document.activeElement === lastFocusableEl) {
          e.preventDefault();
          firstFocusableEl.focus();
        }
      }
    }
  });

});
