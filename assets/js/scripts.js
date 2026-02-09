  document.addEventListener('DOMContentLoaded', () => {
  const openBtn = document.getElementById('open-contact');
  const closeBtn = document.getElementById('close-contact');
  const modal = document.getElementById('contact-modal');

  if (!modal) return;

  openBtn?.addEventListener('click', () => {
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
  });

  closeBtn?.addEventListener('click', () => {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
  });
});