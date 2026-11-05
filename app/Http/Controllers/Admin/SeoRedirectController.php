<?php

namespace App\Http\Controllers\Admin;

use App\Models\SeoRedirect;
use App\Services\SeoRedirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SeoRedirectController
{
    public function index(Request $request, SeoRedirector $redirector): View
    {
        $query = SeoRedirect::query()->with('creator')->latest();
        $status = (string) $request->query('statut', '');
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'draft') {
            $query->where('is_active', false);
        }

        $items = $query->paginate(30)->withQueryString();

        return view('admin.seo-redirects', [
            'redirects' => $items,
            'approvedHosts' => $redirector->allowedHosts(),
            'selectedStatus' => $status,
            'checks' => $items->getCollection()->mapWithKeys(fn ($r) =>
                [$r->id => $redirector->readiness($r)])->all(),
        ]);
    }

    public function store(Request $request, SeoRedirector $redirector): RedirectResponse
    {
        $data = $request->validate([
            'source_host' => ['required', 'string', 'max:255'],
            'source_path' => ['required', 'string', 'max:512'],
            'destination_path' => ['required', 'string', 'max:512'],
            'review_note' => ['required', 'string', 'min:10', 'max:1000'],
            'confirmed_same_market' => ['accepted'],
        ]);

        $host = $redirector->validateHost($data['source_host']);
        $source = $redirector->normalizePath($data['source_path']);
        $target = $redirector->normalizePath($data['destination_path'], 'destination_path');

        if ($source === $target || $redirector->isProtectedSource($source)) {
            throw ValidationException::withMessages([
                'source_path' => 'Source égale à la destination, protégée ou déjà réservée par le nouveau site.',
            ]);
        }
        if (! $redirector->isAllowedDestinationShape($target)) {
            throw ValidationException::withMessages([
                'destination_path' => 'La destination doit être une page publique autorisée du catalogue ou des devis.',
            ]);
        }

        SeoRedirect::create([
            'source_host' => $host,
            'source_path' => $source,
            'destination_path' => $target,
            'review_note' => $data['review_note'],
            'created_by' => $request->user()->id,
            'is_active' => false,
        ]);

        return back()->with('success', 'Redirection enregistrée comme projet inactif. Vérifiez la page cible avant activation.');
    }

    public function activate(SeoRedirect $seoRedirect, SeoRedirector $redirector): RedirectResponse
    {
        $readiness = $redirector->readiness($seoRedirect);
        if ($readiness !== 'ready') {
            throw ValidationException::withMessages([
                'redirect' => match ($readiness) {
                    'target_unavailable' => 'La page cible n’est pas publiée et indexable. Activez-la avant cette redirection.',
                    'host_unapproved' => 'Le domaine source doit être explicitement approuvé dans la configuration.',
                    default => 'La source est en conflit avec une URL du nouveau site.',
                },
            ]);
        }

        $seoRedirect->update(['is_active' => true, 'activated_at' => now()]);
        return back()->with('success', 'Redirection 301 activée pour le domaine et le chemin autorisés.');
    }

    public function deactivate(SeoRedirect $seoRedirect): RedirectResponse
    {
        $seoRedirect->update(['is_active' => false]);
        return back()->with('success', 'Redirection désactivée. L’ancienne URL renverra désormais 404.');
    }

    public function destroy(SeoRedirect $seoRedirect): RedirectResponse
    {
        if ($seoRedirect->is_active) {
            throw ValidationException::withMessages([
                'redirect' => 'Désactivez cette redirection avant de la supprimer.',
            ]);
        }

        $seoRedirect->delete();
        return back()->with('success', 'Projet de redirection supprimé.');
    }
}
