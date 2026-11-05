<?php

use App\Models\SeoRedirect;
use App\Services\LaunchPreflight;
use App\Services\SeoRedirector;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment('Vigilus Guinée — Commerce Dahua');
})->purpose('Afficher un message');

Artisan::command('seo:audit-redirects {--strict : Échouer en présence d’une redirection active non valide}', function (): int {
    $redirector = app(SeoRedirector::class);
    $active = 0;
    $drafts = 0;
    $blocked = 0;

    SeoRedirect::query()->orderBy('id')->chunk(100, function ($redirects) use (
        $redirector, &$active, &$drafts, &$blocked,
    ): void {
        foreach ($redirects as $rule) {
            if ($rule->is_active) {
                $active++;
            } else {
                $drafts++;
            }
            $check = $redirector->readiness($rule);
            if ($check !== 'ready') {
                $blocked++;
                $this->warn('Règle #'.$rule->id.' ('.($rule->is_active ? 'ACTIVE' : 'brouillon').') : '.$check);
            }
        }
    });

    $this->info('SEO : '.$active.' active(s), '.$drafts.' brouillon(s), '.$blocked.' règle(s) non prête(s).');
    $this->comment('Cible canonique configurée : '.(string) config('app.url'));
    $this->comment('Indexation activée : '.(config('seo.default_indexable') ? 'OUI' : 'NON'));

    return $this->option('strict') && $blocked > 0 ? 1 : 0;
})->purpose('Contrôler les correspondances 301 avant la bascule');

Artisan::command('commerce:preflight {--strict : Échouer si des contrôles sont incomplets}', function (): int {
    $checks = app(LaunchPreflight::class)->checks(production: true);
    $missing = 0;
    foreach ($checks as $check) {
        $prefix = $check['passed'] ? 'OK' : 'À compléter';
        $this->line('['.$prefix.'] '.$check['label'].' — '.$check['detail']);
        if (! $check['passed']) {
            $missing++;
        }
    }

    $this->info('Résultat : '.(count($checks) - $missing).' / '.count($checks).' contrôles validés.');
    $this->comment('La livraison SMTP, la conformité juridique et la qualité visuelle exigent toujours une recette manuelle.');

    return $this->option('strict') && $missing > 0 ? 1 : 0;
})->purpose('Audit préproduction : site, commande hors ligne, emails, SEO et données commerciales');
