<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailabilityBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_date',
        'to_date',
        'hotel_name',
        'room_rate',
        'fb',
        'generated_banner_path',
        'image_1_path',
        'image_2_path',
        'image_3_path',
        'contact_info',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'contact_info' => 'array',
    ];
}
