<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchPublicDomainsRequest;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PublicDomainsController extends Controller
{
    public function index(SearchPublicDomainsRequest $request): Response
    {
        $search = $request->validated('q') ?? '';
        $literal = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search);
        $pattern = "%{$literal}%";

        // Group request snapshots by the same caretaker name.
        $activeDomains = DB::table('domains')
            ->join('service_accounts', 'service_accounts.request_id', '=', 'domains.request_id')
            ->join('applicants', 'applicants.applicant_id', '=', 'service_accounts.applicant_id')
            ->where('service_accounts.status', 'active')
            ->where(function (Builder $query) {
                $query->whereNull('service_accounts.expire_date')
                    ->orWhereDate('service_accounts.expire_date', '>=', today()->toDateString());
            })
            ->whereRaw("TRIM(domains.domain_name) <> ''")
            ->selectRaw('LOWER(TRIM(domains.domain_name)) as domain_name, TRIM(applicants.full_name) as owner_name')
            ->selectRaw('LOWER(TRIM(applicants.full_name)) as owner_key')
            ->distinct();

        $directory = DB::query()->fromSub($activeDomains, 'directory');
        $totalDomains = (clone $directory)
            ->whereRaw("domain_name LIKE ? ESCAPE '!'", [$pattern])
            ->distinct()->count('domain_name');

        $groups = (clone $directory)
            ->select('owner_key')
            ->selectRaw("COALESCE(NULLIF(MAX(owner_name), ''), 'ไม่ระบุชื่อผู้ดูแล') as owner_name")
            ->selectRaw('COUNT(DISTINCT domain_name) as domain_count')
            ->selectRaw("COUNT(DISTINCT CASE WHEN domain_name LIKE ? ESCAPE '!' THEN domain_name END) as matching_count", [$pattern])
            ->groupBy('owner_key')
            ->havingRaw("COUNT(DISTINCT CASE WHEN domain_name LIKE ? ESCAPE '!' THEN domain_name END) > 0", [$pattern])
            ->orderBy('owner_name')
            ->orderBy('owner_key')
            ->paginate(10)
            ->appends(['q' => $search]);

        $rows = collect();
        if ($groups->isNotEmpty()) {
            $rows = (clone $directory)
                ->whereRaw("domain_name LIKE ? ESCAPE '!'", [$pattern])
                ->whereIn('owner_key', $groups->pluck('owner_key'))
                ->orderBy('domain_name')
                ->get();
        }

        // Only names, domain names and counts reach the public view.
        $groups->through(function (object $group) use ($rows) {
            $domains = $rows->where('owner_key', $group->owner_key)
                ->pluck('domain_name')->unique()->values();

            return (object) [
                'owner_name' => $group->owner_name,
                'domain_count' => (int) $group->domain_count,
                'matching_count' => (int) $group->matching_count,
                'domains' => $domains,
            ];
        });

        return response()
            ->view('domains.index', compact('groups', 'totalDomains', 'search'))
            ->header('Cache-Control', 'no-store, private');
    }
}
