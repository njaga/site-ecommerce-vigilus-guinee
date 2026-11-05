import './search-overlay.js';

// Interaction progressive : le contenu et les liens restent accessibles sans JS.
document.addEventListener('click', (event) => {
    const thumb = event.target.closest('[data-gallery-thumb]');
    if (!thumb) return;
    const gallery = thumb.closest('[data-product-gallery]');
    if (!gallery) return;

    const main = gallery.querySelector('#gallery-main-image');
    const largeLink = gallery.querySelector('#gallery-large-link');
    if (!main || !largeLink || !thumb.dataset.gallerySrc) return;

    main.src = thumb.dataset.gallerySrc;
    main.alt = thumb.dataset.galleryAlt || main.alt;
    largeLink.href = thumb.dataset.gallerySrc;
    for (const button of gallery.querySelectorAll('[data-gallery-thumb]')) {
        button.setAttribute('aria-pressed', button === thumb ? 'true' : 'false');
    }
    const status = gallery.querySelector('#gallery-status');
    if (status) {
        const selected = [...gallery.querySelectorAll('[data-gallery-thumb]')].indexOf(thumb);
        status.textContent = 'Photographie ' + (selected + 1) + ' affichée';
    }
});
