<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability_banners', function (Blueprint $table) {
            $table->id();
            $table->date('from_date');
            $table->date('to_date');
            $table->string('hotel_name')->default('Jiwar Rawra For Hotel');
            $table->string('generated_banner_path')->nullable();
            $table->string('image_1_path')->nullable();
            $table->string('image_2_path')->nullable();
            $table->string('image_3_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_banners');
    }
};
