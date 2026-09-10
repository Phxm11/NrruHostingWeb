<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Public requests keep their own submitted details, without modifying prior applicants.
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropUnique(['staff_or_student_id', 'email']);
            $table->index('staff_or_student_id');
        });
    }

    public function down(): void
    {
        // Snapshots may repeat an ID and email. Restore from backup to return to the old schema.
        throw new RuntimeException('Applicant snapshots cannot be merged automatically. Restore a backup instead.');
    }
};
