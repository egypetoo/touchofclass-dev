<?php

namespace App\Http\Controllers\Site;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Helper\Path;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Section;
use App\Services\SiteLayoutDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('site.projects.index', [
            'projects_breadcrumb' => Section::query()
                ->with('media')
                ->page(PageEnum::PROJECTS)
                ->type(SectionTypeEnum::BREADCRUMB)
                ->isActive()
                ->first(),
            'projects' => Project::query()
                ->with('media')
                ->isActive()
                ->orderBy('order')
                ->get(),
        ]);
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
