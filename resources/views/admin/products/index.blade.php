@extends('layouts.admin')
@section('title', 'Gestion des produits')
@section('content')
<div class="flex flex-wrap justify-between items-center gap-5 mb-7">
    <div><p class="kicker">Catalogue Dahua</p><h1 class="text-3xl font-extrabold mt-1">Produits</h1></div>
    <a href="{{ route('admin.products.create') }}" class="btn-red">+ Ajouter une référence</a>
</div>
<div class="rounded-xl bg-white border border-slate-200 overflow-x-auto">
    <table class="w-full min-w-[720px] text-sm">
        <thead class="bg-slate-50 text-left text-quiet uppercase text-xs">
        <tr><th class="p-4">Référence / Produit</th><th class="p-4">Catégorie</th><th class="p-4">Prix GNF</th><th class="p-4">Stock</th><th class="p-4">État</th><th class="p-4 text-right">Actions</th></tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse($products as $product)
            <tr>
                <td class="p-4"><strong class="block">{{ $product->name }}</strong><span class="text-xs text-quiet">{{ $product->sku }}</span></td>
                <td class="p-4">{{ $product->category->name }}</td>
                <td class="p-4">{{ $product->price_gnf === null ? 'Sur devis' : number_format($product->price_gnf, 0, ',', ' ') }}</td>
                <td class="p-4">{{ $product->stock_qty }}</td>
                <td class="p-4">{{ $product->is_active ? 'Publié' : 'Brouillon' }}</td>
                <td class="p-4 text-right">
                    <a class="text-rouge font-bold hover:underline" href="{{ route('admin.products.edit', $product) }}">Modifier</a>
                    @if($product->is_active)
                        <form action="{{ route('admin.products.archive', $product) }}" method="POST" class="inline ml-3">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-quiet hover:text-rouge">Masquer</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty <tr><td colspan="6" class="p-10 text-center text-quiet">Aucune référence. Créez un premier produit après validation du catalogue Dahua.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
