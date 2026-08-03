<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_renewals', function (Blueprint $table) {
            $table->id();
            $table->string('software');
            $table->date('renewal_date')->nullable();
            $table->string('pricing')->nullable();
            $table->string('email_registered')->nullable();
            $table->string('pic')->nullable();
            $table->string('status')->default('Active');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_renewals');
    }
};
