<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MenuRouteNameEnum: string implements HasLabel
{
    case PARENT_MENU_ONLY = '#';
    case HOME = 'home';
    case ABOUT_US = 'about-us';
    case SERVICES = 'services.index';
    case PRODUCTS = 'products.index';
    case CATEGORIES = 'categories.index';
    case PARTNERS = 'partners.index';
    case PROJECTS = 'projects.index';
    case NEWS = 'news.index';
    case PUMP_SELECTOR = 'pump-selector';
    case CONTACT_US = 'contact-us';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PARENT_MENU_ONLY => 'Parent Menu Only',
            self::HOME => 'Home Page',
            self::ABOUT_US => 'About Us Page',
            self::SERVICES => 'Solutions Page',
            self::PRODUCTS => 'Products Page',
            self::CATEGORIES => 'Categories Page',
            self::PARTNERS => 'Clients Page',
            self::PROJECTS => 'Industries Page',
            self::NEWS => 'News Page',
            self::PUMP_SELECTOR => 'Pump Selector Page',
            self::CONTACT_US => 'Contact Us Page',
        };
    }
}
