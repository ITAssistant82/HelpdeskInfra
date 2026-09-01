<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('room_type');
            $table->string('asset_type');
            $table->string('brand');
            $table->string('model');
            $table->string('serial_number')->nullable()->unique();
            $table->string('location');
            $table->string('room');
            $table->string('condition');
            $table->string('status')->default('Digunakan');
            $table->string('os')->nullable();
            $table->string('processor')->nullable();
            $table->string('mainboard')->nullable();
            $table->decimal('memory_gb', 8, 2)->nullable();
            $table->unsignedInteger('hard_drive_gb')->nullable();
            $table->string('monitor')->nullable();
            $table->unsignedSmallInteger('tahun_pembelian')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['room_type', 'location']);
            $table->index(['location', 'room']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_assets');
    }
};
