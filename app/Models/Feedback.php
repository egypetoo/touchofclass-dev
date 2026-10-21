<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'organization_id',
        'name',
        'date_of_visit',
        'satisfaction',
        'staff_friendliness',
        'service_speed',
        'product_quality',
        'value_for_money',
        'hear_about_us',
        'improvements',
        'additional_comments',
        'recommendation',
        'is_read',
    ];

    protected $casts = [
        'date_of_visit' => 'date',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
