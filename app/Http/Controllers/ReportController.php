<?php

namespace App\Http\Controllers;

use App\Services\HarvestReportService;
use App\Services\XlsxExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __invoke(Request $request, HarvestReportService $service)
    {
        $filters = $request->validate(['q' => 'nullable|string|max:150', 'start_date' => 'nullable|date', 'end_date' => 'nullable|date|after_or_equal:start_date', 'month' => 'nullable|integer|between:1,12', 'year' => 'nullable|integer|between:1900,2100', 'kwt_id' => 'nullable|integer', 'commodity_id' => 'nullable|integer', 'district' => 'nullable|string|max:100', 'village' => 'nullable|string|max:100', 'export' => 'nullable|in:xlsx']);
        $filters = array_filter($filters, fn ($v) => $v !== null && $v !== '');
        if (! isset($filters['start_date']) && (isset($filters['month']) || isset($filters['year']))) {
            $date = Carbon::create($filters['year'] ?? now()->year, $filters['month'] ?? now()->month, 1);
            $filters['start_date'] = $date->toDateString();
            $filters['end_date'] = $date->endOfMonth()->toDateString();
        }
        $report = $service->build($filters, $request->user());
        if ($request->export === 'xlsx') {
            return app(XlsxExportService::class)->download($report);
        }

        return view('admin.report', compact('report', 'filters'));
    }
}
