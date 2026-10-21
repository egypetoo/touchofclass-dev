<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Support\ArabicTransliterator;
use Illuminate\Console\Command;

class FixProductEnglishNamesCommand extends Command
{
    protected $signature = 'atlantis:fix-product-english-names';

    protected $description = 'Set English product names from Arabic via transliteration when EN still contains Arabic';

    public function handle(): int
    {
        $updated = 0;

        Product::query()->withTrashed()->chunkById(100, function ($products) use (&$updated): void {
            foreach ($products as $product) {
                $nameAr = (string) $product->getTranslation('name', 'ar', false);
                $nameEn = (string) $product->getTranslation('name', 'en', false);

                if ($nameAr === '' || ! ArabicTransliterator::containsArabic($nameAr)) {
                    continue;
                }

                if ($nameEn !== '' && ! ArabicTransliterator::containsArabic($nameEn) && $nameEn !== $nameAr) {
                    continue;
                }

                $latin = ArabicTransliterator::toLatin($nameAr);
                $product->setTranslation('name', 'en', $latin);
                $product->setTranslation('meta_title', 'en', $latin);
                $product->setTranslation('meta_desc', 'en', $latin);

                $descAr = strip_tags((string) $product->getTranslation('description', 'ar', false));
                if ($descAr === '' || ArabicTransliterator::containsArabic($descAr)) {
                    $product->setTranslation('description', 'en', '<p>'.$latin.'</p>');
                }

                $product->save();
                $updated++;
            }
        });

        $this->info("Updated {$updated} product English names.");

        return self::SUCCESS;
    }
}
