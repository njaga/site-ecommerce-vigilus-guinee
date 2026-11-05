@extends('layouts.admin')
@section('title', 'Demandes de devis')
@section('content')
<div class="flex flex-wrap justify-between gap-4 items-end mb-7">
    <div>
        <p class="kicker">Suivi commercial</p>
        <h1 class="text-3xl font-extrabold mt-2">Demandes de devis</h1>
        <p class="mt-2 text-quiet text-sm">Coordonnées et historiques réservés aux administrateurs habilités.</p>
    </div>
    <nav aria-label="Filtrer les devis" class="flex flex-wrap gap-2 text-sm">
        @foreach(['' => 'Tous', 'new' => 'Nouveaux', 'in_progress' => 'En cours', 'closed' => 'Clôturés'] as $key => $label)
            <a class="btn-light {{ $selectedStatus === $key ? 'border-rouge text-rouge' : '' }}"
               href="{{ route('admin.quotes.index', $key !== '' ? ['statut' => $key] : []) }}">{{ $label }}</a>
        @endforeach
    </nav>
</div>
<div class="grid gap-4">
@forelse($quotes as $quote)
<article id="devis-{{ $quote->id }}" class="bg-white border border-slate-200 rounded-xl p-6 grid lg:grid-cols-[minmax(0,1fr)_300px] gap-6">
    <div>
        <div class="flex flex-wrap gap-3 items-center text-xs text-quiet">
            <span class="font-bold">Devis #{{ $quote->id }}</span>
            <span>{{ $quote->created_at->format('d/m/Y H:i') }}</span>
            <span class="font-bold {{ $quote->status === 'closed' ? 'text-emerald-700' : 'text-rouge' }}">{{ match($quote->status) { 'new' => 'Nouveau', 'in_progress' => 'En cours', 'closed' => 'Clôturé', default => $quote->status } }}</span>
        </div>
        <h2 class="font-extrabold text-xl mt-3">{{ $quote->name }}</h2>
        @if($quote->company)<p class="text-sm text-quiet mt-1">{{ $quote->company }}</p>@endif
        <p class="text-sm mt-4"><strong>Téléphone :</strong> {{ $quote->phone }}
            @if($quote->email)<span class="block mt-1"><strong>Email :</strong> {{ $quote->email }}</span>@endif
        </p>
        <div class="mt-5 border-t border-slate-100 pt-5">
            <strong>{{ $quote->product?->sku ?: 'Projet / besoin général' }}</strong> · {{ $quote->quantity }} unité(s)
            @if($quote->message)<p class="text-sm text-quiet whitespace-pre-line mt-2">{{ $quote->message }}</p>@endif
        </div>
        @if($quote->reviewer)<p class="text-xs text-quiet mt-5">Dernière revue : {{ $quote->reviewer->name }} · {{ $quote->reviewed_at?->format('d/m/Y H:i') }}</p>@endif
    </div>
    <div class="bg-cloud border border-slate-100 rounded-lg p-5">
        <h3 class="font-bold mb-4">Traitement de la demande</h3>
        <form method="POST" action="{{ route('admin.quotes.update', $quote) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div><label class="input-label" for="status-{{ $quote->id }}">État</label>
                <select class="field" id="status-{{ $quote->id }}" name="status" required>
                    @foreach(['new' => 'Nouveau', 'in_progress' => 'En cours', 'closed' => 'Clôturé'] as $value => $label)
                        <option value="{{ $value }}" @selected($quote->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="input-label" for="note-{{ $quote->id }}">Note interne (jamais publique)</label>
                <textarea class="field" name="internal_note" id="note-{{ $quote->id }}" rows="3" maxlength="1500" placeholder="Relance prévue, disponibilité vérifiée…"></textarea>
            </div>
            <button type="submit" class="btn-red w-full">Enregistrer le suivi</button>
        </form>
        @if($quote->events()->exists())
            <details class="mt-5">
                <summary class="font-semibold text-sm cursor-pointer">Historique des actions</summary>
                <ol class="space-y-3 mt-3 text-xs text-quiet">
                    @foreach($quote->events()->with('actor')->get() as $event)
                        <li><strong class="text-ink">{{ $event->actor?->name ?: 'Compte désactivé' }}</strong> · {{ $event->created_at->format('d/m/Y H:i') }}
                            <span class="block">{{ $event->from_status }} → {{ $event->to_status }}</span>
                            @if($event->note)<p class="whitespace-pre-line mt-1">{{ $event->note }}</p>@endif
                        </li>
                    @endforeach
                </ol>
            </details>
        @endif
    </div>
</article>
@empty
    <p class="text-center py-12 border border-slate-200 rounded-xl text-quiet">Aucune demande avec ce filtre.</p>
@endforelse
</div>
<div class="mt-6">{{ $quotes->links() }}</div>
@endsection
