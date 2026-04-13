<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_banner_path',
        'logo_path',
        'room_image_1_path',
        'room_image_2_path',
        'room_image_3_path',
        'room_image_4_path',
        'generated_banner_path',
        'contact_info',
    ];

    protected $casts = [
        'contact_info' => 'array',
    ];

    public function offerDetails(): HasMany
    {
        return $this->hasMany(PromotionOfferDetail::class)->orderBy('row_order');
    }
}

