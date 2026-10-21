<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SocialLinkPlatform: string implements HasLabel
{
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case X = 'x';
    case Youtube = 'youtube';
    case LinkedIn = 'linkedin';
    case TikTok = 'tiktok';
    case Snapchat = 'snapchat';
    case Website = 'website';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Facebook => __('admin_trans.social_platform_facebook'),
            self::Instagram => __('admin_trans.social_platform_instagram'),
            self::X => __('admin_trans.social_platform_x'),
            self::Youtube => __('admin_trans.social_platform_youtube'),
            self::LinkedIn => __('admin_trans.social_platform_linkedin'),
            self::TikTok => __('admin_trans.social_platform_tiktok'),
            self::Snapchat => __('admin_trans.social_platform_snapchat'),
            self::Website => __('admin_trans.social_platform_website'),
            self::Other => __('admin_trans.social_platform_other'),
        };
    }

    public function iconClass(): string
    {
        return match ($this) {
            self::Facebook => 'fa-brands fa-facebook-f',
            self::Instagram => 'fa-brands fa-instagram',
            self::X => 'fa-brands fa-x-twitter',
            self::Youtube => 'fa-brands fa-youtube',
            self::LinkedIn => 'fa-brands fa-linkedin-in',
            self::TikTok => 'fa-brands fa-tiktok',
            self::Snapchat => 'fa-brands fa-snapchat',
            self::Website, self::Other => 'fa-solid fa-globe',
        };
    }
}
