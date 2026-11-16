import test from 'node:test';
import assert from 'node:assert/strict';
import { formatPrice } from '../resources/js/currency.js';

const rates = { GNF: 1, USD: 0.00011, EUR: 0.00010 };
const normalize = (value) => value.replace(/[\u00a0\u202f]/g, ' ');

test('user-supplied rates convert GNF to indicative USD and EUR', () => {
    assert.equal(normalize(formatPrice(1000000, 'GNF', rates)), '1 000 000 GNF');
    assert.equal(formatPrice(1000000, 'USD', rates), '≈ 110,00 USD');
    assert.equal(formatPrice(1000000, 'EUR', rates), '≈ 100,00 EUR');
    assert.equal(formatPrice(123456, 'USD', rates), '≈ 13,58 USD');
});

test('zero remains a number, unpriced products remain on quote', () => {
    assert.equal(formatPrice(0, 'EUR', rates), '≈ 0,00 EUR');
    for (const value of [null, undefined, '', NaN, 'invalid']) {
        assert.equal(formatPrice(value, 'USD', rates), 'Prix sur devis');
    }
});

test('unsupported currencies and invalid rates fall back to GNF', () => {
    for (const [currency, invalid] of [['CAD', rates], ['USD', { USD: 0 }], ['EUR', { EUR: -1 }], ['EUR', { EUR: Infinity }]]) {
        assert.equal(normalize(formatPrice(100000, currency, invalid)), '100 000 GNF');
    }
});

test('switching currency persists preference and never changes the original amount', async () => {
    const originalDocument = Object.getOwnPropertyDescriptor(globalThis, 'document');
    const originalStorage = Object.getOwnPropertyDescriptor(globalThis, 'localStorage');
    const changes = {};
    const picker = { hidden: true };
    const flag = { src: '' };
    const label = { textContent: '' };
    let stored = 'USD';
    const controls = ['GNF', 'USD', 'EUR'].map(value => ({ value, checked: false, dataset: { flagSrc: value + '.svg' }, addEventListener: (_, callback) => { changes[value] = callback; } }));
    const amount = { dataset: { priceGnf: '1000000' }, textContent: '', removeAttribute: () => {} };
    const notice = { hidden: true };
    Object.defineProperty(globalThis, 'localStorage', { configurable: true, value: {
        getItem: () => stored, setItem: (_, value) => { stored = value; },
    } });
    Object.defineProperty(globalThis, 'document', { configurable: true, value: {
        querySelector: () => ({ dataset: { currencyRates: JSON.stringify(rates) } }),
        querySelectorAll: (selector) => ({ '[data-currency-select]': controls, '[data-currency-picker]': [picker], '[data-currency-flag]': [flag], '[data-currency-label]': [label], '[data-price-gnf]': [amount], '[data-currency-notice]': [notice] })[selector],
    } });
    try {
        await import('../resources/js/currency.js?switch-test');
        assert.equal(picker.hidden, false);
        assert.equal(controls[1].checked, true);
        assert.equal(flag.src, 'USD.svg');
        assert.equal(label.textContent, 'USD ≈');
        assert.equal(amount.textContent, '≈ 110,00 USD');
        assert.equal(notice.hidden, false);
        changes.EUR();
        assert.equal(controls[2].checked, true);
        assert.equal(controls[1].checked, false);
        assert.equal(flag.src, 'EUR.svg');
        assert.equal(stored, 'EUR');
        assert.equal(amount.textContent, '≈ 100,00 EUR');
        assert.equal(amount.dataset.priceGnf, '1000000');
        changes.GNF();
        assert.equal(flag.src, 'GNF.svg');
        assert.equal(label.textContent, 'GNF');
        assert.equal(normalize(amount.textContent), '1 000 000 GNF');
        assert.equal(notice.hidden, true);
    } finally {
        if (originalDocument) Object.defineProperty(globalThis, 'document', originalDocument); else delete globalThis.document;
        if (originalStorage) Object.defineProperty(globalThis, 'localStorage', originalStorage); else delete globalThis.localStorage;
    }
});
