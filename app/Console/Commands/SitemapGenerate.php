<?php

namespace App\Console\Commands;

use App\Services\Seo\BuildSitemapService;
use Illuminate\Console\Command;

class SitemapGenerate extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate XML sitemaps and robots.txt';

    public function handle(BuildSitemapService $service): int
    {
        $service->generateSitemap();
        $this->info('Sitemap and robots.txt generated successfully.');

        return self::SUCCESS;
    }
}
