<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SectionTypeEnum: string implements HasLabel
{
    case GALLERY = 'gallery';
    case HERO = 'hero';
    case SERVICES = 'services';
    case TEAMS = 'teams';
    case CATEGORIES = 'categories';
    case VIDEO = 'video';
    case FEATURED_PRODUCTS = 'featured_products';
    case PROJECTS = 'projects';
    case COLLECTION = 'collection';
    case BEST_SELLERS = 'best_sellers';
    case WHY_CHOOSE_US_BREADCRUMB = 'why_choose_us_breadcrumb';
    case WHY_CHOOSE_US = 'why_choose_us';
    case BENEFITS = 'benefits';
    case BLOGS = 'blogs';

    // ABOUT US SECTIONS
    case ABOUTUS = 'about_us';
    case VALUES = 'values';
    case MISSION = 'mission';
    case VISION = 'vision';
    case ABOUT_BREADCRUMB = 'about_breadcrumb';
    case STATISTICS = 'statistics';

    // BRANCHES SECTIONS
    case BRANCHES_BREADCRUMB = 'branches_breadcrumb';

    // CONTACT US SECTIONS
    case CONTACT_US_BREADCRUMB = 'contact_us_breadcrumb';
    case CONTACT_US_FORM = 'contact_us_form';
    case CONTACT_US_CONTENT = 'contact_us_content';

    // TRAINING SECTIONS
    case BREADCRUMB = 'breadcrumb';
    case CONTENT = 'content';
    case FAQ = 'faq';

    // B2B SECTIONS
    case B2B_BANNER = 'b2b_banner';
    case B2B_CONTENT = 'b2b_content';
    case B2B_FORM_CONTENT = 'b2b_form_content';
    case FIND_BY_STYLE_BREADCRUMB = 'find_by_style_breadcrumb';
    case SLIDER = 'slider';
    case DISCOUNT_BANNER = 'discount_banner';
    case FEATURES = 'features';
    case TESTIMONIALS = 'testimonials';
    case CTA = 'cta';
    case BREADCRUMB_IMAGE = 'breadcrumb_image';
    case GALLERY_IMAGES = 'gallery_images';
    case GALLERY_VIDEOS = 'gallery_videos';

    // MINA PACK HOME SECTIONS
    case PROCESS = 'process';
    case PROCESS_STEP = 'process_step';
    case CERTIFICATES = 'certificates';
    case CERTIFICATE = 'certificate';
    case CLIENTS = 'clients';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GALLERY => 'Gallery Section',
            self::HERO => 'Hero Section',
            self::SERVICES => 'Services Section',
            self::TEAMS => 'Teams Section',
            self::CATEGORIES => 'Categories Section',
            self::VIDEO => 'Video Section',
            self::FEATURED_PRODUCTS => 'Featured Products Section',
            self::PROJECTS => 'Projects Section',
            self::COLLECTION => 'Collection Section',
            self::BEST_SELLERS => 'Best Sellers Section',
            self::WHY_CHOOSE_US_BREADCRUMB => 'Why Choose Us Breadcrumb Section',
            self::WHY_CHOOSE_US => 'Why Choose Us Section',
            self::BENEFITS => 'Benefits Section',
            self::BLOGS => 'Blogs Section',
            // ABOUT US SECTIONS
            self::ABOUT_BREADCRUMB => 'About Breadcrumb Section',
            self::ABOUTUS => 'About Us Section',
            self::VALUES => 'Values Section',
            self::MISSION => 'Mission Section',
            self::VISION => 'Vision Section',
            self::STATISTICS => 'Statistics Section',
            // BRANCHES SECTIONS
            self::BRANCHES_BREADCRUMB => 'Branches Breadcrumb Section',

            // CONTACT US SECTIONS
            self::CONTACT_US_BREADCRUMB => 'Contact Us Breadcrumb Section',
            self::CONTACT_US_FORM => 'Contact Us Form Section',
            self::CONTACT_US_CONTENT => 'Contact Us Content Section',

            // B2B SECTIONS
            self::B2B_BANNER => 'B2B Banner',
            self::B2B_CONTENT => 'B2B Content',
            self::B2B_FORM_CONTENT => 'B2B Form Content',

            self::FIND_BY_STYLE_BREADCRUMB => 'Find by Style Breadcrumb Section',

            self::SLIDER => 'Slider Section',
            self::DISCOUNT_BANNER => 'Discount Banner',
            self::FEATURES => 'Features Section',
            self::TESTIMONIALS => 'Testimonials Section',
            self::CTA => 'Call to Action',
            self::CONTENT => 'Content Section',
            self::BREADCRUMB_IMAGE => 'Breadcrumb Image',
            self::GALLERY_IMAGES => 'Gallery Images',
            self::GALLERY_VIDEOS => 'Gallery Videos',
            self::BREADCRUMB => 'Breadcrumb',
            self::FAQ => 'FAQ Section',
            self::PROCESS => 'Process Section',
            self::PROCESS_STEP => 'Process Step',
            self::CERTIFICATES => 'Certificates Section',
            self::CERTIFICATE => 'Certificate',
            self::CLIENTS => 'Clients Section',
        };
    }
}
