@extends('layouts.admin')
@section('title', 'Migration SEO — URLs historiques')
@section('content')
<div class="mb-8">
    <p class="kicker">Préparer la bascule</p>
    <h1 class="text-3xl font-extrabold mt-2">Redirections SEO</h1>
    <p class="text-quiet max-w-3xl mt-3">Cartographiez uniquement les anciennes URLs appartenant au même marché que la nouvelle boutique. Aucune règle n'est importée depuis un autre site du groupe.</p>
</div>
<div class="mb-7 border border-amber-200 bg-amber-50 rounded-xl p-5 text-sm leading-relaxed text-amber-950">
    <strong>Attention au périmètre.</strong> Le dépôt historique consulté référence des pages Vigilus Côte d’Ivoire et le domaine <code>groupevigilus.ci</code>. Ne redirigez pas ces pages vers Conakry : vérifiez d’abord les URLs du véritable domaine Guinée et la correspondance exacte des contenus. Une redirection ne fonctionne sur un ancien domaine que si son trafic est réellement acheminé vers cette application.
</div>
<div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(300px,.65fr)] gap-6 mb-9">
    <section class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-xl font-extrabold">Ajouter une correspondance</h2>
        <p class="text-quiet text-sm mt-3">Une règle est toujours créée en <strong>brouillon inactif</strong>, puis activée séparément après vérification.</p>
        <form method="POST" action="{{ route('admin.seo-redirects.store') }}" class="space-y-4 mt-5">
            @csrf
            <div><label class="input-label" for="source_host">Domaine source autorisé *</label>
                <select class="field" name="source_host" id="source_host" required>
                    @foreach($approvedHosts as $host)
                        <option value="{{ $host }}">{{ $host }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-quiet mt-2">Domaines supplémentaires à déclarer via SEO_APPROVED_LEGACY_HOSTS, après validation. Aucun domaine n'est détecté ou autorisé automatiquement.</p>
            </div>
            <div><label class="input-label" for="source_path">Ancienne URL (chemin uniquement) *</label>
                <input class="field" name="source_path" id="source_path" required maxlength="512" placeholder="/ancienne-page-produit"></div>
            <div><label class="input-label" for="destination_path">Nouvelle URL correspondante (chemin uniquement) *</label>
                <input class="field" name="destination_path" id="destination_path" required maxlength="512" placeholder="/produits/modele-dahua-verifie"></div>
            <div><label class="input-label" for="review_note">Justification et validation métier *</label>
                <textarea class="field" name="review_note" id="review_note" required minlength="10" maxlength="1000" rows="3" placeholder="Même produit / même intention et même pays, validé par…"></textarea></div>
            <label class="text-sm flex gap-3 items-start text-quiet"><input type="checkbox" name="confirmed_same_market" value="1" required class="mt-1">
                Je confirme que les deux URLs concernent le même marché et des contenus réellement équivalents.</label>
            <button type="submit" class="btn-red w-full sm:w-auto">Enregistrer en brouillon</button>
        </form>
    </section>
    <aside class="bg-cloud border border-slate-200 rounded-xl p-6">
        <h2 class="text-xl font-extrabold">Règles de sécurité</h2>
        <ul class="list-disc pl-5 text-sm text-quiet mt-4 space-y-3">
            <li>Redirections <strong>301 exactes</strong> : aucune redirection globale de toutes les erreurs 404 vers l’accueil.</li>
            <li>Aucun domaine étranger ni cible externe depuis le formulaire.</li>
            <li>La destination doit exister et être publique pour activer la règle.</li>
            <li>Les paniers, comptes, commandes, suivis privés et pages administratives ne sont jamais redirigés.</li>
            <li>Les données de suivi par query string ne sont pas reprises automatiquement.</li>
        </ul>
    </aside>
</div>
<div class="flex flex-wrap gap-3 justify-between items-center mb-5">
    <h2 class="text-xl font-extrabold">Correspondances préparées</h2>
    <nav class="flex gap-2 text-sm font-semibold" aria-label="Filtrer les redirections">
        @foreach(['' => 'Toutes', 'draft' => 'Brouillons', 'active' => 'Actives'] as $key => $label)
            <a href="{{ route('admin.seo-redirects.index', $key !== '' ? ['statut' => $key] : []) }}"
               class="px-3 py-2 border rounded-lg {{ $selectedStatus === $key ? 'bg-ink text-white border-ink' : 'bg-white border-slate-200' }}">{{ $label }}</a>
        @endforeach
    </nav>
</div>
<div class="space-y-3">
@forelse($redirects as $redirect)
    @php $check = $checks[$redirect->id] ?? 'target_unavailable'; @endphp
    <article class="bg-white border border-slate-200 p-5 rounded-xl">
        <div class="flex flex-wrap justify-between gap-4">
            <div class="min-w-0 flex-1">
                <div class="flex gap-3 flex-wrap text-xs font-semibold">
                    <span class="{{ $redirect->is_active ? 'text-emerald-700' : 'text-quiet' }}">{{ $redirect->is_active ? '301 active' : 'Brouillon' }}</span>
                    <span class="{{ $check === 'ready' ? 'text-emerald-700' : 'text-amber-700' }}">{{ match($check) {
                        'ready' => 'Cible accessible',
                        'host_unapproved' => 'Domaine non autorisé',
                        'source_conflict' => 'Source protégée',
                        default => 'Cible indisponible',
                    } }}</span>
                </div>
                <p class="font-bold text-sm mt-3 break-all">{{ $redirect->source_host }}{{ $redirect->source_path }}</p>
                <p class="text-sm text-quiet mt-1 break-all">301 → {{ $redirect->destination_path }}</p>
                <p class="text-xs text-quiet mt-2">{{ $redirect->review_note }}</p>
            </div>
            <div class="flex gap-2 flex-wrap items-start">
                @if($redirect->is_active)
                    <form method="POST" action="{{ route('admin.seo-redirects.deactivate', $redirect) }}">
                        @csrf @method('PATCH')<button class="btn-light" type="submit">Désactiver</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.seo-redirects.activate', $redirect) }}">
                        @csrf @method('PATCH')<button class="btn-red" type="submit" @disabled($check !== 'ready')>Activer 301</button>
                    </form>
                    <form method="POST" action="{{ route('admin.seo-redirects.destroy', $redirect) }}">
                        @csrf @method('DELETE')<button class="btn-light" type="submit">Supprimer</button>
                    </form>
                @endif
            </div>
        </div>
    </article>
@empty
    <p class="text-center text-quiet py-12 border border-slate-200 rounded-xl">Aucune redirection activée ni proposée : le lancement n'affectera pas d'autres pays par défaut.</p>
@endforelse
</div>
<div class="mt-6">{{ $redirects->links() }}</div>
@endsection
