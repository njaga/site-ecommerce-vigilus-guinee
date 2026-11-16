<div class="contact-details">
    @foreach(['phone' => 'Téléphone 1', 'phone_secondary' => 'Téléphone 2'] as $key => $label)
        <div><span class="contact-label">{{ $label }}</span>
            @if(config('site.'.$key))
                <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('site.'.$key)) }}">{{ config('site.'.$key) }}</a>
            @else
                <span class="contact-placeholder">+224 XXX XX XX XX <small>À venir</small></span>
            @endif
        </div>
    @endforeach
    <div><span class="contact-label">Email</span>
        @if(config('site.email'))<a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>
        @else<span class="contact-placeholder">Adresse email à venir</span>@endif
    </div>
    <div><span class="contact-label">Adresse</span><span>{{ config('site.address') ?: 'Conakry, Guinée · adresse précise à venir' }}</span></div>
</div>
