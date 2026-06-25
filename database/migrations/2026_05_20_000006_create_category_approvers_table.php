<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('ticket_categories')->cascadeOnDelete();
            $table->string('role_name');
            $table->unsignedTinyInteger('sequence_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_approvers');
    }
};
