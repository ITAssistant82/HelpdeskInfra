<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('role_name');
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->string('status')->default('pending');
            $table->text('note')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->unsignedTinyInteger('sequence_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_approvals');
    }
};
