<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClientSectorEnum: string implements HasLabel
{
    case FoodBeverages = 'food-beverages';
    case AgricultureFrozen = 'agriculture-frozen';
    case HomeAppliance = 'home-appliance';
    case ElectronicsEcommerce = 'electronics-ecommerce';
    case Ceramics = 'ceramics';
    case ClothesMattresses = 'clothes-mattresses';
    case CosmeticsPharmaceutical = 'cosmetics-pharmaceutical';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FoodBeverages => __('site.sector_food_beverages'),
            self::AgricultureFrozen => __('site.sector_agriculture_frozen'),
            self::HomeAppliance => __('site.sector_home_appliance'),
            self::ElectronicsEcommerce => __('site.sector_electronics_ecommerce'),
            self::Ceramics => __('site.sector_ceramics'),
            self::ClothesMattresses => __('site.sector_clothes_mattresses'),
            self::CosmeticsPharmaceutical => __('site.sector_cosmetics_pharmaceutical'),
        };
    }

    public function iconClass(): string
    {
        return match ($this) {
            self::FoodBeverages => 'fas fa-utensils',
            self::AgricultureFrozen => 'fas fa-seedling',
            self::HomeAppliance => 'fas fa-blender',
            self::ElectronicsEcommerce => 'fas fa-plug',
            self::Ceramics => 'fas fa-border-all',
            self::ClothesMattresses => 'fas fa-tshirt',
            self::CosmeticsPharmaceutical => 'fas fa-pills',
        };
    }
}
