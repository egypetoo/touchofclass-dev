<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class B2bInquiry extends Model
{
    protected $fillable = [
        'organization_id',
        'company_name',
        'first_name',
        'last_name',
        'street_address',
        'apartment_floor',
        'city',
        'governorate',
        'postal_code',
        'country',
        'email',
        'phone',
        'message',
        'is_read',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
