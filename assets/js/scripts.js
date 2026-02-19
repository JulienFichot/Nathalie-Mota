document.addEventListener('DOMContentLoaded', () => {

  // ==================== SAFE: motaAjax ====================
  // motaAjax doit être défini par wp_localize_script dans functions.php
  const ajaxUrl = (window.motaAjax && window.motaAjax.ajaxUrl) ? window.motaAjax.ajaxUrl : '/wp-admin/admin-ajax.php';
  const ajaxNonce = (window.motaAjax && window.motaAjax.nonce) ? window.motaAjax.nonce : '';

  // ==================== MODALE CONTACT ====================
  const modal = document.getElementById('contact-modal');
  const focusableSelectors = 'a, button, textarea, input, select, [tabindex]:not([tabindex="-1"])';
  let focusableEls, firstFocusableEl, lastFocusableEl;
  let lastTriggerBtn = null;

  function openModal(e) {
    if (!modal) return;
    lastTriggerBtn = e.currentTarget;

    const photoRef = lastTriggerBtn.getAttribute('data-ref');
    const refField = modal.querySelector('#ref-photo');
    if (photoRef && refField) refField.value = photoRef;

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    focusableEls = modal.querySelectorAll(focusableSelectors);
    firstFocusableEl = focusableEls[0];
    lastFocusableEl = focusableEls[focusableEls.length - 1];
    if (firstFocusableEl) firstFocusableEl.focus();
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (lastTriggerBtn) lastTriggerBtn.focus();
  }

  function bindContactButtons(scope) {
    scope.querySelectorAll('.contact-button').forEach(btn => {
      if (btn.dataset.boundContact) return;
      btn.dataset.boundContact = '1';
      btn.addEventListener('click', openModal);
    });
  }

  const closeBtn = modal ? modal.querySelector('.contact-modal__close') : null;
  if (closeBtn) closeBtn.addEventListener('click', closeModal);

  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) {
      closeModal();
    }

    if (e.key === 'Tab' && modal && modal.classList.contains('is-open')) {
      if (!firstFocusableEl || !lastFocusableEl) return;

      if (e.shiftKey) {
        if (document.activeElement === firstFocusableEl) {
          e.preventDefault();
          lastFocusableEl.focus();
        }
      } else {
        if (document.activeElement === lastFocusableEl) {
          e.preventDefault();
          firstFocusableEl.focus();
        }
      }
    }
  });

  // Bind initial contact buttons
  bindContactButtons(document);

  // ==================== LIGHTBOX ====================
  const lightboxOverlay = document.getElementById('lightbox-overlay');
  const lightboxImage = lightboxOverlay ? lightboxOverlay.querySelector('img') : null;

  function openLightboxFromImg(img) {
    if (!lightboxOverlay || !lightboxImage) return;
    lightboxImage.src = img.src;
    lightboxOverlay.style.display = 'flex';
  }

  function bindLightbox(scope) {
    scope.querySelectorAll('.lightbox-trigger').forEach(img => {
      if (img.dataset.boundLb) return;
      img.dataset.boundLb = '1';
      img.addEventListener('click', () => openLightboxFromImg(img));
    });

    // bouton plein écran (si présent dans ton HTML)
    scope.querySelectorAll('.fullscreen-button, .lightbox-open').forEach(btn => {
      if (btn.dataset.boundFs) return;
      btn.dataset.boundFs = '1';
      btn.addEventListener('click', () => {
        const card = btn.closest('.photo-block');
        const img = card ? card.querySelector('.lightbox-trigger') : null;
        if (img) openLightboxFromImg(img);
      });
    });
  }

  bindLightbox(document);

  if (lightboxOverlay) {
    lightboxOverlay.addEventListener('click', () => {
      lightboxOverlay.style.display = 'none';
      if (lightboxImage) lightboxImage.src = '';
    });
  }

  // ==================== PAGINATION AJAX (8 par clic via mota_load_photos) ====================
  let paged = 1;
  const loadMoreBtn = document.getElementById('load-more');
  const gallery = document.getElementById('photo-gallery');

  // Filtres (si présents)
  const selCat = document.getElementById('filter-category');
  const selFormat = document.getElementById('filter-format');
  const selSort = document.getElementById('sort-by');

  const state = {
    cat: '',
    format: '',
    sort: 'date_desc',
  };

  async function fetchPhotos({ reset }) {
    if (!gallery) return;

    const data = new FormData();
    data.append('action', 'mota_load_photos'); // doit matcher functions.php
    data.append('nonce', ajaxNonce);
    data.append('paged', String(paged));
    data.append('cat', state.cat);
    data.append('format', state.format);
    data.append('sort', state.sort);

    const res = await fetch(ajaxUrl, { method: 'POST', body: data });
    const json = await res.json();

    const html = (json && json.html) ? json.html : '';

    if (reset) gallery.innerHTML = html;
    else gallery.insertAdjacentHTML('beforeend', html);

    // re-bind sur le nouveau HTML
    bindLightbox(gallery);
    bindContactButtons(gallery);

    if (loadMoreBtn) {
      loadMoreBtn.style.display = json && json.hasMore ? 'inline-flex' : 'none';
    }
  }

  function onFilterChange() {
    paged = 1;
    state.cat = selCat ? selCat.value : '';
    state.format = selFormat ? selFormat.value : '';
    state.sort = selSort ? selSort.value : 'date_desc';
    fetchPhotos({ reset: true });
  }

  if (selCat) selCat.addEventListener('change', onFilterChange);
  if (selFormat) selFormat.addEventListener('change', onFilterChange);
  if (selSort) selSort.addEventListener('change', onFilterChange);

  if (loadMoreBtn && gallery) {
    loadMoreBtn.addEventListener('click', () => {
      paged += 1;
      fetchPhotos({ reset: false });
    });
  }

});