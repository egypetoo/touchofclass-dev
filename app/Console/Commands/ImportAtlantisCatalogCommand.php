<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Support\AtlantisCatalogImporter;
use Illuminate\Console\Command;

class ImportAtlantisCatalogCommand extends Command
{
    protected $signature = 'atlantis:import-catalog
                            {path? : Absolute path to the Atlantis Trade catalog folder}
                            {--keep-existing : Do not delete existing categories/products before import}';

    protected $description = 'Wipe current catalog data and import categories, subcategories, and products from the client folder';

    public function handle(AtlantisCatalogImporter $importer): int
    {
        $path = $this->argument('path')
            ?: config('atlantis.catalog_path')
            ?: env('ATLANTIS_CATALOG_PATH');

        if (! is_string($path) || $path === '') {
            $this->error('Provide a catalog path argument or set ATLANTIS_CATALOG_PATH.');

            return self::FAILURE;
        }

        if (! is_dir($path)) {
            $this->error("Catalog path not found: {$path}");

            return self::FAILURE;
        }

        $organization = Organization::query()->first();

        if (! $organization) {
            $this->error('No organization found. Seed OrganizationSeeder first.');

            return self::FAILURE;
        }

        $purge = ! $this->option('keep-existing');

        if ($purge) {
            $this->warn('Deleting all existing categories and products...');
        }

        $this->info("Importing catalog from: {$path}");

        $stats = $importer->import(
            $organization,
            $path,
            replaceExisting: $purge
        );

        $this->table(
            ['Metric', 'Count'],
            [
                ['Categories', $stats['categories']],
                ['Subcategories', $stats['subcategories']],
                ['Products', $stats['products']],
            ]
        );

        $this->info('Catalog import completed.');

        return self::SUCCESS;
    }
}
