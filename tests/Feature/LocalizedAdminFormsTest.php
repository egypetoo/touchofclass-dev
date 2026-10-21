<?php

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Filament\Resources\BlogCategories\Pages\ManageBlogCategories;
use App\Filament\Resources\Blogs\Pages\ManageBlogs;
use App\Filament\Resources\Branches\Pages\CreateBranch;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Filament\Resources\GalleryImages\Pages\ManageGalleryImages;
use App\Filament\Resources\GalleryVideos\Pages\ManageGalleryVideos;
use App\Filament\Resources\Menus\Pages\ManageMenus;
use App\Filament\Resources\Partners\Pages\ManagePartners;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\ProjectCategories\Pages\ManageProjectCategories;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Sections\Pages\ManageSections;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Teams\Pages\ManageTeams;
use App\Models\Admin;
use App\Models\Section;
use App\Services\Translation\MyMemoryTranslator;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Filament::setCurrentPanel('admin');

    $admin = Admin::factory()->create();
    $admin->assignRole(Role::findOrCreate('super_admin', 'admin'));

    $this->actingAs($admin, 'admin');

    $this->section = Section::query()->create([
        'type' => SectionTypeEnum::HERO->value,
        'page' => PageEnum::HOME->value,
        'title' => ['ar' => 'عنوان قديم', 'en' => 'Old title'],
        'sub_title' => ['ar' => 'وصف قديم'],
        'order' => 1,
        'is_active' => true,
    ]);
});

it('loads and saves arabic and english values from the same form', function () {
    Livewire::withQueryParams(['page' => PageEnum::HOME->value])
        ->test(ManageSections::class)
        ->mountAction(TestAction::make('edit')->table($this->section))
        ->assertSchemaStateSet([
            'title.ar' => 'عنوان قديم',
            'title.en' => 'Old title',
            'sub_title.ar' => 'وصف قديم',
        ])
        ->fillForm([
            'title.ar' => 'عنوان جديد',
            'title.en' => 'New title',
            'sub_title.en' => 'New subtitle',
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $this->section->refresh();

    expect($this->section->getTranslations('title'))->toBe(['ar' => 'عنوان جديد', 'en' => 'New title'])
        ->and($this->section->getTranslations('sub_title'))->toBe(['ar' => 'وصف قديم', 'en' => 'New subtitle']);
});

it('shows arabic and english fields on every translated create form', function (string $page, string $field, bool $isModal, array $query) {
    $component = Livewire::withQueryParams($query)->test($page);

    if ($isModal) {
        $component->mountAction('create');
    }

    $component
        ->assertFormFieldExists("{$field}.ar")
        ->assertFormFieldExists("{$field}.en");
})->with([
    'blogs' => [ManageBlogs::class, 'title', true, []],
    'blog categories' => [ManageBlogCategories::class, 'name', true, []],
    'branches' => [CreateBranch::class, 'name', false, []],
    'categories' => [ManageCategories::class, 'name', true, []],
    'faqs' => [ManageFaqs::class, 'question', true, []],
    'gallery images' => [ManageGalleryImages::class, 'title', true, []],
    'gallery videos' => [ManageGalleryVideos::class, 'title', true, []],
    'menus' => [ManageMenus::class, 'title', true, []],
    'partners' => [ManagePartners::class, 'name', true, []],
    'products' => [CreateProduct::class, 'name', false, []],
    'project categories' => [ManageProjectCategories::class, 'name', true, []],
    'projects' => [CreateProject::class, 'title', false, []],
    'sections' => [ManageSections::class, 'title', true, ['page' => PageEnum::HOME->value]],
    'services' => [CreateService::class, 'title', false, []],
    'teams' => [ManageTeams::class, 'name', true, []],
]);

it('translates arabic into empty english fields only', function () {
    $this->mock(MyMemoryTranslator::class)
        ->shouldReceive('translateFields')
        ->once()
        ->with(['sub_title' => 'وصف قديم'], 'ar', 'en')
        ->andReturn(['sub_title' => 'Old description']);

    Livewire::withQueryParams(['page' => PageEnum::HOME->value])
        ->test(ManageSections::class)
        ->callAction([
            TestAction::make('edit')->table($this->section),
            TestAction::make('translateContent')->schemaComponent('translateContentActions'),
        ])
        ->assertSchemaStateSet([
            'title.en' => 'Old title',
            'sub_title.en' => 'Old description',
        ]);
});
