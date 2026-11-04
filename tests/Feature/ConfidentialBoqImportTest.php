<?php

namespace Tests\Feature;

use App\Models\CatalogImportBatch;
use App\Models\CatalogImportRow;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ConfidentialBoqImportTest extends TestCase
{
    use RefreshDatabase;

    private function admin(bool $authorized = true): User
    {
        return User::create([
            'name' => $authorized ? 'Responsable catalogue' : 'Utilisateur non autorisé',
            'email' => $authorized ? 'catalogue@example.test' : 'visiteur@example.test',
            'password' => 'test-password-longue-123',
            'is_admin' => $authorized,
        ]);
    }

    /**
     * All test data are invented. Never commit a real supplier BOQ as a fixture.
     */
    private function fakeBoq(bool $correctHeaders = true): UploadedFile
    {
        $book = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Data Sheet');

        $columns = [
            'A' => 'Item', 'B' => 'Category', 'C' => 'Internal Model',
            'D' => 'External Model', 'E' => 'General Description',
            'F' => 'Features', 'J' => 'Quantity',
            'K' => 'Unit Price(VAT exclusive)',
            'L' => 'Total Amount(VAT exclusive)',
            'M' => 'Part No.', 'O' => 'Standard Warranty',
        ];
        foreach ($columns as $column => $header) {
            $sheet->setCellValue($column.'1', $correctHeaders ? $header : 'Invalid heading');
        }
        $rows = [
            [1, 'Cameras for Overseas Distribution Channels', 'DH-INTERNAL-EXAMPLE-A',
                'DH-EXAMPLE-IP-A', 'Synthetic 4MP Bullet Camera', '4MP; PoE; IR',
                300, 13.67, 4101, '9.8.7.6.001', 36],
            [2, 'Cameras for Overseas Distribution Channels', 'DH-INTERNAL-EXAMPLE-B',
                'DH-EXAMPLE-IP-A', 'Duplicate synthetic variant', '4MP; PoE',
                500, 9.99, 4995, '9.8.7.6.002', 24],
            [3, 'Intelligent Building', null,
                'IC-TEST-S50', 'Synthetic access card', 'Test access control',
                1000, 0.7, 700, '9.8.7.6.003', 12],
        ];
        foreach ($rows as $index => $row) {
            $r = $index + 3;
            foreach (['A','B','C','D','E','F','J','K','L','M','O'] as $i => $column) {
                if ($row[$i] !== null) {
                    $sheet->setCellValue($column.$r, $row[$i]);
                }
            }
        }
        $sheet->setCellValue('K6', 'Total Amount');
        $sheet->setCellValue('L6', 9796);

        $path = tempnam(sys_get_temp_dir(), 'test-boq-').'.xlsx';
        (new Xlsx($book))->save($path);
        $book->disconnectWorksheets();

        $file = UploadedFile::fake()->createWithContent('synthetic-boq.xlsx', file_get_contents($path));
        @unlink($path);

        return $file;
    }

    private function stage(): CatalogImportBatch
    {
        $this->actingAs($this->admin())
            ->post(route('admin.imports.store'), [
                'file' => $this->fakeBoq(),
                'confidentiality_ack' => '1',
            ])
            ->assertRedirect();

        return CatalogImportBatch::firstOrFail();
    }

    public function test_upload_is_admin_only(): void
    {
        $this->get(route('admin.imports.index'))->assertRedirect(route('login'));
        $this->actingAs($this->admin(false))
            ->post(route('admin.imports.store'), [
                'file' => $this->fakeBoq(),
                'confidentiality_ack' => 1,
            ])->assertForbidden();

        $this->assertDatabaseCount('catalog_import_batches', 0);
    }

    public function test_staging_never_saves_vendor_costs_or_procurement_quantity(): void
    {
        $batch = $this->stage();

        $this->assertSame(3, $batch->total_rows);
        $this->assertDatabaseCount('catalog_import_rows', 3);
        $this->assertDatabaseCount('products', 0);

        $rows = $batch->rows()->orderBy('source_row')->get();
        $this->assertSame(CatalogImportRow::PENDING, $rows[0]->status);
        $this->assertSame(CatalogImportRow::DUPLICATE, $rows[1]->status);
        $this->assertSame(CatalogImportRow::PENDING, $rows[2]->status);
        $this->assertNull($rows[2]->internal_model);

        $data = $rows[0]->getAttributes();
        foreach (['unit_price', 'price_usd', 'amount_usd', 'procurement_quantity', 'total_amount'] as $forbidden) {
            $this->assertArrayNotHasKey($forbidden, $data);
        }
        $this->assertFalse(str_contains(json_encode($data), '4101'));
        $this->assertSame(36, $rows[0]->warranty_months);
        $this->assertSame('DH-EXAMPLE-IP-A', $rows[0]->external_model);
    }

    public function test_approval_creates_nonpublic_unpriced_zero_stock_product(): void
    {
        $batch = $this->stage();
        $row = $batch->rows()->where('status', CatalogImportRow::PENDING)->firstOrFail();

        $this->post(route('admin.imports.approve', [$batch, $row]), [
            'category_id' => $row->suggested_category_id,
        ])->assertRedirect();

        $product = Product::firstOrFail();
        $this->assertSame('DH-EXAMPLE-IP-A', $product->sku);
        $this->assertSame('Dahua', $product->brand);
        $this->assertNull($product->price_gnf);
        $this->assertSame(0, $product->stock_qty);
        $this->assertFalse($product->is_active);
        $this->assertDatabaseHas('catalog_import_rows', [
            'id' => $row->id, 'product_id' => $product->id,
            'status' => CatalogImportRow::APPROVED,
        ]);

        $this->get(route('product.show', $product->slug))->assertNotFound();
        $this->post(route('admin.imports.approve', [$batch, $row]), [
            'category_id' => $row->suggested_category_id,
        ])->assertSessionHasErrors('row');
    }

    public function test_duplicate_cannot_be_approved_or_overwrite_product(): void
    {
        $batch = $this->stage();
        $duplicate = $batch->rows()->where('status', CatalogImportRow::DUPLICATE)->firstOrFail();

        $this->post(route('admin.imports.approve', [$batch, $duplicate]), [
            'category_id' => $duplicate->suggested_category_id,
        ])->assertSessionHasErrors('row');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_import_can_be_purged_without_deleting_approved_draft(): void
    {
        $batch = $this->stage();
        $row = $batch->rows()->where('status', CatalogImportRow::PENDING)->firstOrFail();
        $this->post(route('admin.imports.approve', [$batch, $row]), [
            'category_id' => $row->suggested_category_id,
        ]);

        $this->delete(route('admin.imports.purge', $batch), ['confirmation' => 'SUPPRIMER'])
            ->assertRedirect(route('admin.imports.index'));
        $this->assertDatabaseCount('catalog_import_batches', 0);
        $this->assertDatabaseCount('catalog_import_rows', 0);
        $this->assertDatabaseCount('products', 1);
    }

    public function test_bad_header_and_missing_consent_are_rejected(): void
    {
        $this->actingAs($this->admin());
        $this->post(route('admin.imports.store'), [
            'file' => $this->fakeBoq(correctHeaders: false),
            'confidentiality_ack' => '1',
        ])->assertSessionHasErrors('file');

        $this->post(route('admin.imports.store'), [
            'file' => $this->fakeBoq(),
        ])->assertSessionHasErrors('confidentiality_ack');

        $this->assertDatabaseCount('catalog_import_batches', 0);
    }

    public function test_row_from_another_batch_cannot_be_approved(): void
    {
        $first = $this->stage();
        $this->post(route('admin.imports.store'), [
            'file' => $this->fakeBoq(),
            'confidentiality_ack' => '1',
        ])->assertRedirect();
        $second = CatalogImportBatch::latest('id')->firstOrFail();
        $foreignRow = $second->rows()->firstOrFail();

        $this->post(route('admin.imports.approve', [$first, $foreignRow]), [
            'category_id' => Category::firstOrFail()->id,
        ])->assertNotFound();
    }
}
