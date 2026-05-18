/**
 * gallery.js — Galerie AJAX : filtres, tri et pagination
 *
 * Gère la galerie photos de la page d'accueil :
 *   - Peuple les <select> de filtres (catégories, formats) via AJAX
 *   - Recharge la galerie en AJAX au changement de filtre ou de tri
 *   - Charge 8 photos supplémentaires au clic sur "CHARGER PLUS"
 *
 * Actions WordPress AJAX utilisées :
 *   mota_get_filters  — retourne { categories: [], formats: [] }
 *   mota_load_photos  — retourne { html: "...", hasMore: bool }
 *
 * Requiert :
 *   window.motaAjax.ajaxUrl / .nonce  (localisé depuis functions.php)
 *   window.motaBindContactButtons      (exposé par contact.js)
 *
 * Éléments HTML attendus (dans front-page.php) :
 *   #photo-gallery    — conteneur de la grille
 *   #filter-category  — select catégorie
 *   #filter-format    — select format
 *   #sort-by          — select tri
 *   #load-more        — bouton "CHARGER PLUS"
 */
document.addEventListener('DOMContentLoaded', () => {

  const ajaxUrl   = window.motaAjax?.ajaxUrl ?? '/wp-admin/admin-ajax.php';
  const ajaxNonce = window.motaAjax?.nonce   ?? '';

  const gallery     = document.getElementById('photo-gallery');
  const loadMoreBtn = document.getElementById('load-more');
  const selCat      = document.getElementById('filter-category');
  const selFormat   = document.getElementById('filter-format');
  const selSort     = document.getElementById('sort-by');

  // Quitte si la galerie n'existe pas sur cette page (ex. page single, contact…)
  if (!gallery) return;

  // État courant des filtres et de la pagination
  let paged = 1;
  const state = { cat: '', format: '', sort: 'date_desc' };

  // ── Chargement des photos ─────────────────────────────────────────────────

  /**
   * Récupère un lot de photos depuis le serveur et les injecte dans la galerie.
   * @param {boolean} reset — true = vide la galerie avant d'insérer (changement de filtre)
   *                          false = ajoute à la suite (pagination)
   */
  async function fetchPhotos({ reset }) {
    const data = new FormData();
    data.append('action', 'mota_load_photos');
    data.append('nonce',  ajaxNonce);
    data.append('paged',  String(paged));
    data.append('cat',    state.cat);
    data.append('format', state.format);
    data.append('sort',   state.sort);

    const json = await fetch(ajaxUrl, { method: 'POST', body: data })
      .then(r => r.json())
      .catch(() => null);

    if (!json) return;

    if (reset) {
      gallery.innerHTML = json.html ?? '';   // Repart de zéro (nouveau filtre)
    } else {
      gallery.insertAdjacentHTML('beforeend', json.html ?? ''); // Ajoute à la suite
    }

    // Lie les boutons CONTACT sur les nouvelles photos chargées
    window.motaBindContactButtons?.(gallery);

    // Masque "CHARGER PLUS" si toutes les photos sont déjà affichées
    if (loadMoreBtn) loadMoreBtn.style.display = json.hasMore ? 'inline-flex' : 'none';
  }

  // ── Filtres ───────────────────────────────────────────────────────────────

  /**
   * Appelée à chaque changement de filtre ou de tri.
   * Remet la pagination à 1 et recharge la galerie depuis le début.
   */
  function onFilterChange() {
    paged = 1;
    state.cat    = selCat?.value    || '';
    state.format = selFormat?.value || '';
    state.sort   = selSort?.value   || 'date_desc';
    fetchPhotos({ reset: true });
  }

  // Écoute les changements sur les trois selects
  [selCat, selFormat, selSort].forEach(sel => sel?.addEventListener('change', onFilterChange));

  // ── Pagination ────────────────────────────────────────────────────────────

  // Bouton "CHARGER PLUS" : incrémente la page et ajoute les nouvelles photos
  loadMoreBtn?.addEventListener('click', () => {
    paged += 1;
    fetchPhotos({ reset: false });
  });

  // ── Peuplement des selects ────────────────────────────────────────────────

  /**
   * Charge les catégories et formats disponibles depuis le serveur
   * et peuple les <select> correspondants.
   */
  async function populateFilters() {
    if (!selCat && !selFormat) return;

    const data = new FormData();
    data.append('action', 'mota_get_filters');
    data.append('nonce',  ajaxNonce);

    const json = await fetch(ajaxUrl, { method: 'POST', body: data })
      .then(r => r.json())
      .catch(() => null);

    if (!json) return;

    // Ajoute les options de catégories au select
    json.categories?.forEach(({ slug, name }) =>
      selCat?.insertAdjacentHTML('beforeend', `<option value="${slug}">${name}</option>`)
    );

    // Ajoute les options de formats au select
    json.formats?.forEach(({ slug, name }) =>
      selFormat?.insertAdjacentHTML('beforeend', `<option value="${slug}">${name}</option>`)
    );
  }

  populateFilters();

});
