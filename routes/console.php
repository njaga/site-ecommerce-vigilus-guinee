<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment('Vigilus Guinée — Commerce Dahua');
})->purpose('Afficher un message');

use App\Models\SeoRedirect;
use App\Services\SeoRedirector;

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
