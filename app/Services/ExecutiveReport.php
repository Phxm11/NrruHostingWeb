<?php

namespace App\Services;

use App\Models\ServiceRequest;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ExecutiveReport
{
    public const ServiceLabels = [
        'web_hosting' => 'Web Hosting',
        'virtual_server' => 'Virtual Server',
        '' => 'ไม่ระบุประเภท',
    ];

    public const StatusLabels = [
        'draft' => 'ฉบับร่าง',
        'submitted' => 'รอพิจารณา',
        'approved' => 'อนุมัติ',
        'rejected' => 'ไม่อนุมัติ',
        'expired' => 'หมดอายุ',
    ];

    public function requests(array $filters): Builder
    {
        return ServiceRequest::query()
            ->where('request_date', '>=', $filters['start_date'])
            ->where('request_date', '<', CarbonImmutable::parse($filters['end_date'])->addDay()->toDateString())
            ->when($filters['service_type'] ?? null, fn (Builder $query, string $type) => $query->where('service_type', $type));
    }

    public function generate(array $filters): array
    {
        $generatedAt = CarbonImmutable::now();
        $today = $generatedAt->toDateString();
        $expiringThrough = $generatedAt->addDays(30)->toDateString();
        $start = CarbonImmutable::parse($filters['start_date']);
        $end = CarbonImmutable::parse($filters['end_date']);
        $requests = $this->requests($filters);
        $statusCounts = (clone $requests)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $totalRequests = (int) $statusCounts->sum();
        $serviceCounts = (clone $requests)
            ->selectRaw('service_type, COUNT(*) as total')
            ->groupBy('service_type')
            ->pluck('total', 'service_type');

        $monthlyCounts = (clone $requests)
            ->selectRaw('SUBSTR(request_date, 1, 7) as month, COUNT(*) as total')
            ->groupByRaw('SUBSTR(request_date, 1, 7)')
            ->pluck('total', 'month');
        $months = collect();
        for ($month = $start->startOfMonth(); $month <= $end->startOfMonth(); $month = $month->addMonth()) {
            $months->push([
                'label' => $month->format('m/').($month->year + 543),
                'total' => (int) $monthlyCounts->get($month->format('Y-m'), 0),
            ]);
        }

        $departments = (clone $requests)
            ->leftJoin('applicants', 'applicants.applicant_id', '=', 'service_requests.applicant_id')
            ->select('applicants.unit_name', 'applicants.affiliation')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN service_requests.status = ? THEN 1 ELSE 0 END) as approved', ['approved'])
            ->selectRaw('SUM(CASE WHEN service_requests.status = ? THEN 1 ELSE 0 END) as submitted', ['submitted'])
            ->groupBy('applicants.unit_name', 'applicants.affiliation')
            ->orderByDesc('total')
            ->orderBy('applicants.unit_name')
            ->orderBy('applicants.affiliation')
            ->get();

        $domainCount = DB::table('domains')
            ->whereIn('request_id', (clone $requests)->select('service_requests.request_id'))
            ->count();

        // Account inventory is current; it is independent of the request-date range.
        $accounts = DB::table('service_accounts')
            ->join('service_requests', 'service_requests.request_id', '=', 'service_accounts.request_id')
            ->when($filters['service_type'] ?? null, fn ($query, string $type) => $query->where('service_requests.service_type', $type));
        $accountSummary = (clone $accounts)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN service_accounts.status = ? THEN 1 ELSE 0 END) as disabled', ['disabled'])
            ->selectRaw('SUM(CASE WHEN service_accounts.status != ? AND (service_accounts.status = ? OR expire_date < ?) THEN 1 ELSE 0 END) as expired', ['disabled', 'expired', $today])
            ->selectRaw('SUM(CASE WHEN service_accounts.status = ? AND (expire_date IS NULL OR expire_date >= ?) THEN 1 ELSE 0 END) as active', ['active', $today])
            ->selectRaw('SUM(CASE WHEN service_accounts.status = ? AND expire_date BETWEEN ? AND ? THEN 1 ELSE 0 END) as expiring', ['active', $today, $expiringThrough])
            ->selectRaw('SUM(CASE WHEN service_accounts.status = ? AND expire_date IS NULL THEN 1 ELSE 0 END) as no_expiry', ['active'])
            ->first();

        $renewals = DB::table('service_renewals')
            ->join('service_accounts', 'service_accounts.account_id', '=', 'service_renewals.account_id')
            ->join('service_requests', 'service_requests.request_id', '=', 'service_accounts.request_id')
            ->where('service_renewals.created_at', '>=', $start->startOfDay()->toDateTimeString())
            ->where('service_renewals.created_at', '<', $end->addDay()->startOfDay()->toDateTimeString())
            ->when($filters['service_type'] ?? null, fn ($query, string $type) => $query->where('service_requests.service_type', $type));
        $renewalCount = (clone $renewals)->count();
        $renewedAccountCount = (clone $renewals)->distinct()->count('service_renewals.account_id');

        return compact(
            'filters', 'generatedAt', 'expiringThrough', 'start', 'end', 'statusCounts',
            'totalRequests', 'serviceCounts', 'months', 'departments', 'domainCount',
            'accountSummary', 'renewalCount', 'renewedAccountCount',
        ) + [
            'serviceLabels' => self::ServiceLabels,
            'statusLabels' => self::StatusLabels,
        ];
    }
}
