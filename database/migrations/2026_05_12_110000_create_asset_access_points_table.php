<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_access_points', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->string('nama_gedung');
            $table->string('lantai');
            $table->integer('jumlah')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_access_points');
    }
};
