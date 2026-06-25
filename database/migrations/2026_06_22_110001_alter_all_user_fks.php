<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to avoid issues with constraint names
        $fks = [
            ['ticket_comments', 'user_id', 'ticket_comments_user_id_foreign'],
            ['ticket_attachments', 'user_id', 'ticket_attachments_user_id_foreign'],
            ['ticket_approvals', 'approver_id', 'ticket_approvals_approver_id_foreign'],
            ['exports', 'user_id', 'exports_user_id_foreign'],
            ['imports', 'user_id', 'imports_user_id_foreign'],
            ['guides', 'created_by', 'guides_created_by_foreign'],
        ];

        foreach ($fks as [$table, $col, $fkName]) {
            $this->alterFK($table, $col, $fkName);
        }

        // Tickets - assigned_to and approved_by
        $this->alterFK('tickets', 'assigned_to', 'tickets_assigned_to_foreign');
        $this->alterFK('tickets', 'approved_by', 'tickets_approved_by_foreign');
    }

    private function alterFK(string $table, string $column, string $fkName): void
    {
        // Drop FK if it exists
        $exists = DB::selectOne("
            SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = 'pmu_db' AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?
        ", [$table, $fkName]);

        if ($exists) {
            DB::statement("ALTER TABLE `$table` DROP FOREIGN KEY `$fkName`");
        }

        // Make column nullable
        DB::statement("ALTER TABLE `$table` MODIFY `$column` BIGINT UNSIGNED NULL");

        // Add FK with SET NULL
        DB::statement("ALTER TABLE `$table` ADD CONSTRAINT `$fkName` FOREIGN KEY (`$column`) REFERENCES `users` (`id`) ON DELETE SET NULL");
    }

    public function down(): void
    {
        // Reverse all changes
        $fks = [
            ['ticket_comments', 'user_id', 'ticket_comments_user_id_foreign', false],
            ['ticket_attachments', 'user_id', 'ticket_attachments_user_id_foreign', false],
            ['ticket_approvals', 'approver_id', 'ticket_approvals_approver_id_foreign', false],
            ['exports', 'user_id', 'exports_user_id_foreign', false],
            ['imports', 'user_id', 'imports_user_id_foreign', false],
            ['guides', 'created_by', 'guides_created_by_foreign', false],
            ['tickets', 'assigned_to', 'tickets_assigned_to_foreign', true],
            ['tickets', 'approved_by', 'tickets_approved_by_foreign', true],
        ];

        foreach ($fks as [$table, $col, $fkName, $allowNull]) {
            DB::statement("ALTER TABLE `$table` DROP FOREIGN KEY `$fkName`");
            $nullable = $allowNull ? 'NULL' : 'NOT NULL';
            DB::statement("ALTER TABLE `$table` MODIFY `$col` BIGINT UNSIGNED $nullable");
            DB::statement("ALTER TABLE `$table` ADD CONSTRAINT `$fkName` FOREIGN KEY (`$col`) REFERENCES `users` (`id`)");
        }
    }
};
