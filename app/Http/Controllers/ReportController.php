<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Comment;
use App\Models\CreatorProfile;
use App\Models\Post;
use App\Models\Report;

class ReportController extends Controller
{
    public function store(StoreReportRequest $request)
    {
        $data = $request->validated();

        $map = [
            'post' => Post::class,
            'comment' => Comment::class,
            'creator' => CreatorProfile::class,
        ];

        $modelClass = $map[$data['reportable_type']];
        $reportable = $modelClass::findOrFail($data['reportable_id']);

        $report = new Report([
            'user_id' => $request->user()->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'status' => 'open',
        ]);

        $report->reportable()->associate($reportable);
        $report->save();

        return back()->with('success', 'Report submitted successfully.');
    }
}
