@extends('layouts.store')
@section('title', 'Suivi sécurisé de demande | Vigilus Guinée')
@section('robots', 'noindex,nofollow')
@section('content')
<div class="container-wide max-w-3xl py-16">
    <p class="section-eyebrow">Suivi de commande · Accès privé</p>
    <h1 class="text-3xl sm:text-4xl font-extrabold mt-5">Suivi de la demande {{ $order->reference }}</h1>
    <div class="bg-cloud border border-slate-200 rounded-md p-7 mt-8">
        <h2 class="font-extrabold text-xl">{{ $order->statusLabel() }}</h2>
        <p class="mt-3 text-sm text-quiet">
            @if($order->status === \App\Models\Order::PENDING)
                Notre équipe étudie la disponibilité, les frais de livraison et les modalités de commande.
            @elseif($order->status === \App\Models\Order::RESERVED)
                Le matériel a été réservé ; l’équipe doit encore confirmer les conditions commerciales.
            @elseif($order->status === \App\Models\Order::CONFIRMED)
                Votre commande a été confirmée par l’équipe. Les modalités de règlement se conviennent directement avec Vigilus.
            @else
                Cette demande n’est plus active.
            @endif
        </p>
        <p class="text-xs text-quiet mt-4">Aucun paiement en ligne n'est proposé.</p>
    </div>
    <section class="mt-10">
        <h2 class="text-xl font-extrabold mb-4">Articles concernés</h2>
        <div class="divide-y divide-slate-200">
            @foreach($order->items as $item)
                <div class="py-4 flex items-start justify-between gap-5 text-sm">
                    <div><strong>{{ $item->name_snapshot }}</strong><p class="text-quiet">{{ $item->sku_snapshot }} · Qté {{ $item->quantity }}</p></div>
                    <span class="shrink-0 font-bold">{{ number_format($item->subtotal_gnf, 0, ',', ' ') }} GNF</span>
                </div>
            @endforeach
        </div>
        <div class="flex justify-between font-bold border-t border-slate-200 pt-5"><span>Sous-total matériel</span><span>{{ number_format($order->subtotal_gnf, 0, ',', ' ') }} GNF</span></div>
        @if($order->total_gnf !== null)
            <div class="flex justify-between mt-3"><span>Frais livraison confirmés</span><span>{{ number_format($order->shipping_gnf, 0, ',', ' ') }} GNF</span></div>
            <div class="flex justify-between mt-3 text-lg font-extrabold"><span>Total matériel + livraison</span><span>{{ number_format($order->total_gnf, 0, ',', ' ') }} GNF</span></div>
        @else
            <p class="text-sm text-quiet mt-5">Frais de livraison et total à confirmer par Vigilus.</p>
        @endif
    </section>
    <p class="text-xs text-quiet mt-12">Ce lien temporaire permet de consulter uniquement l'état de votre demande. Il n'ouvre pas d'espace de paiement.</p>
</div>
@endsection
