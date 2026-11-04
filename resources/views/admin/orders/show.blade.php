@extends('layouts.admin')
@section('title', 'Commande '.$order->reference)
@section('content')
<a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-quiet hover:text-rouge">← Retour aux demandes</a>
<div class="flex flex-wrap items-center justify-between gap-5 mt-4 mb-8">
    <div><p class="kicker">Demande de commande</p><h1 class="text-3xl font-extrabold mt-2">{{ $order->reference }}</h1>
        <p class="mt-2 text-sm text-quiet">{{ $order->created_at->format('d/m/Y à H:i') }} · {{ $order->status }} · Paiement : {{ $order->payment_status }}</p></div>
    @if($order->status === 'pending_review')
        <div class="flex gap-3">
            <form action="{{ route('admin.orders.reserve', $order) }}" method="POST">@csrf @method('PATCH')
                <button type="submit" class="btn-red">Réserver le stock</button>
            </form>
            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">@csrf @method('PATCH')
                <button type="submit" class="btn-light">Annuler</button>
            </form>
        </div>
    @elseif($order->status === 'reserved')
        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">@csrf @method('PATCH')
            <button type="submit" class="btn-light">Annuler et restituer le stock</button>
        </form>
    @endif
</div>
@if($order->status === \App\Models\Order::RESERVED)
    <section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
        <h2 class="text-xl font-extrabold">Confirmer commercialement la commande</h2>
        <p class="text-sm text-quiet mt-2">Avant de confirmer, vérifier la disponibilité, le prix matériel, les modalités de livraison et l'accord du client. Aucun paiement n'est collecté sur le site.</p>
        <form method="POST" action="{{ route('admin.orders.confirm', $order) }}" class="grid sm:grid-cols-[220px_1fr_auto] gap-4 items-end mt-5">
            @csrf @method('PATCH')
            <div><label class="input-label" for="shipping_gnf">Frais livraison confirmés (GNF) *</label>
                <input class="field" id="shipping_gnf" type="number" min="0" step="1" required name="shipping_gnf" value="{{ old('shipping_gnf') }}"></div>
            <div><label class="input-label" for="internal_note">Note interne (facultative)</label>
                <input class="field" id="internal_note" name="internal_note" maxlength="1500" placeholder="Accord obtenu par téléphone…"></div>
            <button type="submit" class="btn-red">Confirmer</button>
        </form>
        <p class="text-xs text-quiet mt-3">0 GNF ne doit être saisi que si la livraison est réellement offerte ou sans frais. Une prestation d'installation éventuelle doit faire l'objet d'un chiffrage séparé.</p>
    </section>
@endif
<div class="grid lg:grid-cols-2 gap-6 items-start">
    <section class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-xl font-extrabold">Articles</h2>
        <div class="divide-y divide-slate-100 mt-4">
            @foreach($order->items as $item)
            <div class="py-4">
                <strong class="block">{{ $item->name_snapshot }}</strong>
                <p class="text-xs text-quiet">{{ $item->sku_snapshot }}</p>
                <div class="mt-2 flex justify-between text-sm">
                    <span>{{ $item->quantity }} × {{ number_format($item->unit_price_gnf, 0, ',', ' ') }} GNF</span>
                    <strong>{{ number_format($item->subtotal_gnf, 0, ',', ' ') }} GNF</strong>
                </div>
            </div>
            @endforeach
        </div>
        <div class="border-t border-slate-200 mt-4 pt-5 font-extrabold flex justify-between">
            <span>Sous-total matériel</span><span>{{ number_format($order->subtotal_gnf, 0, ',', ' ') }} GNF</span>
        </div>
        @if($order->total_gnf !== null)
            <div class="mt-3 flex justify-between text-sm"><span>Livraison confirmée</span><span>{{ number_format($order->shipping_gnf, 0, ',', ' ') }} GNF</span></div>
            <div class="mt-3 flex justify-between font-extrabold"><span>Total matériel + livraison</span><span>{{ number_format($order->total_gnf, 0, ',', ' ') }} GNF</span></div>
        @else
            <p class="text-quiet text-sm mt-4">Montant final encore indéterminé (frais de livraison non validés).</p>
        @endif
    </section>
    <section class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-xl font-extrabold">Informations du client</h2>
        <dl class="mt-4 text-sm divide-y divide-slate-100">
            <div class="py-3"><dt class="font-bold">Nom</dt><dd>{{ $order->customer_name }}</dd></div>
            <div class="py-3"><dt class="font-bold">Téléphone</dt><dd>{{ $order->customer_phone }}</dd></div>
            <div class="py-3"><dt class="font-bold">Email</dt><dd>{{ $order->customer_email ?: 'Non communiqué' }}</dd></div>
            <div class="py-3"><dt class="font-bold">Adresse souhaitée</dt><dd class="whitespace-pre-line">{{ $order->address }} — {{ $order->district }}, {{ $order->city }}</dd></div>
            <div class="py-3"><dt class="font-bold">Notes</dt><dd class="whitespace-pre-line">{{ $order->notes ?: 'Aucune' }}</dd></div>
        </dl>
        <div class="bg-amber-50 border border-amber-200 text-amber-950 rounded-lg p-4 text-sm mt-5">
            <strong>Aucun paiement en ligne.</strong> La commande est gérée commercialement par Vigilus. La confirmation ne signifie pas qu'un règlement a été encaissé.
        </div>
    </section>
</div>
@endsection
