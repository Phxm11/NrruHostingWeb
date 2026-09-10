<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite is used for fresh test databases; their initial schema already includes staff.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE approvals MODIFY approver_level ENUM('staff','unit_head','computer_center_deputy_director','computer_center_director') NOT NULL");
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Staff approval history must be preserved. Restore a backup to downgrade.');
    }
};
