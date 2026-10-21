<?php

namespace App\Enums;

enum ProductStatusEnum: string
{
    case FEATURED = 'Featured';
    case BEST_SELLERS = 'Best Sellers';
    case NEW = 'New';
    case COMMING_SOON = 'Coming Soon';
}
