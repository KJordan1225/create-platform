<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportManagementController extends Controller
{
    public function index(): View
    {
        $reports = Report::query()
            ->with(['user', 'reportable'])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function resolve(Report $report): RedirectResponse
    {
        $report->update([
            'status' => 'resolved',
        ]);

        return back()->with('success', 'Report marked as resolved.');
    }

    public function dismiss(Report $report): RedirectResponse
    {
        $report->update([
            'status' => 'dismissed',
        ]);

        return back()->with('success', 'Report dismissed.');
    }
}
