<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_access_point_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_access_point_id')->constrained()->cascadeOnDelete();
            $table->string('host_name');
            $table->string('ip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_access_point_items');
    }
};
