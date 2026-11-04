@extends('layouts.admin')
@section('title', 'Revue import BOQ #'.$batch->id)
@section('content')
<nav class="text-sm text-quiet mb-5"><a href="{{ route('admin.imports.index') }}" class="hover:text-rouge">← Tous les imports</a></nav>
<div class="flex flex-wrap justify-between gap-5 items-end">
    <div>
        <p class="kicker">Revue privée</p>
        <h1 class="text-3xl font-extrabold mt-2">Import BOQ #{{ $batch->id }}</h1>
        <p class="mt-3 text-quiet">Références techniques en attente de validation — {{ $batch->total_rows }} ligne(s) détectée(s).</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn-light">Voir les produits importés</a>
</div>
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-8">
    @foreach([
        'pending' => ['À examiner', 'text-ink'],
        'approved' => ['Brouillons créés', 'text-emerald-700'],
        'duplicate' => ['Doublons', 'text-amber-700'],
        'invalid' => ['Incomplètes', 'text-rouge'],
        'skipped' => ['Ignorées', 'text-quiet'],
    ] as $key => [$label, $class])
        <div class="bg-white rounded-xl border border-slate-200 px-4 py-5">
            <p class="text-xs text-quiet">{{ $label }}</p>
            <p class="text-2xl font-extrabold mt-1 {{ $class }}">{{ $counts[$key] ?? 0 }}</p>
        </div>
    @endforeach
</div>
<div class="border border-amber-200 bg-amber-50 rounded-xl px-5 py-4 mt-7 text-sm text-amber-950">
    <strong>Aucune vente automatique.</strong> L'approbation crée un brouillon non visible publiquement. Les quantités du BOQ ne sont pas le stock. Les prix d'achat du fournisseur ne sont jamais copiés dans les fiches.
</div>
<nav class="flex flex-wrap gap-2 mt-7 mb-5 text-sm font-semibold" aria-label="Filtrer les lignes">
    @foreach(['' => 'Toutes', 'pending' => 'À examiner', 'approved' => 'Approuvées', 'duplicate' => 'Doublons', 'invalid' => 'Incomplètes', 'skipped' => 'Ignorées'] as $key => $label)
        <a href="{{ route('admin.imports.show', $batch) . ($key !== '' ? '?statut='.$key : '') }}"
           class="px-3 py-2 rounded-lg border {{ $selectedStatus === $key ? 'bg-ink text-white border-ink' : 'bg-white border-slate-200 hover:border-rouge' }}">{{ $label }}</a>
    @endforeach
</nav>
<div class="space-y-4">
    @forelse($rows as $row)
        <article class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6">
            <div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(260px,320px)] gap-5">
                <div>
                    <div class="flex flex-wrap gap-2 items-center text-xs font-semibold">
                        <span class="bg-cloud rounded px-2 py-1">Ligne Excel {{ $row->source_row }}</span>
                        <span class="bg-cloud rounded px-2 py-1">{{ $row->source_category }}</span>
                        <span class="{{ $row->status === 'approved' ? 'text-emerald-700' : ($row->status === 'duplicate' || $row->status === 'invalid' ? 'text-rouge' : 'text-quiet') }}">
                            {{ match($row->status) { 'pending' => 'À examiner', 'approved' => 'Brouillon créé', 'duplicate' => 'Doublon', 'invalid' => 'Incomplète', 'skipped' => 'Ignorée', default => 'Statut inconnu' } }}
                        </span>
                    </div>
                    <h2 class="text-lg font-extrabold mt-3">{{ $row->description ?: 'Description absente' }}</h2>
                    <p class="text-sm mt-2"><strong>Modèle externe :</strong> {{ $row->external_model ?: 'À vérifier' }}</p>
                    @if($row->internal_model)<p class="text-xs text-quiet mt-1"><strong>Modèle interne fournisseur :</strong> {{ $row->internal_model }}</p>@endif
                    @if($row->part_number)<p class="text-xs text-quiet mt-1"><strong>Code fabricant :</strong> {{ $row->part_number }}</p>@endif
                    @if($row->warranty_months !== null)<p class="text-xs text-quiet mt-1"><strong>Garantie indiquée au BOQ :</strong> {{ $row->warranty_months }} mois (à valider pour la vente)</p>@endif
                    @if($row->issue)<p class="text-sm text-amber-700 mt-3">{{ $row->issue }}</p>@endif
                    @if($row->features)
                        <details class="mt-4 text-sm">
                            <summary class="font-semibold cursor-pointer text-rouge">Voir les caractéristiques fournisseur</summary>
                            <p class="whitespace-pre-line text-quiet mt-3 leading-relaxed max-w-3xl">{{ $row->features }}</p>
                        </details>
                    @endif
                </div>
                <div class="flex flex-col justify-center gap-3">
                    @if($row->status === \App\Models\CatalogImportRow::PENDING)
                        <form action="{{ route('admin.imports.approve', [$batch, $row]) }}" method="POST" class="space-y-3">
                            @csrf
                            <label class="input-label" for="cat-{{ $row->id }}">Catégorie proposée (modifiable)</label>
                            <select name="category_id" id="cat-{{ $row->id }}" required class="field">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected($row->suggested_category_id === $category->id)>{{ $category->name }}{{ ! $category->is_active ? ' (masquée)' : '' }}</option>
                                @endforeach
                            </select>
                            <button class="btn-red w-full" type="submit">Créer un brouillon non publié</button>
                        </form>
                    @elseif($row->status === \App\Models\CatalogImportRow::APPROVED && $row->product)
                        <a class="btn-light w-full" href="{{ route('admin.products.edit', $row->product) }}">Compléter la fiche →</a>
                    @endif
                    @if($row->status !== \App\Models\CatalogImportRow::APPROVED && $row->status !== \App\Models\CatalogImportRow::SKIPPED)
                        <form action="{{ route('admin.imports.skip', [$batch, $row]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-quiet text-sm font-semibold hover:text-rouge">Ignorer cette ligne</button>
                        </form>
                    @endif
                </div>
            </div>
        </article>
    @empty
        <p class="py-12 text-center text-quiet">Aucune ligne ne correspond à ce filtre.</p>
    @endforelse
</div>
<div class="mt-8">{{ $rows->links() }}</div>
<section class="mt-12 bg-white border border-slate-200 rounded-xl p-6">
    <h2 class="text-xl font-extrabold">Effacer les données de préparation</h2>
    <p class="mt-3 text-quiet text-sm max-w-3xl">Après la validation, vous pouvez supprimer définitivement ce lot de revue et ses modèles internes. Cela ne supprime pas les produits brouillons déjà créés.</p>
    <form method="POST" action="{{ route('admin.imports.purge', $batch) }}" class="flex flex-wrap gap-3 items-end mt-5">
        @csrf @method('DELETE')
        <div><label class="input-label" for="confirmation">Écrire SUPPRIMER pour confirmer</label>
        <input class="field" name="confirmation" id="confirmation" autocomplete="off" required placeholder="SUPPRIMER"></div>
        <button type="submit" class="btn-light border-red-200 text-rouge">Purger ce lot</button>
    </form>
</section>
@endsection
