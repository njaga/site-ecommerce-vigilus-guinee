@extends('layouts.admin')
@section('title', 'Imports confidentiels Dahua')
@section('content')
<div class="flex flex-wrap justify-between items-end gap-5 mb-8">
    <div>
        <p class="kicker">Catalogue produit</p>
        <h1 class="text-3xl font-extrabold mt-1">Imports BOQ Dahua</h1>
        <p class="mt-3 text-quiet max-w-3xl">Préparez les références fabricant à partir d'un fichier Excel fournisseur. Cette fonction est réservée à l'administration.</p>
    </div>
    <a class="btn-light" href="{{ route('admin.products.index') }}">Voir les brouillons →</a>
</div>
<div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(300px,.8fr)] gap-6 mb-10">
    <section class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8">
        <h2 class="text-xl font-extrabold">Importer un nouveau BOQ</h2>
        <p class="text-quiet mt-3 text-sm leading-relaxed">
            Format attendu : XLSX, feuille « Data Sheet » avec les colonnes de référence, description, caractéristiques et garantie.
            Taille maximale : 8 Mo. Aucune référence ne sera publiée automatiquement.
        </p>
        <form method="POST" action="{{ route('admin.imports.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf
            <div>
                <label class="input-label" for="boq_file">Fichier Excel confidentiel *</label>
                <input class="field" id="boq_file" type="file" name="file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
            </div>
            <label class="flex gap-3 items-start text-sm text-quiet">
                <input type="checkbox" name="confidentiality_ack" value="1" class="mt-1" required>
                <span>Je confirme que le document est autorisé pour l'usage interne de Vigilus. Je comprends que seuls les champs techniques seront conservés pour revue et que le fichier original ne sera pas sauvegardé par cet importateur.</span>
            </label>
            <button class="btn-red w-full sm:w-auto" type="submit">Analyser et préparer les références</button>
        </form>
    </section>
    <aside class="border border-red-200 bg-red-50 rounded-xl p-6 sm:p-8">
        <p class="text-rouge font-extrabold uppercase tracking-wide text-xs">Confidentialité</p>
        <h2 class="font-extrabold text-xl mt-3">Prix fournisseurs non importés</h2>
        <p class="text-sm leading-relaxed text-slate-700 mt-4">
            Les prix d'achat en USD, montants totaux, volumes d'approvisionnement et remises du BOQ ne sont ni importés dans la base commerciale ni transférés aux pages publiques.
        </p>
        <p class="text-sm leading-relaxed text-slate-700 mt-4">
            Les références sont d'abord examinées dans un espace privé. Après validation, elles deviennent des <strong>brouillons non publiés</strong> avec un prix public vide et un stock à zéro.
        </p>
        <p class="text-sm leading-relaxed text-slate-700 mt-4">
            La suppression d'un lot purge ses informations de préparation. Les produits déjà approuvés restent disponibles dans le back-office.
        </p>
    </aside>
</div>
<section>
    <h2 class="text-xl font-extrabold mb-5">Historique des imports</h2>
    <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
        <table class="w-full min-w-[730px] text-sm">
            <thead class="bg-slate-50 uppercase text-xs text-quiet text-left">
                <tr><th class="p-4">Import</th><th class="p-4">Date</th><th class="p-4">Lignes</th><th class="p-4">À examiner</th><th class="p-4">Brouillons</th><th class="p-4">Doublons</th><th class="p-4"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($batches as $batch)
                <tr>
                    <td class="p-4 font-bold">BOQ #{{ $batch->id }}</td>
                    <td class="p-4">{{ $batch->created_at->format('d/m/Y H:i') }}</td>
                    <td class="p-4">{{ $batch->total_rows }}</td>
                    <td class="p-4">{{ $batch->pending_count }}</td>
                    <td class="p-4">{{ $batch->approved_count }}</td>
                    <td class="p-4">{{ $batch->duplicate_count }}</td>
                    <td class="p-4 text-right"><a class="font-bold text-rouge hover:underline" href="{{ route('admin.imports.show', $batch) }}">Examiner →</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-10 text-center text-quiet">Aucun import encore effectué.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $batches->links() }}</div>
</section>
@endsection
