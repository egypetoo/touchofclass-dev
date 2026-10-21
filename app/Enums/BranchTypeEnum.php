<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BranchTypeEnum: string implements HasLabel
{
    case Main = 'main';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Main => __('admin_trans.branch_type_main'),
            self::Other => __('admin_trans.branch_type_other'),
        };
    }
}
