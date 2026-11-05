<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;

class LaunchPreflight
{
    /**
     * These checks are technical readiness signals, not certification that
     * emails, legal compliance, SEO or real product stock have been verified.
     *
     * @return array<int, array{key:string, label:string, passed:bool, detail:string}>
     */
    public function checks(bool $production = false): array
    {
        $checks = [];
        $push = function (string $key, string $label, bool $passed, string $detail) use (&$checks): void {
            $checks[] = compact('key', 'label', 'passed', 'detail');
        };

        $appUrl = (string) config('app.url');
        $host = parse_url($appUrl, PHP_URL_HOST);
        $https = parse_url($appUrl, PHP_URL_SCHEME) === 'https';
        $validHost = is_string($host) && $host !== '' && ! in_array($host, ['localhost', '127.0.0.1'], true);

        $push('app-url', 'Domaine canonique HTTPS',
            $validHost && $https, 'APP_URL doit pointer vers le domaine final avec HTTPS.');
        $push('runtime', 'Configuration de production',
            config('app.env') === 'production' && ! config('app.debug'),
            'APP_ENV=production et APP_DEBUG=false sur le serveur.');
        $push('market', 'Marché et devise',
            config('site.country') === 'GN' && config('site.currency') === 'GNF',
            'Pays GN, devise GNF, après confirmation de la direction.');
        $push('contact', 'Coordonnées locales',
            filled(config('site.phone')) && filter_var(config('site.email'), FILTER_VALIDATE_EMAIL) !== false,
            'Téléphone et email publics réels à renseigner.');
        $push('order-mail', 'Alertes nouvelles commandes',
            filter_var(config('commerce.order_alert_email'), FILTER_VALIDATE_EMAIL) !== false,
            'ORDER_ALERT_EMAIL doit être une boîte de réception vérifiée.');
        $push('quote-mail', 'Alertes nouvelles demandes de devis',
            filter_var(config('commerce.quote_alert_email'), FILTER_VALIDATE_EMAIL) !== false,
            'QUOTE_ALERT_EMAIL doit être une boîte de réception vérifiée.');
        $push('smtp', 'Transport email transactionnel',
            ! in_array(config('mail.default'), ['log', 'array', 'null'], true)
                && filter_var(config('mail.from.address'), FILTER_VALIDATE_EMAIL) !== false,
            'Transport SMTP ou fournisseur opérationnel : envoi/réception réels à tester.');
        $push('staff', 'Accès administrateur autorisé',
            User::where('is_admin', true)->exists(),
            'Vérifier au moins un administrateur, son mot de passe et l’accès protégé.');
        $push('catalog', 'Catalogue publié',
            ! \App\Support\CatalogPreview::enabled() && Product::published()->exists(),
            'L’aperçu local et ses prix fictifs ne constituent pas une publication commerciale.');
        $logo = (string) config('site.brand_logo_url');
        $logoIsLocal = str_starts_with($logo, '/images/')
            && is_file(public_path(ltrim($logo, '/')));
        $push('brand-logo', 'Logo Vigilus officiel servi localement',
            $logoIsLocal,
            'Copier une ressource logo approuvée dans public/images/brand, puis configurer SITE_BRAND_LOGO_URL=/images/brand/nom-fichier.png.');
        $hero = (string) config('site.hero_illustration_url');
        $heroIsLocal = str_starts_with($hero, '/images/')
            && is_file(public_path(ltrim($hero, '/')));
        $push('hero-media', 'Visuel de couverture local',
            $heroIsLocal || Product::published()->where('is_featured', true)->whereHas('images')->exists(),
            'Utiliser une image locale autorisée ou une vraie photo de produit vedette approuvée avant publication.');

        $push('media', 'Stockage des images publiques',
            is_link(public_path('storage')) || is_dir(public_path('storage')),
            'Vérifier storage:link, droits des fichiers et chargement réel des photos.');
        $push('indexing', 'Indexation contrôlée',
            config('seo.default_indexable') === true,
            'Activer l’indexation seulement après la validation de la bascule SEO.');
        $push('legal-identity', 'Identité de l’entité exploitante',
            filled(config('site.legal_name')) && filled(config('site.legal_address'))
                && filled(config('site.legal_registration')),
            'Renseigner raison sociale, adresse légale et immatriculation vérifiées.');
        $push('privacy', 'Politique de confidentialité approuvée',
            config('site.privacy_approved') === true && filled(config('site.privacy_contact'))
                && filled(config('site.privacy_retention_notice')),
            'Revue juridique, contact confidentialité et durée de conservation explicitement rédigés.');
        $push('sales', 'Conditions commerciales approuvées',
            config('site.sales_approved') === true && filled(config('site.delivery_terms'))
                && filled(config('site.returns_terms')) && filled(config('site.warranty_terms')),
            'Livraison, garanties, retours et validation hors ligne à faire approuver.');
        $push('offline-payments', 'Aucun paiement en ligne',
            config('commerce.online_payments_enabled') === false,
            'Le site doit uniquement enregistrer des demandes puis obtenir une confirmation commerciale.');

        if (! $production) {
            // En préproduction, les conditions de publication ne sont pas
            // des erreurs techniques : elles restent visibles comme travaux.
            return $checks;
        }

        return $checks;
    }
}
