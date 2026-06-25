<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('main_category');
            $table->string('sub_category');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('needs_approval')->default(false);
            $table->string('assigned_team')->nullable();
            $table->timestamps();

            $table->unique(['type', 'main_category', 'sub_category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_categories');
    }
};
