<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceReportRequest;
use App\Services\ExecutiveReport;
use Illuminate\Http\Response;

class ReportsController extends Controller
{
    public function index(ServiceReportRequest $request, ExecutiveReport $report): Response
    {
        $filters = $request->validated();
        $data = $report->generate($filters);
        $data['serviceRequests'] = $report->requests($filters)
            ->select(['request_id', 'form_no', 'request_date', 'applicant_id', 'service_type', 'status'])
            ->with('applicant:applicant_id,unit_name,affiliation')
            ->orderByDesc('request_date')
            ->orderByDesc('request_id')
            ->paginate(20)
            ->appends($filters);

        return response()
            ->view('admin.reports.index', $data)
            ->header('Cache-Control', 'private, no-store');
    }

    public function print(ServiceReportRequest $request, ExecutiveReport $report): Response
    {
        return response()
            ->view('admin.reports.print', $report->generate($request->validated()))
            ->header('Cache-Control', 'private, no-store');
    }
}
