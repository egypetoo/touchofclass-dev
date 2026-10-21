<?php

namespace App\Enums;

enum ProductDiscountType: string
{
    case Percentage = 'percentage';
    case FixedAmount = 'fixed_amount';

    public function label(): string
    {
        return match ($this) {
            self::Percentage => __('admin_trans.discount_type_percentage'),
            self::FixedAmount => __('admin_trans.discount_type_fixed_amount'),
        };
    }
}
