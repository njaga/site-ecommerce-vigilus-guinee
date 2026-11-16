<details class="currency-picker" data-currency-picker data-dismissable-details hidden>
    <summary aria-label="Choisir la devise d’affichage"><img data-currency-flag src="{{ asset('images/flags/gn.svg') }}" alt="" width="23" height="16"><span data-currency-label>GNF</span><x-store-icon name="chevron-down" :size="13"/></summary>
    <fieldset class="currency-options">
        <legend class="sr-only">Devise d’affichage des prix</legend>
        @foreach(['GNF' => ['gn', 'Franc guinéen'], 'USD' => ['us', 'Dollar américain'], 'EUR' => ['eu', 'Euro']] as $code => [$flag, $label])
            <label><input type="radio" name="display_currency" data-currency-select value="{{ $code }}" data-flag-src="{{ asset('images/flags/'.$flag.'.svg') }}" @checked($code === 'GNF')><img src="{{ asset('images/flags/'.$flag.'.svg') }}" alt="" width="26" height="18"><span><strong>{{ $code }}{{ $code !== 'GNF' ? ' ≈' : '' }}</strong><small>{{ $label }}</small></span></label>
        @endforeach
        <p>USD et EUR : montants indicatifs.</p>
    </fieldset>
</details>
