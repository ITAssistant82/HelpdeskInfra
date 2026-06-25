<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_layers', function (Blueprint $table) {
            $table->integer('escalation_hours')->nullable()->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_layers', function (Blueprint $table) {
            $table->dropColumn('escalation_hours');
        });
    }
};
