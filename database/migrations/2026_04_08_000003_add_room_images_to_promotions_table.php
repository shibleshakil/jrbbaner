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
        Schema::table('promotions', function (Blueprint $table) {
            $table->string('room_image_1_path')->nullable()->after('logo_path');
            $table->string('room_image_2_path')->nullable()->after('room_image_1_path');
            $table->string('room_image_3_path')->nullable()->after('room_image_2_path');
            $table->string('room_image_4_path')->nullable()->after('room_image_3_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn([
                'room_image_1_path',
                'room_image_2_path',
                'room_image_3_path',
                'room_image_4_path',
            ]);
        });
    }
};

