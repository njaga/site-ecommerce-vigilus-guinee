<?php

namespace App\Http\Controllers\Admin;

use App\Models\CatalogImportBatch;
use App\Models\CatalogImportRow;
use App\Models\Category;
use App\Services\CatalogDraftApprover;
use App\Services\DahuaBoqImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CatalogImportController
{
    public function index(): View
    {
        return view('admin.imports.index', [
            'batches' => CatalogImportBatch::query()
                ->with('creator')
                ->withCount([
                    'rows as pending_count' => fn ($q) => $q->where('status', CatalogImportRow::PENDING),
                    'rows as approved_count' => fn ($q) => $q->where('status', CatalogImportRow::APPROVED),
                    'rows as duplicate_count' => fn ($q) => $q->where('status', CatalogImportRow::DUPLICATE),
                    'rows as invalid_count' => fn ($q) => $q->where('status', CatalogImportRow::INVALID),
                ])
                ->latest()->paginate(15),
        ]);
    }

    public function store(Request $request, DahuaBoqImporter $importer): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:8192'],
            'confidentiality_ack' => ['accepted'],
        ]);

        $batch = $importer->stage($validated['file'], $request->user());

        return redirect()->route('admin.imports.show', $batch)->with(
            'success',
            'Import technique préparé. Les informations financières ne sont pas conservées ; aucune référence n’est publiée.',
        );
    }

    public function show(Request $request, CatalogImportBatch $batch): View
    {
        $status = (string) $request->query('statut', '');
        $allowed = [
            CatalogImportRow::PENDING, CatalogImportRow::DUPLICATE,
            CatalogImportRow::INVALID, CatalogImportRow::APPROVED,
            CatalogImportRow::SKIPPED,
        ];

        $query = $batch->rows()->with(['category', 'product'])->orderBy('source_row');
        if (in_array($status, $allowed, true)) {
            $query->where('status', $status);
        }

        return view('admin.imports.show', [
            'batch' => $batch,
            'rows' => $query->paginate(30)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'selectedStatus' => $status,
            'counts' => $batch->rows()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function approve(
        Request $request,
        CatalogImportBatch $batch,
        CatalogImportRow $row,
        CatalogDraftApprover $approver,
    ): RedirectResponse {
        $this->assertChild($batch, $row);

        $validated = $request->validate([
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
        ]);
        $category = Category::findOrFail($validated['category_id']);
        $product = $approver->approve($row, $category);

        return back()->with('success',
            'Brouillon créé pour '.$product->sku.'. Prix : non renseigné ; stock : 0 ; publication : désactivée.',
        );
    }

    public function skip(CatalogImportBatch $batch, CatalogImportRow $row): RedirectResponse
    {
        $this->assertChild($batch, $row);
        DB::transaction(function () use ($row): void {
            $locked = CatalogImportRow::whereKey($row->id)->lockForUpdate()->firstOrFail();
            if ($locked->status === CatalogImportRow::APPROVED) {
                throw ValidationException::withMessages([
                    'row' => 'Une référence approuvée doit être gérée depuis le catalogue.',
                ]);
            }
            $locked->update(['status' => CatalogImportRow::SKIPPED]);
        });

        return back()->with('success', 'Ligne ignorée. Aucun produit créé.');
    }

    public function purge(Request $request, CatalogImportBatch $batch): RedirectResponse
    {
        $request->validate(['confirmation' => ['required', Rule::in(['SUPPRIMER'])]]);

        // Supprime toutes les données du lot de revue, dont modèles internes et
        // caractéristiques temporaires. Les produits brouillons approuvés restent.
        $batch->delete();

        return redirect()->route('admin.imports.index')->with('success',
            'Lot de préparation supprimé. Les brouillons déjà créés sont conservés.',
        );
    }

    private function assertChild(CatalogImportBatch $batch, CatalogImportRow $row): void
    {
        abort_unless($row->batch_id === $batch->id, 404);
    }
}
