// Measure the real header height, including font loading and mobile wrapping,
// so sticky panels and in-page links remain below the navigation.
const header = document.querySelector('.store-header');
if (header) {
    const updateHeaderHeight = () => {
        document.documentElement.style.setProperty('--store-header-height', `${Math.ceil(header.getBoundingClientRect().height)}px`);
    };
    updateHeaderHeight();
    if ('ResizeObserver' in window) new ResizeObserver(updateHeaderHeight).observe(header);
    else window.addEventListener('resize', updateHeaderHeight, { passive: true });
}

const filters = document.querySelector('#catalog-filters');
if (filters) {
    const desktop = window.matchMedia('(min-width: 1024px)');
    let mobileOpen = filters.open;
    let wasDesktop = desktop.matches;
    const syncFilters = () => {
        if (desktop.matches && !wasDesktop) mobileOpen = filters.open;
        filters.open = desktop.matches || mobileOpen;
        wasDesktop = desktop.matches;
    };
    syncFilters();
    desktop.addEventListener('change', syncFilters);
}
