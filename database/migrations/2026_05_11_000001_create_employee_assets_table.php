<?php

use App\Models\Employee;
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
        Schema::create('employee_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->string('asset_code')->unique();
            $table->string('asset_type');
            $table->string('brand');
            $table->string('model');
            $table->string('serial_number')->nullable()->unique();
            $table->string('condition');
            $table->date('assigned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_assets');
    }
};
