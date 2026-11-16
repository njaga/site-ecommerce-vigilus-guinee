const storageKey = 'vigilus-display-currency';

export function formatPrice(amount, currency = 'GNF', rates = { GNF: 1 }) {
    if (amount === null || amount === undefined || amount === '' || !Number.isFinite(Number(amount))) return 'Prix sur devis';
    const validCurrency = ['GNF', 'USD', 'EUR'].includes(currency)
        && Number.isFinite(Number(rates[currency])) && Number(rates[currency]) > 0;
    const code = validCurrency ? currency : 'GNF';
    const converted = Number(amount) * (code === 'GNF' ? 1 : Number(rates[code]));
    const formatted = new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: code === 'GNF' ? 0 : 2,
        maximumFractionDigits: code === 'GNF' ? 0 : 2,
    }).format(converted);
    return (code === 'GNF' ? '' : '≈ ') + formatted + ' ' + code;
}

const controls = typeof document === 'undefined' ? [] : [...document.querySelectorAll('[data-currency-select]')];
const settings = typeof document === 'undefined' ? null : document.querySelector('[data-currency-rates]');
let rates = { GNF: 1 };
try { rates = { ...rates, ...JSON.parse(settings?.dataset.currencyRates || '{}') }; } catch { /* Keep GNF on invalid configuration. */ }
let current = 'GNF';
try { if (settings) current = localStorage.getItem(storageKey) || 'GNF'; } catch { /* Storage may be unavailable. */ }
const available = (code) => ['GNF', 'USD', 'EUR'].includes(code) && Number.isFinite(Number(rates[code])) && Number(rates[code]) > 0;
if (!available(current)) current = 'GNF';

export function displayPrice(amount) { return formatPrice(amount, current, rates); }

function refreshPrices() {
    document.querySelectorAll('[data-price-gnf]').forEach((el) => {
        el.textContent = displayPrice(el.dataset.priceGnf);
        if (current !== 'GNF') el.title = 'Montant de référence : ' + formatPrice(el.dataset.priceGnf);
        else el.removeAttribute('title');
    });
    controls.forEach((el) => {
        el.checked = el.value === current;
        if (el.checked) {
            document.querySelectorAll('[data-currency-flag]').forEach((flag) => { flag.src = el.dataset.flagSrc; });
        }
    });
    document.querySelectorAll('[data-currency-label]').forEach((el) => { el.textContent = current + (current === 'GNF' ? '' : ' ≈'); });
    document.querySelectorAll('[data-currency-notice]').forEach((el) => { el.hidden = current === 'GNF'; });
}

if (settings && controls.length) {
    controls.forEach((control) => {
        control.addEventListener('change', () => {
            current = available(control.value) ? control.value : 'GNF';
            try { localStorage.setItem(storageKey, current); } catch { /* Selection still works for this page. */ }
            refreshPrices();
        });
    });
    document.querySelectorAll('[data-currency-picker]').forEach((el) => { el.hidden = false; });
    refreshPrices();
}
