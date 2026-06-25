<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_switches', function (Blueprint $table) {
            $table->id();

            $table->string('host_name');
            $table->string('ip');
            $table->string('network_device')->nullable();
            $table->string('stacking')->nullable();
            $table->string('snmp')->nullable();
            $table->string('brand');
            $table->string('type');
            $table->string('series')->nullable();
            $table->string('remote_type')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('location')->nullable();
            $table->string('tower')->nullable();
            $table->string('uplink_port')->nullable();
            $table->string('uplink_switch')->nullable();
            $table->string('downlink_port')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_switches');
    }
};
