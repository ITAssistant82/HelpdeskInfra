<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_license_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_renewal_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('slot_name')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('Active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['software_renewal_id', 'status']);
            $table->unique(['software_renewal_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_license_seats');
    }
};
