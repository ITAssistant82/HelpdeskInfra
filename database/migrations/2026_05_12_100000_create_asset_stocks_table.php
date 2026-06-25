<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('asset_type');
            $table->string('brand');
            $table->string('model');
            $table->string('serial_number')->nullable()->unique();
            $table->string('condition')->default('Baik');
            $table->string('os')->nullable();
            $table->string('processor')->nullable();
            $table->string('mainboard')->nullable();
            $table->decimal('memory_gb', 5, 2)->nullable();
            $table->decimal('hard_drive_gb', 8, 2)->nullable();
            $table->string('monitor')->nullable();
            $table->year('tahun_pembelian')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_stocks');
    }
};
