<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_renewals', function (Blueprint $table) {
            $table->id();
            $table->string('domain');
            $table->date('registration_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('platform')->nullable();
            $table->string('status')->default('Active');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_renewals');
    }
};
