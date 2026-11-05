<?php

namespace App\Http\Controllers\Admin;

use App\Services\CatalogPreviewPreparer;
use App\Support\CatalogPreview;
use Illuminate\Http\RedirectResponse;

class CatalogPreviewController
{
    public function __invoke(CatalogPreviewPreparer $preparer): RedirectResponse
    {
        abort_unless(CatalogPreview::enabled(), 404);

        $result = $preparer->prepare();

        return back()->with(
            'success',
            $result['created'].' référence(s) ajoutée(s) à l’aperçu. '
                .$result['total'].' fiche(s) au catalogue. '
                .$result['skipped'].' ligne(s) ignorée(s) et '
                .$result['pending'].' en attente de revue.'
        );
    }
}
