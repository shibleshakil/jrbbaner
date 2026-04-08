<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionOfferDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'from_date',
        'to_date',
        'double_rate',
        'triple_rate',
        'quad_rate',
        'meals',
        'row_order',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }
}

