<?php

namespace Waterline\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Workflow\Models\StoredWorkflow;
use Workflow\Models\StoredWorkflowException;

class DashboardStatsController extends Controller
{
    public function index() {

        $flowsPastHour = StoredWorkflow::where('updated_at', '>=', now()->subHour())
            ->count();

        $exceptionsPastHour = StoredWorkflowException::where('created_at', '>=', now()->subHour())
            ->count();

        $failedFlowsPastWeek = StoredWorkflow::where('status', 'failed')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        $maxWaitTimeWorkflow = StoredWorkflow::where('status', 'pending')
            ->orderBy('updated_at')
            ->first();

        $maxDurationWorkflow = StoredWorkflow::select('*')
            ->addSelect(DB::raw('TIMEDIFF(created_at, updated_at) as duration'))
            ->where('status', '!=', 'pending')
            ->orderBy('duration')
            ->first();

        $maxExceptionsWorkflow = StoredWorkflow::withCount('exceptions')
            ->orderByDesc('exceptions_count')
            ->orderByDesc('updated_at')
            ->first();

        return response()->json([
            'flows' => StoredWorkflow::count(),
            'flows_per_minute' => $flowsPastHour / 60,
            'flows_past_hour' => $flowsPastHour,
            'exceptions_past_hour' => $exceptionsPastHour,
            'failed_flows_past_week' => $failedFlowsPastWeek,
            'max_wait_time_workflow' => $maxWaitTimeWorkflow,
            'max_duration_workflow' => $maxDurationWorkflow,
            'max_exceptions_workflow' => $maxExceptionsWorkflow,
        ]);
    }
}
