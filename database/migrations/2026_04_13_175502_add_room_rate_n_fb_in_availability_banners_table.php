<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('availability_banners', function (Blueprint $table) {
            $table->string('room_rate')->nullable()->after('image_3_path');
            $table->string('fb')->nullable()->after('room_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability_banners', function (Blueprint $table) {
            $table->dropColumn(['room_rate', 'fb']);
        });
    }
};
