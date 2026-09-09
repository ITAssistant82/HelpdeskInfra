<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->string('color')->default('gray')->after('name');
        });

        DB::table('ticket_types')->where('name', 'Incident')->update(['color' => 'danger']);
        DB::table('ticket_types')->where('name', 'Service Request')->update(['color' => 'warning']);
    }

    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
