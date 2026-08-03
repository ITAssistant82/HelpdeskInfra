<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('domain_renewals', function (Blueprint $table) {
            $table->string('status')->nullable()->default(null)->change();
        });

        Schema::table('software_renewals', function (Blueprint $table) {
            $table->string('status')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('domain_renewals', function (Blueprint $table) {
            $table->string('status')->default('Active')->change();
        });

        Schema::table('software_renewals', function (Blueprint $table) {
            $table->string('status')->default('Active')->change();
        });
    }
};
