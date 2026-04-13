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
            $table->text('contact_info')->nullable()->after('generated_banner_path');
        });

        Schema::table('availability_banners', function (Blueprint $table) {
            $table->text('contact_info')->nullable()->after('generated_banner_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('contact_info');
        });

        Schema::table('availability_banners', function (Blueprint $table) {
            $table->dropColumn('contact_info');
        });
    }
};
