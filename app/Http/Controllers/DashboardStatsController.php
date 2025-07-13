<?php

namespace Waterline\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Waterline\Repositories\Workflow\Interfaces\WorkflowRepositoryInterface;
use Workflow\Models\StoredWorkflow;
use Workflow\Models\StoredWorkflowException;

class DashboardStatsController extends Controller
{
    public function index(WorkflowRepositoryInterface $repository) {
        // Temporary mock data to verify frontend functionality
        // TODO: Replace with actual database queries once DB issues are resolved

        return response()->json([
            'flows' => 42,
            'flows_per_minute' => 0.7,
            'flows_past_hour' => 42,
            'exceptions_past_hour' => 3,
            'failed_flows_past_week' => 12,
            'max_wait_time_workflow' => 'example-workflow-1',
            'max_duration_workflow' => 'long-running-process',
            'max_exceptions_workflow' => 'problematic-workflow',
        ]);
    }
}
