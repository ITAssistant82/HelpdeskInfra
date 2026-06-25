<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->string('file_name')->nullable()->change();
            $table->unsignedBigInteger('file_size')->nullable()->change();
            $table->string('mime_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->string('file_name')->nullable(false)->change();
            $table->unsignedBigInteger('file_size')->nullable(false)->change();
            $table->string('mime_type')->nullable(false)->change();
        });
    }
};
