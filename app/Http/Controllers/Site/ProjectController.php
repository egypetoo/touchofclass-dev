<?php

namespace App\Http\Controllers\Site;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Helper\Path;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Section;
use App\Services\SiteLayoutDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projectCategories = $this->projectAlbums();
        $requestedCategory = (string) request()->query('category', '');
        $activeProjectCategory = $projectCategories->firstWhere('slug', $requestedCategory)
            ?? $projectCategories->first();

        return view('site.projects.index', [
            'projects_breadcrumb' => Section::query()
                ->with('media')
                ->page(PageEnum::PROJECTS)
                ->type(SectionTypeEnum::BREADCRUMB)
                ->isActive()
                ->first(),
            'projectCategories' => $projectCategories,
            'activeProjectCategory' => $activeProjectCategory,
            'projectTabColumn' => match ($projectCategories->count()) {
                1 => 'col-12',
                2 => 'col-6',
                3 => 'col-6 col-lg-4',
                default => 'col-6 col-lg-3',
            },
        ]);
    }

    /**
     * @return Collection<int, array{slug: string, name: string, description: string, icon: string, projects: list<array{name: string, value: int, images: list<string>}>}>
     */
    private function projectAlbums(): Collection
    {
        return ProjectCategory::query()
            ->isActive()
            ->orderBy('order')
            ->with(['projects' => fn ($query) => $query->isActive()->orderBy('order')->with('media')])
            ->get()
            ->map(function (ProjectCategory $category): array {
                return [
                    'slug' => (string) $category->getTranslation('slug', 'en'),
                    'name' => (string) $category->name,
                    'description' => trim(strip_tags((string) $category->short_text)),
                    'icon' => filled($category->icon) ? (string) $category->icon : 'fa-solid fa-box',
                    'projects' => $category->projects->map(function (Project $project): array {
                        $images = $project->galleryLightboxUrls()->filter()->values()->all();

                        if ($images === []) {
                            $images = [$project->coverUrl() ?: asset('assets/images/studio/zayed-villa.jpg')];
                        }

                        return [
                            'name' => (string) $project->title,
                            'value' => (int) $project->order,
                            'images' => array_slice($images, 0, 4),
                        ];
                    })->all(),
                ];
            })
            ->filter(fn (array $category): bool => $category['projects'] !== [])
            ->values();
    }

    public function show(Project $project, SiteLayoutDataService $layoutData): View|RedirectResponse
    {
        abort_unless($project->is_active, 404);

        if ($redirect = $project->redirectIfSlugLocaleMismatch()) {
            return $redirect;
        }

        $project->load('media');

        $industries = Project::query()
            ->isActive()
            ->orderBy('order')
            ->get(['id', 'title', 'slug', 'order']);

        $siteSetting = $layoutData->siteSetting();
        $industryPosition = $industries->search(fn (Project $industry): bool => $industry->is($project));
        $galleryImages = $project->galleryLightboxUrls()->all();

        return view('site.projects.show', [
            'project' => $project,
            'industries' => $industries,
            'industryNumber' => str_pad((string) (($industryPosition === false ? 0 : $industryPosition) + 1), 2, '0', STR_PAD_LEFT),
            'galleryImages' => $galleryImages ?: [Path::imagesPath(Path::projectDesignImages()[0])],
            'bannerImage' => $project->coverUrl() ?: Path::imagesPath(Path::projectDesignImages()[0]),
            'turnstileEnabled' => $siteSetting?->isTurnstileEnabled() ?? false,
            'turnstileSiteKey' => $siteSetting?->turnstile_site_key,
        ]);
    }
}
