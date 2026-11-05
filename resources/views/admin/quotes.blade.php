@extends('layouts.admin')
@section('title', 'Demandes de devis')
@section('content')
<h1 class="text-3xl font-extrabold mb-7">Demandes commerciales</h1>
<div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
<table class="w-full min-w-[920px] text-sm">
    <thead class="bg-slate-50 text-left"><tr><th class="p-4">Date</th><th class="p-4">Client</th><th class="p-4">Coordonnées</th><th class="p-4">Demande</th><th class="p-4">Statut</th></tr></thead>
    <tbody class="divide-y divide-slate-100">
    @forelse($quotes as $quote)
        <tr>
            <td class="p-4 whitespace-nowrap">{{ $quote->created_at->format('d/m/Y H:i') }}</td>
            <td class="p-4"><strong class="block">{{ $quote->name }}</strong><span>{{ $quote->company }}</span></td>
            <td class="p-4">{{ $quote->phone }}<br>{{ $quote->email }}</td>
            <td class="p-4 max-w-sm"><strong class="block">{{ $quote->product?->sku ?: 'Projet sur mesure' }} · {{ $quote->quantity }} unité(s)</strong><p class="whitespace-pre-line text-quiet">{{ $quote->message }}</p></td>
            <td class="p-4">{{ $quote->status === 'new' ? 'Nouveau' : $quote->status }}</td>
        </tr>
    @empty <tr><td colspan="5" class="p-10 text-center text-quiet">Aucune demande reçue.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-6">{{ $quotes->links() }}</div>
@endsection
