<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_access_point_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('ip');
            $table->date('eol_announcement')->nullable()->after('product_name');
            $table->date('end_of_sale')->nullable()->after('eol_announcement');
            $table->date('end_of_service_life')->nullable()->after('end_of_sale');
        });
    }

    public function down(): void
    {
        Schema::table('asset_access_point_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'eol_announcement', 'end_of_sale', 'end_of_service_life']);
        });
    }
};
