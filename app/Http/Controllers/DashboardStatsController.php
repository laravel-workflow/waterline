<?php

namespace Waterline\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Workflow\Models\StoredWorkflow;
use Workflow\Models\StoredWorkflowException;

class DashboardStatsController extends Controller
{
    public function index() {

        $flowsPastHour = config('workflows.stored_workflow_model', StoredWorkflow::class)::where('updated_at', '>=', now()->subHour())
            ->count();

        $exceptionsPastHour = config('workflows.stored_workflow_exception_model', StoredWorkflowException::class)::where('created_at', '>=', now()->subHour())
            ->count();

        $failedFlowsPastWeek = config('workflows.stored_workflow_model', StoredWorkflow::class)::where('status', 'failed')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        $maxWaitTimeWorkflow = config('workflows.stored_workflow_model', StoredWorkflow::class)::where('status', 'pending')
            ->orderBy('updated_at')
            ->first();

        $maxDurationWorkflow = config('workflows.stored_workflow_model', StoredWorkflow::class)::select('*')
            ->addSelect(DB::raw('TIMEDIFF(created_at, updated_at) as duration'))
            ->where('status', '!=', 'pending')
            ->orderBy('duration')
            ->first();

        $maxExceptionsWorkflow = config('workflows.stored_workflow_model', StoredWorkflow::class)::withCount('exceptions')
            ->orderByDesc('exceptions_count')
            ->orderByDesc('updated_at')
            ->first();

        return response()->json([
            'flows' => config('workflows.stored_workflow_model', StoredWorkflow::class)::count(),
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
