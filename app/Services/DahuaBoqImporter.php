<?php

namespace App\Services;

use App\Models\CatalogImportBatch;
use App\Models\CatalogImportRow;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

class DahuaBoqImporter
{
    /**
     * Read only the technical catalogue columns from an uploaded BOQ.
     *
     * NEVER read/store columns G,H,I,J,K,L,N,P,Q:
     * logistics, photos, quantities, wholesale USD costs, supplier totals,
     * commercial remarks, embedded links and extension terms are not needed.
     * The raw Excel file remains in PHP temporary upload storage.
     */
    public function stage(UploadedFile $file, ?User $actor): CatalogImportBatch
    {
        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $reader->setLoadSheetsOnly(['Data Sheet']);
            $spreadsheet = $reader->load($file->getRealPath());
        } catch (Throwable $e) {
            // Previously swallowed all reader errors, so neither the UI nor the
            // application log could distinguish a missing PHP extension from an
            // unreadable OOXML package.
            $reason = Str::limit(
                str_replace((string) $file->getRealPath(), '[fichier temporaire]', $e->getMessage()),
                350
            );

            Log::warning('catalogue.xlsx.read_failed', [
                'exception_type' => get_debug_type($e),
                'reason' => $reason,
                'zip_enabled' => extension_loaded('zip'),
            ]);

            if (! extension_loaded('zip') || ! class_exists(\\ZipArchive::class)) {
                $message = 'Extension PHP ZIP absente. Activez extension=zip dans le php.ini utilisé par WAMP, puis redémarrez PHP.';
            } elseif (app()->environment('local')) {
                $message = 'Lecture XLSX impossible ('.class_basename($e).') : '.$reason;
            } else {
                $message = 'Le fichier XLSX ne peut pas être lu. Vérifiez son format et son intégrité.';
            }

            throw ValidationException::withMessages(['file' => $message]);
        }

