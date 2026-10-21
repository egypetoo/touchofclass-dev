<?php

use App\Filament\Resources\BlogCategories\BlogCategoryResource;
use App\Filament\Resources\Blogs\BlogResource;
use App\Filament\Resources\Branches\BranchResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\GalleryImages\GalleryImageResource;
use App\Filament\Resources\GalleryVideos\GalleryVideoResource;
use App\Filament\Resources\Menus\MenuResource;
use App\Filament\Resources\Partners\PartnerResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\ProjectCategories\ProjectCategoryResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Sections\SectionResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Teams\TeamResource;
use App\Models\Faq;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

$translatableResources = [
    FaqResource::class,
    ServiceResource::class,
    GalleryImageResource::class,
    CategoryResource::class,
    TeamResource::class,
    GalleryVideoResource::class,
    MenuResource::class,
    PartnerResource::class,
    BlogCategoryResource::class,
    BranchResource::class,
    ProjectResource::class,
    SectionResource::class,
    BlogResource::class,
    ProductResource::class,
    ProjectCategoryResource::class,
];

it('does not use the locale switcher on translated model resources', function () use ($translatableResources) {
    foreach ($translatableResources as $resource) {
        expect(in_array(Translatable::class, class_uses_recursive($resource), true))
            ->toBeFalse("{$resource} should edit all locales in one form instead of switching locale");
    }
});

it('renders every translatable attribute as arabic and english fields in the same form', function () use ($translatableResources) {
    foreach ($translatableResources as $resource) {
        $contents = file_get_contents((new ReflectionClass($resource))->getFileName());
        $attributes = (new ($resource::getModel()))->getTranslatableAttributes();

        foreach ($attributes as $attribute) {
            expect($contents)->not->toMatch(
                "/(TextInput|Textarea|RichEditor)::make\\('{$attribute}'\\)/",
                "{$resource} renders translatable '{$attribute}' without LocalizedField"
            );
        }

        expect($contents)->toContain('LocalizedField::make(');
    }
});

it('stores faq translations per locale with setTranslation', function () {
    $faq = new Faq;
    $faq->setTranslation('question', 'en', 'What is veneers?');
    $faq->setTranslation('question', 'ar', 'ما هي الفينير؟');
    $faq->setTranslation('answer', 'en', 'Dental veneers improve smile aesthetics.');
    $faq->setTranslation('answer', 'ar', 'فينير الأسنان يحسن جمال الابتسامة.');
    $faq->sort_order = 1;
    $faq->is_active = true;
    $faq->save();

    $faq->refresh();

    expect($faq->getTranslation('question', 'en'))->toBe('What is veneers?')
        ->and($faq->getTranslation('question', 'ar'))->toBe('ما هي الفينير؟')
        ->and($faq->getTranslation('answer', 'en'))->toBe('Dental veneers improve smile aesthetics.')
        ->and($faq->getTranslation('answer', 'ar'))->toBe('فينير الأسنان يحسن جمال الابتسامة.');
});
