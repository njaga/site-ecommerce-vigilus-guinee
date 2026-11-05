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


Artisan::command('catalogue:preparer-apercu {--dry-run : Afficher le nombre sans créer de fiches}', function (): int {
    if (! \App\Support\CatalogPreview::enabled()) {
        $this->error('Réservé au mode aperçu sur localhost, indexation désactivée.');
        return 1;
    }

    $preparer = app(\App\Services\CatalogPreviewPreparer::class);
    $counts = $preparer->counts();

    if ($this->option('dry-run')) {
        $this->info($counts['total'].' produit(s) déjà dans le catalogue ; '
            .$counts['pending'].' ligne(s) d’import admissible(s) en attente.');
        return 0;
    }

    $result = $preparer->prepare();
    $this->info($result['created'].' fiche(s) ajoutée(s) à partir des imports.');
    $this->info($result['total'].' produit(s) visible(s) en aperçu à 0 GNF.');
    $this->comment($result['skipped'].' ignorée(s) ; '.$result['pending'].' ligne(s) encore à examiner.');
    $this->comment('Aucun prix réel, stock ou statut de publication n’a été modifié.');

    return 0;
})->purpose('Remplir le catalogue de prévisualisation à partir des imports techniques locaux');


Artisan::command('catalogue:diagnostic-xlsx {fichier : Chemin du XLSX sur ce PC}', function (): int {
    if (! app()->environment(['local', 'testing'])) {
        $this->error('Diagnostic autorisé uniquement en environnement local.');
        return 1;
    }

    $path = (string) $this->argument('fichier');
    if (! is_file($path) || ! is_readable($path)) {
        $this->error('Fichier introuvable ou illisible. Donnez son chemin complet, entre guillemets.');
        return 1;
    }

    if (! extension_loaded('zip') || ! class_exists(\ZipArchive::class)) {
        $this->error('ZIP MANQUANT : activez extension=zip dans le fichier php.ini utilisé par WAMP.');
        $this->line('PHP CLI : '.PHP_VERSION.' | Configuration : '.(php_ini_loaded_file() ?: 'aucune'));
        return 1;
    }

    $zip = new \ZipArchive();
    $opened = $zip->open($path, \ZipArchive::RDONLY);
    if ($opened !== true) {
        $this->error('Archive ZIP invalide ou inaccessible (code '.$opened.').');
        return 1;
    }

    try {
        if ($zip->locateName('xl/workbook.xml') === false) {
            $this->error('Archive ZIP valide, mais xl/workbook.xml est absent : ce fichier n’est pas un XLSX standard.');
            return 1;
        }
    } finally {
        $zip->close();
    }

    $spreadsheet = null;
    try {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly(['Data Sheet']);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheetByName('Data Sheet');

        if ($sheet === null) {
            $this->error('Lecture XLSX réussie, mais la feuille « Data Sheet » est absente.');
            return 1;
        }

        $required = [
            'A' => 'Item', 'B' => 'Category', 'C' => 'Internal Model',
            'D' => 'External Model', 'E' => 'General Description',
            'F' => 'Features', 'M' => 'Part No.', 'O' => 'Standard Warranty',
        ];

        foreach ($required as $column => $label) {
            $value = trim((string) $sheet->getCell($column.'1')->getValue());
            if (strcasecmp($value, $label) !== 0) {
                $this->error('En-tête incorrect en '.$column.'1 : attendu « '.$label.' ».');
                return 1;
            }
        }

        $this->info('Lecture XLSX réussie avec PHPSpreadsheet.');
        $this->info('Feuille Data Sheet valide. Dernière ligne : '.$sheet->getHighestDataRow().'.');
        $this->comment('Aucune donnée importée ni enregistrée.');
        return 0;
    } catch (\Throwable $e) {
        $reason = str_replace($path, '[fichier]', $e->getMessage());
        $this->error('Échec PHPSpreadsheet : '.get_debug_type($e));
        $this->error(\Illuminate\Support\Str::limit($reason, 500));
        $this->line('PHP : '.PHP_VERSION.' | php.ini : '.(php_ini_loaded_file() ?: 'aucun'));
        return 1;
    } finally {
        $spreadsheet?->disconnectWorksheets();
    }
})->purpose('Diagnostiquer un fichier Excel sans import ni divulgation de son contenu');
