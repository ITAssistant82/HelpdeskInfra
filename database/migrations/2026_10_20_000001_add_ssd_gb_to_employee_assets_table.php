<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_assets', function (Blueprint $table) {
            $table->decimal('ssd_gb', 8, 2)->nullable()->after('hard_drive_gb');
        });
    }

    public function down(): void
    {
        Schema::table('employee_assets', function (Blueprint $table) {
            $table->dropColumn('ssd_gb');
        });
    }
};
