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
        Schema::table('employee_assets', function (Blueprint $table) {
            $table->string('os')->nullable()->after('asset_type');
            $table->string('mainboard')->nullable()->after('os');
            $table->string('processor')->nullable()->after('mainboard');
            $table->decimal('memory_gb', 5, 2)->nullable()->after('processor');
            $table->decimal('hard_drive_gb', 8, 2)->nullable()->after('memory_gb');
            $table->string('monitor')->nullable()->after('hard_drive_gb');
            $table->year('tahun_pembelian')->nullable()->after('monitor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_assets', function (Blueprint $table) {
            $table->dropColumn([
                'os',
                'mainboard',
                'processor',
                'memory_gb',
                'hard_drive_gb',
                'monitor',
                'tahun_pembelian',
            ]);
        });
    }
};
