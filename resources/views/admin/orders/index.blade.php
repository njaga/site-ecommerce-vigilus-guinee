@extends('layouts.admin')
@section('title', 'Demandes de commande')
@section('content')
<div class="flex items-center justify-between mb-7">
    <div><p class="kicker">Commerce</p><h1 class="text-3xl font-extrabold mt-1">Commandes à traiter</h1></div>
</div>
<div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
    <table class="min-w-[850px] w-full text-sm">
        <thead class="text-quiet text-xs uppercase bg-slate-50 text-left"><tr><th class="p-4">Référence</th><th class="p-4">Date</th><th class="p-4">Client</th><th class="p-4">Articles</th><th class="p-4">Sous-total</th><th class="p-4">Statut</th><th class="p-4"></th></tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($orders as $order)
                <tr>
                    <td class="p-4 font-bold">{{ $order->reference }}</td>
                    <td class="p-4">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="p-4">{{ $order->customer_name }}<span class="block text-quiet">{{ $order->city }}</span></td>
                    <td class="p-4">{{ $order->items_count }}</td>
                    <td class="p-4">{{ number_format($order->subtotal_gnf, 0, ',', ' ') }} GNF</td>
                    <td class="p-4">{{ match($order->status) { 'reserved' => 'Stock réservé', 'cancelled' => 'Annulée', default => 'À confirmer' } }}</td>
                    <td class="p-4 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="font-bold text-rouge hover:underline">Ouvrir</a></td>
                </tr>
            @empty
                <tr><td class="p-10 text-center text-quiet" colspan="7">Aucune demande de commande enregistrée.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
