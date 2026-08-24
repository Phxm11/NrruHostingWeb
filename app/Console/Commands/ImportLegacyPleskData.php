<?php

namespace App\Console\Commands;

use App\Models\Applicant;
use App\Models\Domain;
use App\Models\ServiceAccount;
use App\Models\ServiceRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportLegacyPleskData extends Command
{
    protected $signature = 'legacy:import-plesk
        {file=database/seeders/legacy_plesk_import.json : path to the cleaned JSON dataset}
        {--fresh : delete previously-imported legacy_import requests before re-importing}';

    protected $description = 'Import the old Plesk hosting spreadsheet as legacy_import requests, '
        . 'grouping one service_request per real account (not per domain).';

    public function handle(): int
    {
        $path = base_path($this->argument('file'));
        if (! file_exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($path), true);
        $applicantsIn = collect($data['applicants']);
        $accountsIn = collect($data['accounts']);

        if ($this->option('fresh')) {
            $this->warn('Removing previously-imported legacy_import requests...');
            ServiceRequest::where('source', 'legacy_import')->get()->each->delete();
        }

        DB::transaction(function () use ($applicantsIn, $accountsIn) {
            // 1) one applicant row per real person (deduped by name+email upstream)
            $applicantIdByKey = [];
            $seq = 1;
            foreach ($applicantsIn as $a) {
                // idempotent: match on the real person (name + email), not a synthetic id,
                // so re-running this command never creates duplicate applicants.
                $applicant = Applicant::firstOrCreate(
                    ['full_name' => $a['full_name'], 'email' => $a['email']],
                    [
                        'staff_or_student_id' => 'LEGACY-' . str_pad($seq, 4, '0', STR_PAD_LEFT),
                        'unit_name' => $a['unit_name'] ?: '-',
                        'affiliation' => $a['affiliation'] ?: '-',
                    ]
                );
                $applicantIdByKey[$a['key']] = $applicant->applicant_id;
                $seq++;
            }

            // 2) one service_request per real account (= per username), with all its domains attached
            $requestSeq = 1;
            $accountsSkippedNoUsername = 0;
            foreach ($accountsIn as $acc) {
                $applicantId = $applicantIdByKey[$acc['applicant_key']] ?? null;
                if (! $applicantId) {
                    continue;
                }

                // idempotent: skip accounts already imported in a previous run
                $alreadyImported = ! empty($acc['username'])
                    ? ServiceAccount::where('username', $acc['username'])->exists()
                    : ServiceRequest::where('source', 'legacy_import')
                        ->where('legacy_note', 'like', '%(' . ($acc['customer_name'] ?? '') . ')')
                        ->exists();
                if ($alreadyImported) {
                    continue;
                }

                $request = ServiceRequest::create([
                    'form_no' => 'LEGACY-ACC-' . str_pad($requestSeq, 4, '0', STR_PAD_LEFT),
                    'request_date' => $acc['earliest_date'] ?? now()->toDateString(),
                    'applicant_id' => $applicantId,
                    'purpose_type' => '1.3_internal_admin',
                    'project_start_date' => $acc['earliest_date'] ?? now()->toDateString(),
                    'project_end_date' => '2099-12-31',
                    'status' => 'approved',
                    'source' => 'legacy_import',
                    'legacy_note' => 'นำเข้าจากระบบ Plesk เดิม (' . ($acc['customer_name'] ?? $acc['username']) . ')',
                ]);
                $requestSeq++;

                foreach ($acc['domains'] as $d) {
                    Domain::create([
                        'request_id' => $request->request_id,
                        'domain_name' => $d['domain_name'],
                        'department_code' => $d['department_code'],
                        'department_other' => $d['department_other'],
                    ]);
                }

                if (! empty($acc['username'])) {
                    ServiceAccount::firstOrCreate(
                        ['username' => $acc['username']],
                        [
                            'request_id' => $request->request_id,
                            'applicant_id' => $applicantId,
                            // legacy accounts already exist in Plesk with an unknown password;
                            // this is a random placeholder that forces a reset, never a real credential.
                            // 'password' triggers ServiceAccount::setPasswordAttribute() -> hashes into password_hash.
                            'password' => Str::random(32),
                            'account_type' => 'control_panel',
                            'status' => $acc['status'] === 'active' ? 'active' : 'disabled',
                            'created_by' => 'legacy_import',
                            'expire_date' => null,
                        ]
                    );
                } else {
                    $accountsSkippedNoUsername++;
                }
            }

            $this->info("Imported {$applicantsIn->count()} applicants, {$accountsIn->count()} accounts.");
            if ($accountsSkippedNoUsername) {
                $this->warn("{$accountsSkippedNoUsername} account(s) had no username in the spreadsheet — domains were still imported, but no service_account/login row was created for them.");
            }
        });

        return self::SUCCESS;
    }
}
