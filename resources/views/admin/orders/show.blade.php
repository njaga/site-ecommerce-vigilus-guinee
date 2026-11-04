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
        <p class="text-quiet text-sm mt-4">Total définitif non calculé (livraison et installation à confirmer).</p>
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
            <strong>Pas de paiement connecté.</strong> Réserver le stock ne marque pas la commande comme payée. Une vérification commerciale et une procédure de règlement restent nécessaires.
        </div>
    </section>
</div>
@endsection
