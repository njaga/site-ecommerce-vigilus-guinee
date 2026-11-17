const panels = [...document.querySelectorAll('[data-dismissable-details]')];
const close = (panel, restoreFocus = false) => {
    panel.open = false;
    if (restoreFocus) panel.querySelector('summary')?.focus();
};

document.addEventListener('click', (event) => {
    for (const panel of panels) {
        if (!panel.contains(event.target)) close(panel);
        else if (event.target.closest('[data-close-details]')) close(panel, true);
        // Keep radio arrow-key navigation open; a pointer selection closes the picker.
        else if (event.target.matches('[data-currency-select]') && event.detail > 0) close(panel, true);
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    for (const panel of panels) {
        if (panel.open) close(panel, panel.contains(document.activeElement));
    }
});

for (const panel of panels) {
    panel.addEventListener('focusout', (event) => {
        if (event.relatedTarget && !panel.contains(event.relatedTarget)) close(panel);
    });
}
