<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `tickets` DROP FOREIGN KEY `tickets_requester_id_foreign`");
        DB::statement("ALTER TABLE `tickets` MODIFY `requester_id` BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE `tickets` ADD CONSTRAINT `tickets_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE SET NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `tickets` DROP FOREIGN KEY `tickets_requester_id_foreign`");
        DB::statement("ALTER TABLE `tickets` MODIFY `requester_id` BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE `tickets` ADD CONSTRAINT `tickets_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`)");
    }
};
