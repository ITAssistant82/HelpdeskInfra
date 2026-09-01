<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_assets', function (Blueprint $table) {
            // Nullable only to preserve rows created before this field existed.
            // The Filament form and Excel import require it for all new data.
            $table->string('building')->nullable()->after('location');
            $table->index(['location', 'building', 'room']);
        });
    }

    public function down(): void
    {
        Schema::table('academic_assets', function (Blueprint $table) {
            $table->dropIndex(['location', 'building', 'room']);
            $table->dropColumn('building');
        });
    }
};
