<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PageEnum: string implements HasLabel
{
    /**
     * @return array<SectionTypeEnum>
     */
    public function sectionTypes(): array
    {
        return match ($this) {
            self::HOME => [
                SectionTypeEnum::HERO,
                SectionTypeEnum::SERVICES,
                SectionTypeEnum::FEATURED_PRODUCTS,
                SectionTypeEnum::ABOUTUS,
                SectionTypeEnum::WHY_CHOOSE_US,
                SectionTypeEnum::BENEFITS,
                SectionTypeEnum::STATISTICS,
                SectionTypeEnum::PROJECTS,
                SectionTypeEnum::PROCESS,
                SectionTypeEnum::PROCESS_STEP,
                SectionTypeEnum::CERTIFICATES,
                SectionTypeEnum::CERTIFICATE,
                SectionTypeEnum::BLOGS,
                SectionTypeEnum::CONTACT_US_FORM,
                SectionTypeEnum::CONTACT_US_CONTENT,
                SectionTypeEnum::CLIENTS,
            ],
            self::SERVICES => [
                SectionTypeEnum::BREADCRUMB_IMAGE,
            ],
            self::GALLERY => [
                SectionTypeEnum::BREADCRUMB,
            ],
            self::ABOUT => [
                SectionTypeEnum::ABOUT_BREADCRUMB,
                SectionTypeEnum::ABOUTUS,
                SectionTypeEnum::STATISTICS,
                SectionTypeEnum::VISION,
                SectionTypeEnum::MISSION,
            ],
            self::CONTACT => [
                SectionTypeEnum::CONTACT_US_BREADCRUMB,
                SectionTypeEnum::CONTACT_US_FORM,
                SectionTypeEnum::CONTACT_US_CONTENT,
                SectionTypeEnum::BENEFITS,
                SectionTypeEnum::CONTENT,
                SectionTypeEnum::FEATURES,
            ],
            self::GALLERY_VIDEOS => [
                SectionTypeEnum::BREADCRUMB,
            ],
            self::TRAINING => [
                SectionTypeEnum::BREADCRUMB,
                SectionTypeEnum::CONTENT,
                SectionTypeEnum::VALUES,
                SectionTypeEnum::FAQ,
            ],
            self::MEDICAL_TOURISM => [
                SectionTypeEnum::BREADCRUMB,
            ],
            self::BLOGS => [
                SectionTypeEnum::BREADCRUMB,
            ],
            self::PROJECTS => [
                SectionTypeEnum::BREADCRUMB,
            ],
            self::PRODUCTS => [
                SectionTypeEnum::BREADCRUMB,
            ],
            self::CLIENTS => [
                SectionTypeEnum::BREADCRUMB,
            ],
            self::PRIVACY_POLICY => [
                SectionTypeEnum::CONTENT,
            ],
            self::TERMS_AND_CONDITIONS => [
                SectionTypeEnum::CONTENT,
            ],

        };
    }

    /**
     * @return array<string, string>
     */
    public function sectionTypeOptions(): array
    {
        return collect($this->sectionTypes())
            ->mapWithKeys(fn (SectionTypeEnum $type): array => [$type->value => $type->getLabel()])
            ->all();
    }

    case HOME = 'home';
    case ABOUT = 'about';
    case GALLERY = 'gallery';
    case SERVICES = 'services';
    case GALLERY_VIDEOS = 'gallery_videos';
    case CONTACT = 'contact';
    case BLOGS = 'blogs';
    case TRAINING = 'training';
    case MEDICAL_TOURISM = 'medical_tourism';
    case PROJECTS = 'projects';
    case PRODUCTS = 'products';
    case CLIENTS = 'clients';
    case PRIVACY_POLICY = 'privacy_policy';
    case TERMS_AND_CONDITIONS = 'terms_and_conditions';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HOME => 'Home',
            self::ABOUT => 'About Us',
            self::GALLERY => 'Gallery',
            self::SERVICES => 'Services',
            self::GALLERY_VIDEOS => 'Video Gallery',
            self::CONTACT => 'Contact',
            self::BLOGS => 'Our Blogs',
            self::TRAINING => 'Training',
            self::MEDICAL_TOURISM => 'Medical Tourism',
            self::PROJECTS => 'Industries',
            self::PRODUCTS => 'Products',
            self::CLIENTS => 'Clients',
            self::PRIVACY_POLICY => 'Privacy Policy',
            self::TERMS_AND_CONDITIONS => 'Terms and Conditions',
        };
    }
}
