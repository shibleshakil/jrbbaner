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
            $table->string('contact_email')->nullable()->after('contact_info');
            $table->string('facebook_page')->nullable()->after('contact_email');
        });

        Schema::table('availability_banners', function (Blueprint $table) {
            $table->string('contact_email')->nullable()->after('contact_info');
            $table->string('facebook_page')->nullable()->after('contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('contact_email');
            $table->dropColumn('facebook_page');
        });

        Schema::table('availability_banners', function (Blueprint $table) {
            $table->dropColumn('contact_email');
            $table->dropColumn('facebook_page');
        });
    }
};
