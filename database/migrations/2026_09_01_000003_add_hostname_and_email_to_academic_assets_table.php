<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_assets', function (Blueprint $table) {
            $table->string('hostname')->nullable()->after('asset_code');
            $table->string('email')->nullable()->after('hostname');

            $table->index('hostname');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::table('academic_assets', function (Blueprint $table) {
            $table->dropIndex(['hostname']);
            $table->dropIndex(['email']);
            $table->dropColumn(['hostname', 'email']);
        });
    }
};