        try {
            $sheet = $spreadsheet->getSheetByName('Data Sheet');
            if (! $sheet) {
                throw ValidationException::withMessages([
                    'file' => 'La feuille « Data Sheet » est introuvable.',
                ]);
            }

            $this->assertHeaders($sheet);

            $end = $sheet->getHighestDataRow();
            if ($end > 2002) {
                throw ValidationException::withMessages([
                    'file' => 'Import limité à 2 000 lignes. Séparez le fichier en plusieurs lots.',
                ]);
            }

            $records = [];
            $seen = [];
            for ($rowNumber = 3; $rowNumber <= $end; $rowNumber++) {
                $sourceCategory = $this->value($sheet, 'B', $rowNumber);
                $internal = $this->value($sheet, 'C', $rowNumber);
                $external = $this->value($sheet, 'D', $rowNumber);
                $description = $this->value($sheet, 'E', $rowNumber);

                // Totals, empty separators and footers do not represent items.
                if ($external === '' && $sourceCategory === '' && $description === '') {
                    continue;
                }

                $issue = null;
                $status = CatalogImportRow::PENDING;
                $identity = mb_strtolower($external);

                if ($external === '' || mb_strlen($external) > 100 ||
                    preg_match('/[\x00-\x1F]/', $external) ||
                    str_starts_with($external, '=')) {
                    $status = CatalogImportRow::INVALID;
                    $issue = 'Référence publique absente ou invalide : contrôle requis.';
                } elseif (isset($seen[$identity])) {
                    $status = CatalogImportRow::DUPLICATE;
                    $issue = 'Même référence externe présente plusieurs fois dans le BOQ.';
                } elseif (Product::query()->where('sku', $external)->exists()) {
                    $status = CatalogImportRow::DUPLICATE;
                    $issue = 'Cette référence existe déjà dans le catalogue.';
                }

                if ($external !== '') {
                    $seen[$identity] = true;
                }

                if ($description === '' && $status === CatalogImportRow::PENDING) {
                    $status = CatalogImportRow::INVALID;
                    $issue = 'Description du modèle absente.';
                } elseif ($internal === '' && $status === CatalogImportRow::PENDING) {
                    $issue = 'Modèle interne non fourni ; vérifier la correspondance.';
                }

                $warranty = $this->value($sheet, 'O', $rowNumber);
                $warrantyMonths = ctype_digit($warranty) && (int) $warranty <= 120
                    ? (int) $warranty : null;

                $records[] = [
                    'source_row' => $rowNumber,
                    'source_category' => mb_substr($sourceCategory, 0, 160),
                    'suggested_category_name' => $this->categorySuggestion($sourceCategory, $external, $description),
                    'external_model' => $external === '' ? null : mb_substr($external, 0, 100),
                    'internal_model' => $internal === '' ? null : mb_substr($internal, 0, 150),
                    'part_number' => $this->nullableShort($this->value($sheet, 'M', $rowNumber), 100),
                    'description' => $this->nullableShort($description, 255),
                    'features' => $this->nullableShort($this->value($sheet, 'F', $rowNumber), 12000),
                    'warranty_months' => $warrantyMonths,
                    'status' => $status,
                    'issue' => $issue,
                ];
            }

            if ($records === []) {
                throw ValidationException::withMessages([
                    'file' => 'Le fichier ne contient aucune ligne produit exploitable.',
                ]);
            }

            return DB::transaction(function () use ($records, $actor): CatalogImportBatch {
                $batch = CatalogImportBatch::create([
                    'created_by' => $actor?->id,
                    'total_rows' => count($records),
                    'status' => 'review',
                ]);

                $categoryCache = [];
                foreach ($records as $record) {
                    $categoryName = $record['suggested_category_name'];
                    unset($record['suggested_category_name']);
                    if (! isset($categoryCache[$categoryName])) {
                        // Les catégories nouvelles restent masquées jusqu'à leur revue.
                        $categoryCache[$categoryName] = Category::firstOrCreate(
                            ['slug' => Str::slug($categoryName)],
                            ['name' => $categoryName, 'is_active' => false, 'sort_order' => 200],
                        )->id;
                    }
                    $record['suggested_category_id'] = $categoryCache[$categoryName];
                    $batch->rows()->create($record);
                }

                return $batch;
            });
        } finally {
            // Aucune conservation du BOQ original sur le serveur.
            $spreadsheet->disconnectWorksheets();
        }
    }

    private function assertHeaders(Worksheet $sheet): void
    {
        $expected = [
            'A' => 'Item',
            'B' => 'Category',
            'C' => 'Internal Model',
            'D' => 'External Model',
            'E' => 'General Description',
            'F' => 'Features',
            'M' => 'Part No.',
            'O' => 'Standard Warranty',
        ];

        foreach ($expected as $column => $label) {
            if (strcasecmp($this->value($sheet, $column, 1), $label) !== 0) {
                throw ValidationException::withMessages([
                    'file' => 'Structure BOQ non reconnue (colonne '.$column.').',
                ]);
            }
        }
    }

    private function value(Worksheet $sheet, string $column, int $row): string
    {
        $raw = $sheet->getCell($column.$row)->getValue();
        if (is_object($raw) && ! method_exists($raw, '__toString')) {
            return '';
        }

        return trim((string) ($raw ?? ''));
    }

    private function nullableShort(string $text, int $length): ?string
    {
        return $text === '' ? null : mb_substr($text, 0, $length);
    }

    public function categorySuggestion(string $source, string $sku, string $description): string
    {
        $source = mb_strtolower(trim($source));
        $model = mb_strtolower($sku.' '.$description);

        return match (true) {
            str_contains($source, 'hdcvi') => 'Caméras HDCVI',
            str_contains($source, 'wireless iot') => 'Caméras Wi-Fi',
            str_contains($source, 'cameras for overseas') => 'Caméras IP',
            str_contains($source, 'storage product') && str_contains($model, 'xvr') => 'Enregistreurs XVR',
            str_contains($source, 'storage product') && str_contains($model, 'nvr') => 'Enregistreurs NVR',
            str_contains($source, 'storage product') => 'Enregistreurs NVR',
            str_contains($source, 'intelligent building') &&
                (str_contains($model, 'intercom') || str_contains($model, 'door station')) => 'Interphonie',
            str_contains($source, 'intelligent building') => 'Contrôle d’accès',
            str_contains($source, 'basic network') => 'Réseaux & PoE',
            str_contains($source, 'fire alarm') => 'Détection incendie',
            $source === 'alarm' => 'Alarmes',
            str_contains($source, 'commercial display') => 'Écrans professionnels',
            str_contains($source, 'accessory') => 'Stockage & accessoires',
            default => 'Stockage & accessoires', // À corriger manuellement dans l'écran de revue.
        };
    }
}
