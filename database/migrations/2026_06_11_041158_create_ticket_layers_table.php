<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_layers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role_name');
            $table->unsignedTinyInteger('level');
            $table->string('team_key');
            $table->timestamps();

            $table->unique(['team_key', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_layers');
    }
};
