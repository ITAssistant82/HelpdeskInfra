<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('type');
            $table->foreignId('category_id')->constrained('ticket_categories');
            $table->string('title');
            $table->text('description');
            $table->string('location');

            $table->foreignId('requester_id')->constrained('users');
            $table->string('requester_unit')->nullable();

            $table->string('impact');
            $table->string('urgency')->nullable();
            $table->string('priority')->nullable();

            $table->string('status')->default('New');

            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->string('assigned_group')->nullable();

            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('sla_deadline')->nullable();
            $table->boolean('sla_achieved')->nullable();

            $table->timestamp('solved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('closure_note')->nullable();
            $table->text('resolution_note')->nullable();

            $table->boolean('needs_approval')->default(false);
            $table->unsignedTinyInteger('current_approver_sequence')->default(1);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('justification')->nullable();
            $table->date('due_date')->nullable();
            $table->string('cost_center')->nullable();
            $table->string('vendor_name')->nullable();

            $table->string('device_asset')->nullable();
            $table->string('application_service')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('type');
            $table->index('priority');
            $table->index('assigned_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
