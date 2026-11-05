const dialog = document.querySelector('[data-search-dialog]');

if (dialog && typeof dialog.showModal === 'function') {
    const triggers = [...document.querySelectorAll('[data-open-search]')];
    const input = dialog.querySelector('[data-search-input]');
    const form = dialog.querySelector('form[role="search"]');
    const results = dialog.querySelector('[data-search-results]');
    const introTemplate = results.innerHTML;
    const closeButton = dialog.querySelector('[data-close-search]');
    const endpoint = dialog.dataset.suggestionsUrl;

    let debounceTimer = null;
    let animationTimer = null;
    let controller = null;
    let sequence = 0;

    const stopPending = () => {
        clearTimeout(debounceTimer);
        if (controller) controller.abort();
        controller = null;
        sequence += 1;
    };

    const openSearch = () => {
        clearTimeout(animationTimer);
        delete dialog.dataset.closing;
        if (!dialog.open) dialog.showModal();
        requestAnimationFrame(() => input.focus());
    };

    const closeSearch = () => {
        if (!dialog.open || dialog.dataset.closing) return;
        stopPending();
        dialog.dataset.closing = 'true';
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        animationTimer = setTimeout(() => {
            dialog.close();
            delete dialog.dataset.closing;
        }, reducedMotion ? 0 : 200);
    };

    const textElement = (tag, className, value) => {
        const el = document.createElement(tag);
        if (className) el.className = className;
        el.textContent = value;
        return el;
    };

    const showMessage = (message, status = 'idle') => {
        results.replaceChildren();
        const el = textElement('p', 'search-result-message', message);
        el.dataset.state = status;
        results.append(el);
    };

    const makeHeading = (name) => textElement('h3', 'search-result-heading', name);

    const makeResult = (entry, isCategory = false) => {
        const li = document.createElement('li');
        const link = document.createElement('a');
        link.className = 'search-result-link';
        link.href = entry.url;
        link.dataset.suggestionLink = '';

        const media = document.createElement('span');
        media.className = 'search-result-media';

        if (!isCategory && entry.image) {
            const img = document.createElement('img');
            img.src = entry.image;
            img.alt = '';
            img.width = 58;
            img.height = 58;
            img.loading = 'lazy';
            media.append(img);
        } else {
            // Category/media placeholder is CSS-only, never a fake product photo.
            media.append(textElement('span', 'search-result-media-symbol', isCategory ? '▤' : '◈'));
        }

        const copy = document.createElement('span');
        copy.className = 'search-result-copy';
        copy.append(textElement('strong', '', entry.name));
        if (!isCategory) {
            copy.append(textElement('small', '', [entry.sku, entry.category].filter(Boolean).join(' · ')));
        } else {
            copy.append(textElement('small', '', 'Voir cette catégorie'));
        }
        link.append(media, copy);

        if (!isCategory) {
            const detail = entry.price_on_request || entry.price_gnf === null
                ? 'Sur devis'
                : new Intl.NumberFormat('fr-FR').format(entry.price_gnf) + ' GNF';
            link.append(textElement('span', 'search-result-price', detail));
        } else {
            link.append(textElement('span', 'search-result-end', '↗'));
        }

        li.append(link);
        return li;
    };

    const section = (title, data, isCategory = false) => {
        const fragment = document.createDocumentFragment();
        fragment.append(makeHeading(title));
        const list = document.createElement('ul');
        list.className = 'search-result-list';
        for (const item of data) list.append(makeResult(item, isCategory));
        fragment.append(list);
        return fragment;
    };

    const renderSuggestions = (payload, query) => {
        results.replaceChildren();
        const products = Array.isArray(payload.products) ? payload.products : [];
        const categories = Array.isArray(payload.categories) ? payload.categories : [];

        if (!products.length && !categories.length) {
            showMessage('Aucun résultat pour « ' + query + ' ». Essayez un autre terme ou consultez le catalogue.');
        } else {
            if (categories.length) results.append(section('Catégories', categories, true));
            if (products.length) results.append(section('Produits correspondants', products));
        }

        const viewAll = document.createElement('a');
        viewAll.className = 'search-result-view-all';
        viewAll.href = dialog.dataset.catalogUrl + '?q=' + encodeURIComponent(query);
        viewAll.textContent = 'Voir tous les résultats pour « ' + query + ' »';
        results.append(viewAll);
    };

    const fetchSuggestions = async (term, ticket) => {
        controller = new AbortController();
        try {
            const url = endpoint + '?q=' + encodeURIComponent(term);
            const response = await fetch(url, {
                signal: controller.signal,
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (!response.ok) throw new Error('suggestions-unavailable');
            const payload = await response.json();
            if (ticket !== sequence || input.value.trim() !== term || !dialog.open) return;
            renderSuggestions(payload, term);
        } catch (error) {
            if (error.name === 'AbortError' || ticket !== sequence) return;
            showMessage('Suggestions momentanément indisponibles. Appuyez sur Entrée pour rechercher dans le catalogue.', 'error');
        }
    };

    const updateSuggestions = () => {
        stopPending();
        const term = input.value.trim();
        if (term.length < 2) {
            results.innerHTML = introTemplate;
            return;
        }
        showMessage('Recherche en cours…', 'loading');
        const ticket = sequence;
        debounceTimer = setTimeout(() => fetchSuggestions(term, ticket), 220);
    };

    for (const trigger of triggers) {
        trigger.addEventListener('click', openSearch);
    }

    closeButton?.addEventListener('click', closeSearch);
    dialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeSearch();
    });
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) closeSearch();
    });
    dialog.addEventListener('close', () => {
        stopPending();
        input.value = '';
        results.innerHTML = introTemplate;
        delete dialog.dataset.closing;
    });

    input.addEventListener('input', updateSuggestions);
    input.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown') {
            const link = results.querySelector('[data-suggestion-link]');
            if (link) {
                event.preventDefault();
                link.focus();
            }
        }
    });

    results.addEventListener('keydown', (event) => {
        if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;
        const links = [...results.querySelectorAll('[data-suggestion-link]')];
        const current = links.indexOf(document.activeElement);
        if (current === -1) return;
        event.preventDefault();
        const next = current + (event.key === 'ArrowDown' ? 1 : -1);
        if (next < 0) input.focus();
        else (links[next] || links[links.length - 1]).focus();
    });

    form.addEventListener('submit', () => stopPending());

    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            openSearch();
        }
        if (event.key === '/' && !dialog.open &&
            !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName) &&
            !document.activeElement?.isContentEditable) {
            event.preventDefault();
            openSearch();
        }
    });
} else {
    // Older browsers: keep the catalogue accessible via existing navigation.
    for (const button of document.querySelectorAll('[data-open-search]')) {
        button.addEventListener('click', () => {
            window.location.assign(button.dataset.fallbackUrl || '/produits');
        });
    }
}
