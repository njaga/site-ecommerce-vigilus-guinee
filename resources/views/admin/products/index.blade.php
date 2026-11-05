@extends('layouts.admin')
@section('title', 'Catalogue Dahua')
@section('content')
<div class="flex flex-wrap justify-between items-end gap-5 mb-7">
    <div><p class="kicker">Back-office · Vigilus Guinée</p><h1 class="text-3xl font-extrabold mt-1">Catalogue Dahua</h1>
        <p class="text-quiet text-sm mt-2">Gestion des références du catalogue.</p></div>
    <a href="{{ route('admin.products.create') }}" class="btn-red">+ Ajouter une référence</a>
</div>
<form action="{{ route('admin.products.index') }}" method="GET" class="bg-white border border-slate-200 rounded-xl p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div class="min-w-[220px] flex-1"><label class="input-label" for="catalog-search">Rechercher</label>
        <input class="field" id="catalog-search" name="q" value="{{ $search }}" placeholder="Nom ou modèle Dahua"></div>
    <div class="min-w-[220px]"><label class="input-label" for="catalog-status">Statut</label>
        <select class="field" id="catalog-status" name="statut">
            @foreach(['' => 'Toutes les fiches', 'draft' => 'Brouillons', 'published' => 'Publiées', 'missing-price' => 'Prix non renseigné'] as $value => $label)
                <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
            @endforeach
        </select></div>
    <button type="submit" class="btn-light">Filtrer</button>
</form>
<div class="rounded-xl bg-white border border-slate-200 overflow-x-auto">
    <table class="w-full min-w-[750px] text-sm">
        <thead class="bg-slate-50 text-left text-quiet uppercase text-xs">
            <tr><th class="p-4">Référence / Produit</th><th class="p-4">Catégorie</th><th class="p-4">Prix GNF</th><th class="p-4">Stock</th><th class="p-4">État</th><th class="p-4 text-right">Actions</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
        @forelse($products as $product)
            <tr>
                <td class="p-4"><strong class="block">{{ $product->name }}</strong><span class="text-xs text-quiet">{{ $product->sku }}</span></td>
                <td class="p-4">{{ $product->category->name }}</td>
                <td class="p-4">
                    @if($product->display_price_gnf !== null){{ number_format($product->display_price_gnf, 0, ',', ' ') }}
                    @elseif($product->price_on_request)Sur devis
                    @else<span class="text-rouge">À valider</span>@endif
                </td>
                <td class="p-4">{{ $product->stock_qty }}</td>
                <td class="p-4"><span class="{{ $product->is_active ? 'text-emerald-700' : 'text-quiet' }}">{{ $product->is_active ? 'Publiée' : 'Brouillon' }}</span></td>
                <td class="p-4 text-right">
                    <a class="text-rouge font-bold hover:underline" href="{{ route('admin.products.edit', $product) }}">Compléter</a>
                    @if($product->is_active)
                        <form action="{{ route('admin.products.archive', $product) }}" method="POST" class="inline ml-3">
                            @csrf @method('PATCH')<button type="submit" class="text-quiet hover:text-rouge">Masquer</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="p-10 text-center text-quiet">Aucune fiche correspondant aux filtres.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
