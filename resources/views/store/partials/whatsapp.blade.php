@php $whatsappUrl = app(\App\Support\ContactChannels::class)->whatsappUrl(); @endphp
<details class="whatsapp-widget" data-dismissable-details>
    <summary aria-label="Contacter Vigilus sur WhatsApp">
        <svg aria-hidden="true" width="25" height="25" viewBox="0 0 24 24" fill="currentColor"><path d="M20.5 3.5A11.9 11.9 0 0 0 12 0C5.4 0 .1 5.3.1 11.9c0 2.1.6 4.2 1.6 6L0 24l6.3-1.6a12 12 0 0 0 5.7 1.4c6.6 0 12-5.3 12-11.9 0-3.2-1.3-6.2-3.5-8.4ZM12 21.8a10 10 0 0 1-5-1.4l-.4-.2-3.7 1 1-3.6-.3-.4A9.9 9.9 0 0 1 2.1 12C2.1 6.5 6.5 2 12 2s10 4.4 10 9.9-4.5 9.9-10 9.9Zm5.4-7.4c-.3-.1-1.8-.9-2.1-1s-.5-.1-.7.2-.8 1-.9 1.2-.3.2-.6.1a8.2 8.2 0 0 1-4-3.5c-.2-.3 0-.4.1-.6l.5-.6.3-.5c.1-.2 0-.4 0-.5l-1-2.3c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4s-1 1-1 2.4 1 2.8 1.2 3 2.1 3.2 5.1 4.5c1.9.8 2.6.9 3.5.7.6-.1 1.8-.8 2-1.5s.3-1.3.2-1.5-.3-.2-.6-.4Z"/></svg>
        <span>WhatsApp</span>
    </summary>
    <div class="whatsapp-panel">
        <button type="button" class="whatsapp-close" data-close-details aria-label="Fermer le panneau WhatsApp"><x-store-icon name="close" :size="18"/></button>
        <p class="section-eyebrow">Vigilus Guinée</p>
        <h2>Un projet en tête ?</h2>
        @if($whatsappUrl)
            <p>Échangez avec notre équipe sur vos équipements et vos besoins.</p>
            <a class="whatsapp-action" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">Ouvrir WhatsApp <x-store-icon name="arrow-right" :size="17"/><span class="sr-only"> (nouvel onglet)</span></a>
        @else
            <p>Notre numéro WhatsApp sera bientôt disponible.</p>
            <span class="whatsapp-pending">+224 · numéro à compléter</span>
            <a class="btn-red" href="{{ route('quote.create') }}">Nous transmettre votre besoin <x-store-icon name="arrow-right" :size="16"/></a>
        @endif
        <a class="whatsapp-contact-link" href="{{ route('contact') }}">Toutes nos coordonnées</a>
    </div>
</details>
