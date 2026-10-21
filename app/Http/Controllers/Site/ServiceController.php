<?php

namespace App\Http\Controllers\Site;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Helper\Path;
use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Service;
use App\Services\SiteLayoutDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::query()
            ->with('media')
            ->roots()
            ->isActive()
            ->orderBy('order')
            ->get();

        return view('site.services.index', [
            'services_breadcrumb' => $this->breadcrumb(),
            'serviceCards' => $this->mapServiceCards($services),
        ]);
    }

    public function show(Service $service, SiteLayoutDataService $layoutData): View|RedirectResponse
    {
        if ($redirect = $service->redirectIfSlugLocaleMismatch()) {
            return $redirect;
        }

        $service->load([
            'media',
            'parent',
            'children' => fn ($query) => $query->with('media')->isActive()->orderBy('order'),
        ]);

        $data['services_breadcrumb'] = $this->breadcrumb();
        $data['service'] = $service;

        if ($service->children->isNotEmpty()) {
            $data['serviceCards'] = $this->mapServiceCards($service->children);

            return view('site.services.children', $data);
        }

        $siteSetting = $layoutData->siteSetting();
        $data['turnstileEnabled'] = $siteSetting?->isTurnstileEnabled() ?? false;
        $data['turnstileSiteKey'] = $siteSetting?->turnstile_site_key;

        $siblings = Service::query()
            ->with('media')
            ->when(
                $service->parent_id,
                fn ($query) => $query->where('parent_id', $service->parent_id),
                fn ($query) => $query->roots(),
            )
            ->isActive()
            ->orderBy('order')
            ->get();

        $data['related_services'] = $siblings;
        $data['solutionNumber'] = str_pad((string) (max(0, $siblings->search(fn (Service $sibling): bool => $sibling->is($service))) + 1), 2, '0', STR_PAD_LEFT);
        $data['galleryImages'] = $service->galleryImageUrls() ?: [Path::imagesPath('products/product-card-1.jpg')];

        return view('site.services.show', $data);
    }

    private function breadcrumb(): ?Section
    {
        return Section::query()
            ->with('media')
            ->page(PageEnum::SERVICES)
            ->type(SectionTypeEnum::BREADCRUMB_IMAGE)
            ->IsActive()
            ->first();
    }

    /**
     * @param  Collection<int, Service>  $services
     * @return Collection<int, object{title: string, label: ?string, excerpt: string, url: string, imageUrl: string, number: string}>
     */
    protected function mapServiceCards(Collection $services): Collection
    {
        $serviceFallbacks = Path::serviceDesignImages();

        return $services->values()->map(fn (Service $service, int $index): object => (object) [
            'title' => (string) $service->title,
            'label' => $service->label,
            'excerpt' => strip_tags((string) $service->short_text),
            'url' => route('services.show', $service),
            'imageUrl' => $service->mainImageUrl()
                ?: Path::imagesPath($serviceFallbacks[$index % count($serviceFallbacks)]),
            'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
        ]);
    }
}
