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

  // ==================== LIGHTBOX (avec navigation) ====================
const overlay = document.getElementById('lightbox-overlay');
const lbImg = document.getElementById('lightbox-img');
const lbTitle = document.getElementById('lightbox-title');
const lbRef = document.getElementById('lightbox-ref');

const btnClose = overlay ? overlay.querySelector('.lightbox-close') : null;
const btnPrev = overlay ? overlay.querySelector('.lightbox-prev') : null;
const btnNext = overlay ? overlay.querySelector('.lightbox-next') : null;

let lightboxItems = [];
let currentIndex = 0;

function rebuildLightboxItems() {
  lightboxItems = Array.from(document.querySelectorAll('.lightbox-open')).map(btn => ({
    src: btn.getAttribute('data-src') || '',
    title: btn.getAttribute('data-title') || '',
    ref: btn.getAttribute('data-ref') || ''
  })).filter(x => x.src);
}

function renderLightbox(index) {
  if (!overlay || !lbImg) return;
  if (!lightboxItems.length) return;

  currentIndex = (index + lightboxItems.length) % lightboxItems.length;

  const item = lightboxItems[currentIndex];
  lbImg.src = item.src;
  lbImg.alt = item.title || '';
  if (lbTitle) lbTitle.textContent = item.title || '';
  if (lbRef) lbRef.textContent = item.ref ? `REF. ${item.ref}` : '';
}

function openLightboxAt(index) {
  if (!overlay) return;
  rebuildLightboxItems();
  renderLightbox(index);
  overlay.classList.add('is-open');
  overlay.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  if (!overlay) return;
  overlay.classList.remove('is-open');
  overlay.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
  if (lbImg) lbImg.src = '';
}

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.lightbox-open');
  if (!btn) return;

  e.preventDefault();

  rebuildLightboxItems();
  const index = lightboxItems.findIndex(item => item.src === (btn.getAttribute('data-src') || ''));
  openLightboxAt(index >= 0 ? index : 0);
});

if (btnClose) btnClose.addEventListener('click', closeLightbox);
if (overlay) {
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) closeLightbox();
  });
}
if (btnPrev) btnPrev.addEventListener('click', () => renderLightbox(currentIndex - 1));
if (btnNext) btnNext.addEventListener('click', () => renderLightbox(currentIndex + 1));

document.addEventListener('keydown', (e) => {
  if (!overlay || !overlay.classList.contains('is-open')) return;

  if (e.key === 'Escape') closeLightbox();
  if (e.key === 'ArrowLeft') renderLightbox(currentIndex - 1);
  if (e.key === 'ArrowRight') renderLightbox(currentIndex + 1);
});
  

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