<?php

namespace App\Services;

use App\Models\CatalogImportRow;
use App\Models\Product;
use App\Support\CatalogPreview;
use Illuminate\Validation\ValidationException;
use LogicException;

class CatalogPreviewPreparer
{
    public function __construct(private readonly CatalogDraftApprover $approver)
    {
    }

    /**
     * Technical BOQ rows previously staged in the LOCAL database can be
     * materialized as drafts. All supplier costs, discounts and inventory
     * quantities remain excluded by CatalogDraftApprover.
     *
     * @return array{created:int, skipped:int, total:int, pending:int}
     */
    public function prepare(): array
    {
        if (! CatalogPreview::enabled()) {
            throw new LogicException('Prévisualisation disponible uniquement sur localhost, hors indexation.');
        }

        $created = 0;
        $skipped = 0;

        $this->pendingQuery()->with('category')->orderBy('id')
            ->chunkById(100, function ($rows) use (&$created, &$skipped): void {
                foreach ($rows as $row) {
                    try {
                        $this->approver->approve($row, $row->category);
                        $created++;
                    } catch (ValidationException) {
                        // Duplicate/changed rows remain in review; no existing
                        // product is overwritten, and no confidential data is logged.
                        $skipped++;
                    }
                }
            });

        return [
            'created' => $created,
            'skipped' => $skipped,
            'total' => Product::query()->count(),
            'pending' => $this->pendingQuery()->count(),
        ];
    }

    public function counts(): array
    {
        if (! CatalogPreview::enabled()) {
            return ['total' => 0, 'pending' => 0];
        }

        return [
            'total' => Product::query()->count(),
            'pending' => $this->pendingQuery()->count(),
        ];
    }

    private function pendingQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return CatalogImportRow::query()
            ->where('status', CatalogImportRow::PENDING)
            ->whereNotNull('external_model')
            ->whereNotNull('suggested_category_id')
            ->whereHas('category');
    }
}
