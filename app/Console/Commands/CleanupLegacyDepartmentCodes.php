<?php

namespace App\Console\Commands;

use App\Models\DepartmentCode;
use App\Models\Domain;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupLegacyDepartmentCodes extends Command
{
    protected $signature = 'legacy:cleanup-department-codes {--prefix=-leg : code prefix to treat as junk}';

    protected $description = 'department_codes should only hold real faculty/office codes. '
        . 'This moves any -legXXX code text into domains.department_other (free text) and deletes the fake code.';

    public function handle(): int
    {
        $prefix = $this->option('prefix');
        $junkCodes = DepartmentCode::where('code', 'like', $prefix . '%')->get();

        if ($junkCodes->isEmpty()) {
            $this->info('No junk department codes found.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($junkCodes) {
            foreach ($junkCodes as $code) {
                Domain::where('department_code', $code->code)->update([
                    'department_other' => $code->department_name,
                    'department_code' => null,
                ]);
                $code->delete();
            }
        });

        $this->info("Cleaned up {$junkCodes->count()} junk department codes.");
        return self::SUCCESS;
    }
}
